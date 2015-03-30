<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Document;
use app\models\Interpret;
use app\models\Tag;

define(
    'SONG_XPATH',
    '//table[@width=740]//td/node()[not(self::text()[not(normalize-space())])]'
);

class IndexerController extends Controller
{
    
    private $document = null;

    private $tags = [];

    private $interprets = [];

    private $TAG_MAPPING = [
        'texty' => ['text'],
        'melodie' => ['melodia'],
        'preklady' => ['preklad'],
        'akordy' => ['akordy', 'text'],
    ];

    private function escapeNodes($content) {
        $content = str_replace("<3", "&lt;3", $content);
        @$this->document->loadHTML($content);
        $xpath = new \DOMXPath($this->document);
        return $xpath->query(SONG_XPATH);
    }

    private function parseDocument($array) {
        extract($array);


        echo "Parsing document $name : $link\n";

        if(!Document::find()->where(['link' => $link])->exists()) {
            $document = new Document;
            $document->link = $link;
            $document->name = $name;
            $document->interpret_id = $this->parseInterpret($interpret);
            $document->save();
            foreach($this->parseTag($type) as $tag) {
                $document->link('tags', $tag);
            }
        }
    }

    private function parseInterpret($name) {
        if(!array_key_exists($name, $this->interprets)) {
            echo "New interpret $name\n";
            if(Interpret::find()->where(['name' => $name])->exists())
                $interpret = Interpret::find(['name' => $name])->one();
            else {
                $interpret = new Interpret;
                $interpret->name = $name;
                $interpret->save();
            }
            $this->interprets[$name] = $interpret->getPrimaryKey();
        }

        return $this->interprets[$name];
    }

    private function parseTag($name) {
        if(!array_key_exists($name, $this->tags)) {
            echo "New tag $name\n";
            if(array_key_exists($name, $this->TAG_MAPPING)) {
                $this->tags[$name] = [];
                foreach($this->TAG_MAPPING[$name] as $tag_name) {

                    echo Tag::find()->where(['name' => $tag_name])->exists()."\n";
                    if(!Tag::find()->where(['name' => $tag_name])->exists()) {
                        $tag = new Tag;
                        $tag->name = $tag_name;
                        $tag->save();
                    }

                    $tag = Tag::find()->where(['name' => $tag_name])->one();

                    $this->tags[$name][] = $tag;
                }
            } else {
                if(!Tag::find()->where(['name' => $name])->exists()) {
                    $tag = new Tag;
                    $tag->name = $name;
                    $tag->save();
                }
                $tag = Tag::find()->where(['name' => $name])->one();
                $this->tags[$name] = [$tag];
            }
        }

        return $this->tags[$name];
    }

    private function parse($url)
    {
        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        echo "[$time ]Parsing $url\n";
        $nodes = $this->escapeNodes(file_get_contents($url));
        for($i=0; $i<$nodes->length; $i += 4) {
            $this->parseDocument([
                'link' => $nodes->item($i+1)->getAttribute('href'),
                'name' => $nodes->item($i+1)->textContent,
                'type' => substr($nodes->item($i)->getAttribute('src'),7,-4),
                'interpret' => substr($nodes->item($i+2)->textContent,3),
            ]);

        }
    }

    public function actionIndex($start = 'A', $end = 'Č')
    {
        $this->document = new \DOMDocument;
        $base_link = 'http://www.supermusic.sk/piesne.php?od=';
        $capitals = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z', '*', 'Ž',
            'Ť', 'Č'
        ];

        $low_caps = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z'
        ];

        $capital_start = array_search($start, $capitals);
        $capital_end = array_search($end, $capitals);

        for($i=$capital_start; $i <= $capital_end;$i++){
            $capital = $capitals[$i];
            foreach($low_caps as $low) {
                $url = "$base_link$capital$low";
                try {
                    if("$capital$low" == 'Id') {
                        $this->testBi = true;
                    }
                    $this->parse($url);
                } catch(Exception $e) {
                    $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                    echo "[$time] Error catched: ".$e->getMessage()."\n";
                }
            }
        }

        return 0;
    }
}
