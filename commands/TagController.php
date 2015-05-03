<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Tag;
use app\models\Document;
use app\models\Interpret;
use app\models\MapDocumentTag;
use app\components\Globals;

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

    private function generate() {
        echo "Generating tags from documents";
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach(Document::find()->with('interpret', 'type')->each() as $document) {
                $document->createTagsFromAtts();
                //$tag_names = $document->generateTags();
                //$this->createTags($tag_names);
                //$this->bindTagsToDocument($document, $tag_names);
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
                $tag_names = $document->generateTags();
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
        echo "Calculating weights\n";
        Tag::calculateWeights();
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

    public function actionLfm($id = false, $id_bigger = false) {
        if(!$id) $documents = Document::find()->all();
        elseif($id_bigger) $documents = Document::find()->where(
            "id > :id_bigger",
            [':id_bigger' => $id_bigger]
        )->all();
        else $documents = [Document::findOne($id)];

        foreach($documents as $document) {
            echo "Requesting $document->id $document->name :".$document->interpret->name."\n";
            $url = 
                "http://bcmusic.yweb.sk/web/document/loadtags?".
                "id=$document->id&".
                "api=true";

            $data = Globals::download($url);

            $json = json_decode($data, true);

            echo "Last.fm: ".$json['message']."\n";

            echo "Done\n";
        }
    }
}
