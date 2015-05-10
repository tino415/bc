<?php

namespace app\commands;

use Yii;
use app\components\SMParserController;
use app\models\Interpret;

define(
    'INTERPRET_XPATH',
    '//td[a/@class="interpretzoznam"]/a'
);

class InterpretController extends SMParserController {
    
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

    public function actionExplore(
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
}
