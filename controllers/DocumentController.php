<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Document;
use app\models\Action;
use app\models\ActionType;

class DocumentController extends Controller {

    public function actionIndex($query = '') {
        return $this->render('results',[
            'phrase' => $query,
            'results' => Document::indexedSearch($query),
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
        $content = file_get_contents(
            "http://www.supermusic.sk/skupina.php?action=piesen&idpiesne=$document->id"
        );
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
        if($document->type == 'akordy') {
            $chordLinks = $xpath->query('//a[@class="sup"]');
            foreach($chordLinks as $chordLink) {
                if(!array_key_exists($chordLink->textContent, $schemas)) 
                    $schemas[$chordLink->textContent] = $chordLink->textContent;

                $chordLink->setAttribute(
                    'href',
                    "http://www.supermusic.sk/akord.php?akord=$chordLink->textContent"
                );
                $chordLink->setAttribute('target', '_blank');
                $chordLink->setAttribute('class', 'chord');
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