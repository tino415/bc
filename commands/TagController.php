<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Tag;
use app\models\Document;
use app\models\Interpret;
use app\models\MapDocumentTag;

class TagController extends Controller {

    private $tags = [];

    private function escapeTags($string) {
        return preg_split('[ \(]', $string);
    }

    private function createTags($tags) {
        foreach($tags as $tag_name) {
            if(!array_key_exists($tag_name, $this->tags)) {
                $tag = new Tag;
                $tag->name = $tag_name;
                $tag->save();
                $tag->id = $tag->getPrimaryKey();
                $this->tags[$tag_name] = Tag::findOne(['name' => $tag_name]);
            }
        }
    }

    private function bindTagsToDocument($document, $tag_names) {
        foreach($tag_names as $tag_name) {
            $document->link('tags', $this->tags[$tag_name]);
        }
    }

    private function escapeDocumentTags($document) {
        return $this->escapeTags(
            $document->name.' '.
            $document->interpret->name.' '.
            $document->type->name
        );

    }

    private function generate() {
        echo "Generating tags from documents";
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach(Document::find()->with('interpret', 'type')->each() as $document) {
                $tag_names = $this->escapeDocumentTags($document);
                $this->createTags($tag_names);
                $this->bindTagsToDocument($document, $tag_names);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $transaction->commit();
        echo "................done\n";
    }


    public function actionIndex($from = 0, $to = 50) {
        $tags = Tag::find()->limit($from, $to)->all();
        printf("Returned tags: %d\n", count($tags));
        foreach($tags as $tag) {
            foreach($tag as $att_name => $att_value) {
                echo "$att_name: $att_value \t";
            }
            echo "\n";
        }
        return 0;
    }

    public function actionBind() {
        $this->actionUnbind();
        echo "Binding tags to documents";
        $this->tags = Tag::find()->indexBy('name')->all();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach(Document::find()->with('interpret', 'type')->each() as $document) {
                $tag_names = $this->escapeDocumentTags($document);
                $this->bindTagsToDocument($document, $tag_names);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $transaction->commit();
        echo "................done\n";
        return 0;
    }

    public function actionClear() {
        echo "Removing all tags";
        Tag::deleteAll('1=1');
        echo "................done\n";
        return 0;
    }

    public function actionUnbind() {
        echo "Removing all links between documents and tags";
        MapDocumentTag::deleteAll('1=1');
        echo "................done\n";
        return 0;
    }

    public function actionRegenerate() {
        $this->actionClear();
        $this->generate();
        return 0;
    }

    public function actionGenerate() {
        $this->tags = Tag::find()->indexBy('name')->all();
        $this->generate();
        return 0;
    }
}
