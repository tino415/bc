<?php 

namespace app\components;

use Yii;

class Crawler {
    protected $insertIgnore = [];

    public function table(){}

    public function prepare(){}

    public function attributes(){}

    public function callBacks(){}

    public function addIgnore($att_name, $value) {
        if(!array_key_exists($att_name, $this->insertIgnore)) {
            $this->insertIgnore[$att_name] = [];
        }
        $this->insertIgnore[$att_name][] = $value;
    }


    public function getText($node) {
        if($node instanceOf \DOMText)
            return $node->wholeText;
        elseif($node instanceOf \DOMAttr)
            return $node->value;
        else
            return $node->textContent;
    }

    public function parseAttributes($dom) {
        $values = [];
        foreach($this->attributes() as $name => $xpath_string) {
            $xpath = new \DOMXpath($dom);
            $nodeList = $xpath->query($xpath_string);
            echo "$name $nodeList->length\n";
            for($i = 0; $i < $nodeList->length; $i++) {
                $values[$name][] = $this->getText($nodeList->item($i));
            }
        }
        return $values;
    }

    public function processCallbacks($values) {
        foreach($this->callbacks() as $name => $function) {
            for($i=0; $i<count($values[$name]); $i++) {
                $values[$name][$i] = $function($values[$name][$i]);
            }
        }
        return $values;
    }

    public function generateRows($values) {
        $inserts = [];
        $att_names = array_keys($this->attributes());
        for($i=0; $i < count(end($values)); $i++) {
            $row = [];
            foreach($att_names as $att_name) {
                $is_ignored = array_key_exists($att_name, $this->insertIgnore);
                if($is_ignored && in_array(
                    $values[$att_name][$i],
                    $this->insertIgnore[$att_name]

                )) {
                    $row = false;
                    break;
                }
                else $row[] = $values[$att_name][$i];
            }
            if($row) $inserts[] = $row;
        }
        return $inserts;
    }

    public function save($inserts) {
        Yii::$app->db->createCommand()->batchInsert(
            $this->table(),
            array_keys($this->attributes()),
            $inserts
        )->execute();
    }

    public function runContent($contents, $runPrepare = true) {

        if($runPrepare) {
            echo "Preparing data\n";
            $this->prepare();
        }

        if(!is_array($contents)) $contents = [$contents];

        foreach($contents as $content) {
            echo "Parsing html\n";
            $dom = new \DOMDocument;
            @$dom->loadHtml($content);

            echo "Parsing attributes\n";
            $rawValues = $this->parseAttributes($dom);
            if(count($rawValues) == 0) {
                echo "Nothing found\n";
                return;
            }

            echo "Proccessing callbacks\n";
            $values = $this->processCallbacks($rawValues);
            
            echo "Generating inserts\n";
            $inserts = $this->generateRows($values);

            echo "Saving to database\n";
            if(count($inserts) > 0)
                $this->save($inserts);
        }
    }

    public function run($url, $runPrepare = true) {
        echo "Downloading page\n";
        $this->runContent(file_get_contents($url), $runPrepare);
    }

    public function runMultiple($urls, $runPrepare = true) {
        if($runPrepare) $this->prepare();
        foreach($urls as $url) {
            $this->run($url, false);
        }
    }
}
