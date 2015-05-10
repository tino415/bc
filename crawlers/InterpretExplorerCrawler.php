<?php

namespace app\crawlers;

use yii\helpers\ArrayHelper;
use app\models\Interpret;
use app\components\Crawler;

class InterpretExplorerCrawler extends Crawler {

    private $interprets;

    protected function charset($string) {
        try { $res = iconv('widnows-1250', 'UTF-8//IGNORE', $string); }
        catch( \Exception $e) {
            ini_set('mbstring.substitute_character', "none"); 
            $res = mb_convert_encoding($string, 'UTF-8', 'UTF-8'); 
        }
        return $res;
    }

    public function table() {
        return Interpret::tableName();
    }

    public function prepare() {
        $interprets = Interpret::find()->all();
        $this->interpret_names = ArrayHelper::map($interprets, 'name', 'id');
        $this->interpret_ids = ArrayHelper::map($interprets, 'id', 'name');
    }

    public function attributes() {
        $base = '//table[@bgcolor="#333333" and position() = 2]//a';
        return [
            'id' => "$base/@id",
            'name' => "$base/@href",
            'alias' => "$base/text()",
        ];
    }

    public function callbacks() {
        return [
            'id' => function($id) {
                if(array_key_exists($id, $this->interpret_ids)) {
                    $this->addIgnore('id', $id);
                } else {
                    $this->interpret_ids[$id] = true;
                }
                return $id;
            },
            'name' => function($href){
                $parts = parse_url($href);
                preg_match('/&name=.*$/', $parts['query'], $name);
                $name = $this->charset(
                    urldecode(substr($name[0], 6))
                );

                if(array_key_exists($name, $this->interpret_names)) {
                    $this->addIgnore('name', $name);
                } else {
                    $this->interpret_names[$name] = true;
                }

                return $name;
            },
            'alias' => function($alias) {
                $alias = preg_replace('/ \([0-9]+\)$/','', $alias);
                return str_replace(',', '', $alias);
            }
        ];
    }
}
