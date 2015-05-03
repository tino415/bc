<?php

namespace app\commands;

use Yii;
use app\components\SMParserController;
use app\models\Document;
use app\models\DocumentType;
use app\models\Interpret;
use app\models\Schema;
use app\models\Tag;
use app\models\MapDocumentTag;
use yii\base\ErrorException;

define(
    'SONG_XPATH',
    '//table[@width=740]//td/node()[not(self::text()[not(normalize-space())])]'
);

class DocumentController extends SMParserController {
    
    private $_tag_cache = false;

    const TAG_TYPE_UNKNOWN = 1;
    const TAG_TYPE_NAME = 2;
    const TAG_TYPE_INTERPRET_NAME = 3;
    const TAG_TYPE_FULL_NAME = 4;
    const TAG_TYPE_FULL_INTERPRET_NAME = 5;
    const TAG_TYPE_LABEL = 6;

    private $TAG_MAPPING = [
        'texty' => 'text',
        'melodie' => 'melodia',
        'preklady' => 'preklad',
    ];

    private function parseDocument($array) {
        extract($array);

        echo "Parsing document $name : $id\n";

        if(!Document::find()->where(['id' => $id])->exists()) {
            $document = new Document;
            $document->id = $id;
            $document->name = $name;
            if($interpret) $document->interpret_id = Interpret::find()
                ->where(['or', ['name' => $interpret], ['alias' => $interpret]])
                ->one()->id;
            else echo "Empty interpret\n";
            if($type) $document->type_id = $this->getDocumentType($type);
            else echo "No tags\n";
            try {
                $document->save();
            } catch(Exception $e) {
                $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                echo "[$time] Insert error: ".$e->getMessage()."\n";
            }
        }
    }

    private function getDocumentType($name) {
        if($name && !array_key_exists($name, $this->documentTypes)) {
            echo "New tag $name\n";

            if(array_key_exists($name, $this->TAG_MAPPING)) $name = $this->TAG_MAPPING[$name];

            if(!DocumentType::find()->where(['name' => $name])->exists()) {
                $tag = new DocumentType;
                $tag->name = $name;
                $tag->save();
            }

            $tag = DocumentType::find()->where(['name' => $name])->one();

            $this->documentTypes[$name] = $tag->id;
        } elseif(!$name) {
            return false;
        }

        return $this->documentTypes[$name];
    }

    private function parsePage($url) {
        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        echo "[$time ]Parsing $url\n";
        $nodes = $this->escapeNodes(file_get_contents($url), SONG_XPATH);


        for($i=0; $i<$nodes->length; $i += 4) {
            preg_match('/[0-9]+$/', $nodes->item($i+1)->getAttribute('href'), $matches);
            $this->parseDocument([
                'id' => $matches[0],
                'name' => $nodes->item($i+1)->textContent,
                'type' => substr($nodes->item($i)->getAttribute('src'),7,-4),
                'interpret' => substr($nodes->item($i+2)->textContent,3),
            ]);

        }
    }

    private function printErrors($model) {
        foreach($model->errors as $attribute => $att_error) {
            foreach($att_error as $err_message)
                echo "[$attribute] $err_message\n";
        }
    }

    private function loadTags($document) {

        if(!$this->_tag_cache) {
            $exists_tags = $document->getTags()->indexBy('name')->all();
        } else $exists_tags = $this->_tag_cache;

        $artist = urlencode($document->interpret->name);
        $track = urlencode($document->name);

        $apy_key = Yii::$app->params['last_fm_api_key'];

        $url = 
            "http://ws.audioscrobbler.com/2.0/".
            "?method=track.getTopTags".
            "&api_key=$apy_key&".
            "artist=$artist&".
            "track=$track&".
            "format=json";

        $data = file_get_contents($url);
        $json = json_decode($data, true);
        $tags = [];
        $message = 'Tags updated';

        if(array_key_exists('error', $json)) {
            echo 'Error: '. $json['message']."\n";
            return false;
        }

        $count = 0;

        if(array_key_exists('toptags', $json)) {

            if(array_key_exists('tag', $json['toptags'])) {

                if(!array_key_exists('name', $json['toptags']['tag']))
                    $tags = $json['toptags']['tag'];
                else $tags = [$json['toptags']['tag']];


                foreach($tags as $tag) {
                    if(!array_key_exists($tag['name'], $exists_tags)) {
                        echo "Inserting tag".$tag['name']." \n";
                        $newtag = new Tag;
                        $newtag->name = $tag['name'];
                        $newtag->save();
                        $newtag->id = $newtag->getPrimaryKey();
                        $exists_tags[$tag['name']] = $newtag;
                    }

                    $map = new MapDocumentTag;
                    $map->document_id = $document->id;
                    $map->tag_id = $exists_tags[$tag['name']]->id;
                    $map->count = ($tag['count'] > 50) ? 2 : 1;
                    if($map->validate()) $map->save();

                    if(count($map->errors) > 0) {
                        $this->printErrors($map);
                    } elseif($map->getPrimaryKey()) $count++;
                    else throw ErrorException("Tag: No error catched but not saved\n");
                }
            }
        }

        echo 'Found '.count($tags)." tags\n";
        echo "Added $count tags\n";
    }

