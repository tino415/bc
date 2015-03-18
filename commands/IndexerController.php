<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Document;
use app\models\Type;
use app\models\Interpret;

define(
    'SONG_XPATH',
    '//table[@width=740]//td/node()[not(self::text()[not(normalize-space())])]'
);

class IndexerController extends Controller
{
    
    private $document = null;

    private $types = [];

    private $interprets = [];

    private $testBi = false;

    private $T_MAPPING = [
        'texty' => 'text',
        'melodie' => 'melodia',
        'preklady' => 'preklad',
    ];

    private function parse($url)
    {
        $time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        echo "[$time ]Parsing $url\n";
        $content = file_get_contents($url);
        $content = str_replace("<3", "&lt;3", $content);
        @$this->document->loadHTML($content);
        $xpath = new \DOMXPath($this->document);
        $nodes = $xpath->query(SONG_XPATH);
        for($i=0; $i<$nodes->length; $i += 4) {
            if($this->testBi) {
                echo "Image:".$this->document->saveHTML($nodes->item($i))."\n";
                echo "Link:".$this->document->saveHTML($nodes->item($i+1))."\n";
                echo "Author:".$this->document->saveHTML($nodes->item($i+2))."\n";
                echo "Br:".$this->document->saveHTML($nodes->item($i+3))."\n";
                echo "<--- END PARAMS --->\n";
            }
            $link = $nodes->item($i+1)->getAttribute('href');
            if(!Document::find()->where(['link' => $link])->exists()) {
                $name = $nodes->item($i+1)->textContent;
                $tname = substr($nodes->item($i)->getAttribute('src'),7,-4);
                $iname = substr($nodes->item($i+2)->textContent,3);
                echo "Proceeding name : $name, type : $tname, interpret : $iname\n";

                if(!(empty($tname)||empty($iname)||empty($name))) {
                    $document = new Document;
                    $document->name = $name;

                    if(in_array($tname, $this->T_MAPPING)) {
                        $tname = $this->T_MAPPING[$name];
                    }

                    if(!array_key_exists($tname, $this->types)) {
                        $type = Type::find()->where(['name' => $tname]);
                        if(!$type->exists()) {
                            echo "New type found $tname\n";
                            $type = new Type;
                            $type->name = $tname;
                            $type->save();
                        } else {
                            echo "Caching Type $tname\n";
                            $type = $type->one();
                        }
                        $this->types[$tname] = $type->getPrimaryKey();
                    }
                    $document->type_id = $this->types[$tname];

                    $iname = substr($nodes->item($i+2)->textContent,3);
                    if(!array_key_exists($iname, $this->interprets)) {
                        $interpret = Interpret::find()->where(['name' => $iname]);
                        if(!$interpret->exists()) {
                            echo "New interpret found $iname\n";
                            $interpret = new Interpret;
                            $interpret->name = $iname;
                            $interpret->save();
                        } else {
                            $interpret = $interpret->one();
                        }
                        $this->interprets[$iname] = $interpret->getPrimaryKey();
                    }
                    $document->interpret_id = $this->interprets[$iname];

                    $document->link = $link;
                    $document->save();
                } else {
                    echo "Empty parameter found\n";
                }
            }
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
