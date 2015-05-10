<?php

namespace app\crawlers;

use app\models\Document;
use app\models\Interpret;
use app\models\DocumentType;
use app\components\Crawler;
use yii\helpers\ArrayHelper;

class DocumentExplorerCrawler extends Crawler {

    private $TAG_MAPPING = [
        'texty' => 'text',
        'melodie' => 'melodia',
        'preklady' => 'preklad',
    ];

    public function table() {
        return Document::tableName();
    }

    public function prepare() {
        $interprets = Interpret::find()->all();
        $this->interpretNames = ArrayHelper::map($interprets, 'name', 'id');
        $this->interpretAlliases = ArrayHelper::map($interprets, 'alias', 'id');
        $this->documents = ArrayHelper::map(Document::find()->all(), 'id', 'name');
        $this->types = ArrayHelper::map(DocumentType::find()->all(), 'name', 'id');
    }

    public function attributes() {
        $base = '//table[@width=740]//td';
        return [
            'name' => "$base/a/text()",
            'id' => "$base/a/@href",
            'type_id' => "$base/img/@src",
            'interpret_id' => "$base/text()",
        ];
    }

    public function callbacks() {
        return [
            'interpret_id' => function($interpret) {
                $name = substr($interpret, 3);
                if(array_key_exists($name, $this->interpretNames)) {
                    return $this->interpretNames[$name];
                }
                if(array_key_exists($name, $this->interpretAlliases)) {
                    return $this->interpretAlliases[$name];
                }
                $this->addIgnore('interpret_id', $name);
                return $name;
            },
            'id' => function($id) {
                preg_match('/[0-9]+$/', $id, $matches);
                $id = $matches[0];
                if(array_key_exists($id, $this->documents)){
                    $this->addIgnore('id', $id);
                }
                return $id;
            },
            'type_id' => function($type) {
                if(empty($type)) {
                    return 100;
                }
                $type = basename($type, '.gif');
                if(array_key_exists($type, $this->TAG_MAPPING))
                    $type = $this->TAG_MAPPING[$type];
                if(!array_key_exists($type, $this->types)) {
                    $model = new DocumentType;
                    $model->name = $type;
                    $model->save();
                    $this->types[$type] = $model->getPrimaryKey();
                }
                return $this->types[$type];
            }
        ];
    }
}
