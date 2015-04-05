<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Document;
use app\models\DocumentType;
use app\models\Interpret;
use app\models\Tag;
use app\models\TagType;

define(
    'SONG_XPATH',
    '//table[@width=740]//td/node()[not(self::text()[not(normalize-space())])]'
);

define(
    'INTERPRET_XPATH',
    '//td[a/@class="interpretzoznam"]/a'
);

class IndexerController extends Controller
{
    
    private $document = null;

    private $documentTypes = [];

    private $TAG_MAPPING = [
        'texty' => 'text',
        'melodie' => 'melodia',
        'preklady' => 'preklad',
    ];

    private function escapeNodes($content, $x_path) {
        $content = str_replace("<3", "&lt;3", $content);
        @$this->document->loadHTML($content);
        $xpath = new \DOMXPath($this->document);
        return $xpath->query($x_path);
    }

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


    public function actionDocuments(
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

    private function parsePageI($url) {
        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        echo "[$time ] SMI Parsing $url\n";
        $nodes = $this->escapeNodes(file_get_contents($url), INTERPRET_XPATH);

        foreach($nodes as $node) {
            $parts = parse_url($node->getAttribute('href'));
            preg_match('/&name=.*$/', $parts['query'], $name);
            $name = substr($name[0], 6);
            preg_match('/idskupiny=[0-9]+&/', $parts['query'], $id);
            $id = substr($id[0], 10, -1);
            $alias = preg_replace('/ \([0-9]+\)$/','', $node->textContent);
            $alias = str_replace(',', '', $alias);

            if(!Interpret::find()->where(['id' => $id])->exists()) {
                echo "Adding interpret $id : $name : $alias\n";
                $interpret = new Interpret;
                $interpret->id = $id;
                $interpret->name = $name;
                $interpret->alias = $alias;
                try {
                    $interpret->save();
                } catch (Exception $e) {
                    $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                    echo "[$time] Inserting Error: ".$e->getMessage()."\n";
                }
            } else {
                echo "Already in database $id : $name\n";
            }
        }
    }

    private function parseSMI(
        $main = false,
        $base_link = 'http://www.supermusic.sk/skupiny.php?od='
    )
    {
        $chars = [
            false, 'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z',
        ];

        $main = ($main) ? $main : $chars;

        $this->document = new \DOMDocument;

        // foreach created one interator, when iterate throught 
        // same set with multiple foreach, all will have same interator
        // aka. foreach($chars) {foreach($chars){}} -> AA, BB, CC, DD...

        foreach($main as $mchar) {
            for($x = 0; $x < count($chars); $x++) {
                for($y = 0; $y < count($chars); $y += 5) {
                    
                    $url = $base_link.$mchar;
                    $url .= ($chars[$x]) ? $chars[$x] : '';
                    $url .= ($chars[$y]) ? $chars[$y] : '';
                    echo "$url\nENRL\n";

                    try {
                        $this->parsePageI($url);
                    } catch(Exception $e) {
                        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                        echo "[$time] Error catched: ".$e->getMessage()."\n";
                    }
                }
            }
        }
    }

    public function actionOneint($char1, $char2, $char3) {
        $base_link = 'http://www.supermusic.sk/skupiny.php?od=';
        $this->document = new \DOMDocument;
        $url = $base_link.$char1.$char2.$char3;
        try {
            $this->parsePageI($url);
        } catch(Exception $e) {
            $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            echo "[$time] Error catched: ".$e->getMessage()."\n";
        }

    }

    public function actionInterprets(
        $capitals = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,*,Ž,Ť,Č'
    )
    {
        $capitals = explode(',', $capitals);
        $pids = [];
        foreach($capitals as $capital) {
            $pid = pcntl_fork();
            if($pid) {
                echo "Starting ".$pid." with capital $capital\n";
                $pids[] = $pid;
            } else {
                $this->parseSMI([$capital]);
                return 0;
            }
        }

        foreach($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        return 0;
    }

    private function createTags($document_id, $tags, $type) {
        foreach($tags as $tag_name => $tag_count) {
            if(!Tag::find()->where([
                'document_id' => $document_id,
                'name' => $tag_name,
                'type_id' => $type
            ])->exists()) 
            {
                echo "Adding tag $tag_name, $tag_count\n";
                $tag = new Tag;
                $tag->name = $tag_name;
                $tag->document_id = $document_id;
                $tag->count = $tag_count;
                $tag->type_id = $type;
                $tag->save();
            } else echo "Tag $tag_name exists for $document_id and $type\n";
        }
    }

    public function actionIndex() {
        define('PATTERN', '/[ ,]+/');
        $documents = Document::find()->all();
        $count = count($documents);

        foreach($documents as $document) {
            echo "Indexing rest:$count $document->id : $document->name \n";
            $count--;
            $tags = [];
            foreach(preg_split(PATTERN, $document->name) as $tag) {
                echo "Original tag $tag\n";
                $tag = mb_strtolower($tag, 'UTF-8');
                echo "To lower tag $tag\n";
                if(!array_key_exists($tag, $tags)) $tags[$tag] = 0;
                $tags[$tag]++;
            }
            $this->createTags($document->id, $tags, TagType::NAME);


            $tags = [];
            foreach(preg_split(PATTERN, $document->interpret->name) as $tag) {
                $tag = mb_strtolower($tag, 'UTF-8');
                if(!array_key_exists($tag, $tags)) $tags[$tag] = 0;
                $tags[$tag]++;
            }
            $this->createTags($document->id, $tags, TagType::INTERPRET);

            if($document->type->name == 'akordy') $tags = ['akordy' => 1, 'text' => 1];
            else $tags = [$document->type->name => 1];
            $this->createTags($document->id, $tags, TagType::OTHER);
        }
    }
}