    private function loadContent($document) {
        $this->document = ($this->document) ? $this->document : new \DOMDocument;

        $url = "http://www.supermusic.sk/skupina.php?action=piesen&idpiesne=$document->id";

        try {
            $content = file_get_contents($url);
        } catch(ErrorException $e) {
            echo "Failde download: $e\n";
            return;
        }

        @$this->document->loadHTML($content);
        $xpath = new \DOMXPath($this->document);

        $schemas = [];
        if($document->type->name == 'akordy') {
            $chordLinks = $xpath->query('//a[@class="sup"]');
            foreach($chordLinks as $chordLink) {
                if(!array_key_exists($chordLink->textContent, $schemas)) {
                    $schema = new Schema;
                    $schema->content = $chordLink->textContent;
                    $schema->document_id = $document->id;
                    $schema->save();
                        $schemas[$chordLink->textContent] = $chordLink->textContent;
                }

                $chordLink->setAttribute(
                    'href',
                    "http://www.supermusic.sk/akord.php?akord=$chordLink->textContent"
                );
                $chordLink->setAttribute('target', '_blank');
                $chordLink->setAttribute('class', 'chord');
            }
        }

        $content = $xpath->query('//td[@class="piesen"]')->item(0);

        if(empty($content)) {
            Yii::info('Page vythout "pisesn" class, document will by deleted'."\n");
            $document->delete();
        } else {
            $document->content = $this->document->saveHTML($content);
            $document->save();
        }
    }

    private function parallelDocuments($processes, $function, $where = '1=1') {
        $document_count = Document::find()->where($where)->count();
        $batch_size = floor($document_count / $processes);

        Yii::$app->db->close();

        $pids = [];

        for($i=0; $i<$processes; $i++) {
            $pid = pcntl_fork();

            $offset = $i*$batch_size;
            $limit = $i*$batch_size + $batch_size;

            if($pid) {
                echo "Starting $pid with slice [$offset, $limit]\n";
                $pids[] = $pid;
            } else {

                $documents = Document::find()->limit($limit)->offset($offset);
                foreach($documents->each() as $document) {
                    echo "Working with $document->id\n";
                    $function($document);
                    echo "Document $document->id done\n";
                }

            }

        }

        foreach($pids as $pid) pcntl_waitpid($pid, $status);

        return 0;
    }

