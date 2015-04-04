<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Document;
use app\models\Action;
use app\models\ActionType;

class DocumentController extends Controller {

    public function actionIndex() {
        Yii::info("Start cycling documents");
        $docs = Document::find()->all();
        foreach($docs as $doc) {
            2 + 3;
        }
        Yii::info("end cycling documents");

        $doc = new Document;
        $doc->load([
            'id' => '666',
            'name' => 'Satan song',
            'link' => 'www.google.sk',
            'interpret_id' => '666',
        ]);

        return $this->render('view', [
            'model' => $doc,
        ]);

    }

    public function actionSearch() {
        return $this->render('search');
    }

    public function actionView($id) {
        $document = Document::findOne($id);


        return $this->render('view', [
            'model' => $document
        ]);
    }

    public function actionRview($id) {
        $document = Document::findOne($id);
        $DOM = new \DOMDocument;
        $content = file_get_contents($document->link);
        @$DOM->loadHTML($content);
        $xpath = new \DOMXPath($DOM);

        $action = new Action;
        $action->type_id = ActionType::DISPLAY_ID;
        $action->document_id = $document->id;
        if(Yii::$app->user->isGuest)
            $action->user_id = Yii::$app->params['anonymousUserId'];
        else $action->user_id = Yii::$app->user->id;
        $action->save();

        $schemas = [];
        if(in_array('akordy', $document->type)) {
            $chordLinks = $xpath->query('//a[@class="sup"]');
            foreach($chordLinks as $chordLink) {
                if(!array_key_exists($chordLink->textContent, $schemas)) 
                    $schemas[$chordLink->textContent] = $chordLink->textContent;

                $chordLink->setAttribute(
                    'href',
                    "http://www.supermusic.sk/akord.php?akord=$chordLink->textContent"
                );
                $chordLink->setAttribute('target', '_blank');
            }
        }

        $content = $DOM->saveHTML($xpath->query('//td[@class="piesen"]')->item(0));


        return $this->render('rview', [
            'document' => $document,
            'content' => $content,
            'schemas' => $schemas,
        ]);

    }
}
