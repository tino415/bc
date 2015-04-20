<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Document;
use app\models\Action;
use app\models\ActionType;
use app\models\View;
use app\models\Tag;
use app\models\Schema;

class DocumentController extends Controller {

    private function getRecommendation() {
        
        if(Yii::$app->user->isGuest) $tags = Tag::getProfileTags(Yii::$app->user->id);
        else $tags = Tag::getProfileTags(Yii::$app->user->id);

        return Document::match($tags);
    }

    public function actionIndex($query = null) {
        return $this->render('index',[
            'phrase' => $query,
            'results' => (is_null($query)) ?
                $this->getRecommendation() : Document::search($query)
        ]);
    }

    public function actionView($id) {
        $document = Document::findOne($id);
        $DOM = new \DOMDocument;

        if(is_null($document->content)) {
            Yii::info('Caching');
            $content = file_get_contents(
                "http://www.supermusic.sk/skupina.php?action=piesen&idpiesne=$document->id"
            );
            @$DOM->loadHTML($content);
            $xpath = new \DOMXPath($DOM);

            foreach($document->tags as $tag) {
                $view = new View;
                $view->document_id = $document->id;
                $view->user_id = (Yii::$app->user->isGuest) ? 
                    Yii::$app->params['anonymousUserId'] : Yii::$app->user->id;
                $view->tag_id = $tag->id;
                $view->save();
            };

            $schemas = [];
            if($document->type->name == 'akordy') {
                $chordLinks = $xpath->query('//a[@class="sup"]');
                foreach($chordLinks as $chordLink) {
                    Yii::info("Chord link");
                    if(!array_key_exists($chordLink->textContent, $schemas)) {
                        $schema = new Schema;
                        $schema->content = $chordLink->textContent;
                        $schema->document_id = $document->id;
                        $schema->save();

                        $schemas[$chordLink->textContent] = $chordLink->textContent;
                    }

                    $chordLink->setAttribute(
                        'href',
                        "http://www.supermusic.sk/akord.php?akord=$chordLink->textContent"
                    );
                    $chordLink->setAttribute('target', '_blank');
                    $chordLink->setAttribute('class', 'chord');
                }
            }

            $document->content = $DOM->saveHTML(
                $xpath->query('//td[@class="piesen"]')->item(0)
            );
            $document->save();
        }

        return $this->render('view', [
            'document' => $document,
            'recommendations' => $this->getRecommendation()
        ]);

    }
}