    private function parseSM(
        array $capitals = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z', '*', 'Ž',
            'Ť', 'Č'
        ],
        array $lowcaps = [
            false, 'a', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z'
        ],
        $base_link = 'http://www.supermusic.sk/piesne.php?od='
    )
    {
        $this->document = new \DOMDocument;
        foreach($capitals as $capital) {
            for($x = 0; $x < count($lowcaps); $x++) {
                for($y = 0; $y < count($lowcaps); $y += 6) {
                    $url = $base_link.$capital;
                    $url .= ($lowcaps[$x]) ? $lowcaps[$x] : '';
                    $url .= ($lowcaps[$y]) ? $lowcaps[$y] : '';

                    try {
                        $this->parsePage($url);
                    } catch(Exception $e) {
                        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                        echo "[$time] Error catched: ".$e->getMessage()."\n";
                    }
                }
            }
        }
    }

    public function actionExplore(
        $capitals = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,*,Ž,Ť,Č'
    )
    {
        $capitals = explode(',', $capitals);
        
        $pids = [];
        foreach($capitals as $capital) {
            $pid = pcntl_fork();
            if($pid) {
                echo "Starting ".end($pids)." with capital $capital\n";
                $pids[] = $pid;
            } else {
                $this->parseSM([$capital]);
                return 0;
            }
        }

        foreach($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        return 0;
    }
    
    public function actionLoad($id) {
        $document = Document::findOne($id);
        echo "Loading tags\n";
        $this->loadTags($document);
        echo "Loading content\n";
        $this->loadContent($document);
        echo "Done\n";
        return 0;
    }

    public function actionLunloaded() {
        echo "Retrieving documents without content\n";
        $documents = Document::find()->where(['content' => null])->all();

        echo "Retruving tags\n";
        $this->_tag_cache = Tag::find()->indexBy('name')->all();

        foreach($documents as $document) {
            echo "Working on $document->id, $document->name\n";
            echo "Loading tags\n";
            $this->loadTags($document);
            echo "Loading content\n";
            $this->loadContent($document);
            echo "Done $document->id, $document->name\n";
        }

        echo "Done";
        return 0;
    }

    public function actionParallelunloaded($processes = 4) {
        echo "Retrieving tags\n";
        $this->_tag_cache = Tag::find()->indexBy('name')->all();

        $this->parallelDocuments($processes, function($document) {
            echo "Loading tags\n";
            $this->loadTags($document);
            echo "Loading content\n";
            $this->loadContent($document);
        }, ['content' => null]);
        echo "Done";
        return 0;
    }

    public function actionLoaded() {
        echo "Counting\n";
        $count = Yii::$app->db->createCommand("
            SELECT 
                CAST(
                    (
                        CAST(
                            (
                                SELECT COUNT(*)
                                FROM document
                                WHERE content IS NOT NULL
                            ) AS float8
                        ) / CAST(
                            (
                                SELECT COUNT(*)
                                FROM document
                            ) AS float8
                        ) * 100
                    ) AS decimal(4,2)
                )
                AS loaded
        ")->queryOne()['loaded'];
        echo "Documents are loaded at $count %\n";
    }

    public function actionParalleltaglfm($processes = 4) {
        echo "Retrieving tags\n";
        $this->_tag_cache = Tag::find()->indexBy('name')->all();

        $this->parallelDocuments($processes, function($document) {
            echo "Loading tags\n";
            $this->loadTags($document);
        });

        foreach($pids as $pid) pcntl_waitpid($pid, $status);

        echo "Done";
        return 0;
    }

    //TODO rebuild
    public function typeMap($map) {
        echo "$map->tag, ".$map->document->name.', '.$map->document->interpret->name."\n";

        $map->type_id = Document::getTagType(
            ($map->tag == mb_strtolower(($map->document->interpret->name), 'UTF-8')),
            ($map->tag == mb_strtolower(($map->document->name), 'UTF-8')),
            (array_key_exists("$map->tag", $map->document->interpret->nameTags)),
            (array_key_exists("$map->tag", $map->document->nameTags))
        );

        echo "$map->type_id \n";
        return;

        $map->save();
    }

    public function actionTagtype($id) {
        $document = Document::findOne($id);
        if(!$document) exit("Unknown document $id\n");
        foreach($document->getMapDocumentTags()->each() as $map) {
            $this->typeMap($map, $document);
        }
        return 0;
    }

    public function actionTagtypes() {
        foreach(Document::find()->each() as $document) {
            echo "Working with $document->id\n";
            foreach($document->getTags()->each() as $tag) {
                $this->typeTags($tag, $document);
            }
            echo "Document $document->id done\n";
        }
        echo "Done\n";
        return 0;
    }


    public function actionParalleltagtypes($processes = 4) {
        $this->parallelDocuments($processes, function($document) {
            foreach($document->getTags()->each() as $tag) {
                $this->typeTags($tag, $document);
            }
        });
        return 0;
    }

    public function actionParallelnametags($processes = 4) {
        $this->parallelDocuments($processes, function($document) {
            foreach([$document->name, $document->interpret->name] as $name) {
                $tag = new Tag;
                $tag->name = mb_strtolower($name, 'UTF-8');
                if($tag->validate()) $tag->save();
                else $tag = Tag::find()->where(['name' => $tag->name])->one();

                $map = new MapDocumentTag;
                $map->document_id = $document->id;
                $map->tag_id = $tag->id;
                if($map->validate()) $map->save();
            }
        });
        return 0;
    }

    public function actionList() {
        foreach(get_class_methods(self::className()) as $method) {
            $action = substr($method, 0, 6);
            if($action == 'action') {
                echo substr($method, 6)."\n";
            }
        }
    }
}
