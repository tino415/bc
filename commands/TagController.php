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
        foreach(array_count_values($tag_names) as $tag => $count) {
            $map = new MapDocumentTag;
            $map->document_id = $document->id;
            $map->tag_id = $this->tags[$tag]->id;
            $map->count = $count;
            $map->save();
        }
    }

    private function escapeDocumentTags($document) {
        return Document::escapeTags(
            $document->name.' '.$document->interpret->name
        ) + [
            $document->type->name,
            mb_strtolower($document->name, 'UTF-8'),
            mb_strtolower($document->interpret->name, 'UTF-8'),
        ];

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
        echo "Selecting tags";
        $this->tags = Tag::find()->indexBy('name')->all();
        echo "................done\n";
        $this->generate();
        return 0;
    }

    public function actionWeight() {
        $transaction = Yii::$app->db->beginTransaction();
        echo "Calculating weights";
        try {
            Yii::$app->db->createCommand("
                UPDATE map_document_tag d
                INNER JOIN (
                    SELECT a.id, (
                        ((LOG(count) + 1)/ dtf1) * 
                        (b.uniq / (1 + 0.115*b.uniq)) * 
                        log(
                            (
                                (   
                                    SELECT COUNT(*) 
                                    FROM document
                                ) - 
                                (
                                    SELECT COUNT(*) 
                                    FROM map_document_tag 
                                    WHERE tag_id = a.tag_id
                                )
                            ) / (
                                SELECT COUNT(*) 
                                FROM map_document_tag 
                                WHERE tag_id = a.tag_id
                            )
                        )
                    ) AS weight
                    FROM map_document_tag AS a
                    INNER JOIN (
                        SELECT document_id, SUM(LOG(count) + 1) as dtf1, COUNT(*) AS uniq 
                        FROM map_document_tag
                        GROUP BY document_id
                    ) AS b ON b.document_id = a.document_id
                ) AS e ON e.id = d.id
                SET d.weight = e.weight
            ")->execute();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $transaction->commit();
        echo "................done\n";
        return 0;
    }

    public function actionAll() {
        $this->actionRegenerate();
        $this->actionWeight();
        return 0;
    }

    public function actionTest() {
        $tags = Tag::getProfileTags(false);
        print_r(Document::match($tags));
        
        return 0;
    }
}
