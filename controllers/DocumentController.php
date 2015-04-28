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
use app\models\Session;
use app\models\User;
use app\models\MapDocumentTag;

class DocumentController extends Controller {

    private function getRecommendation($exclude = false) {
        
        if(Yii::$app->user->isGuest)
            $tags = User::findOne(Yii::$app->params['anonymousUserId'])->recommendTags;
        else $tags = User::findOne(Yii::$app->user->id)->recommendTags;

        return Document::match($tags, $exclude);
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

        $session = Session::getSession();
        if($session) $session->renev();
        else $session = Session::create();

        if(is_null($document->content)) {
            $url = "http://www.supermusic.sk/skupina.php?action=piesen&idpiesne=$document->id";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            exit("$url [$content]");

            @$DOM->loadHTML($content);
            $xpath = new \DOMXPath($DOM);

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

        foreach($document->tags as $tag) {
            $view = new View;
            $view->document_id = $document->id;
            $view->user_id = (Yii::$app->user->isGuest) ? 
                Yii::$app->params['anonymousUserId'] : Yii::$app->user->id;
            $view->tag_id = $tag->id;
            $view->session_id = $session->id;
            $view->save();
        };


        return $this->render('view', [
            'document' => $document,
            'recommendations' => $this->getRecommendation($document->id)
        ]);

    }

    public function actionLoadtags($id, $api = false) {
        $document = Document::findOne($id);
        $artist = urlencode($document->interpret->name);
        $track = urlencode($document->name);
        $exists_tags = Tag::find()->indexBy('name');
        $apy_key = Yii::$app->params['last_fm_api_key'];

        $url = 
            "http://ws.audioscrobbler.com/2.0/".
            "?method=track.getTopTags".
            "&api_key=$apy_key&".
            "artist=$artist&".
            "track=$track&".
            "format=json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($data, true)['toptags'];
        $tags = [];

        if(array_key_exists('tag', $decoded)) {
            $tags = $decoded['tag'];
            foreach($tags as $tag) {
                if(!array_key_exists($tag['name'], $exists_tags)) {
                    $newtag = new Tag;
                    $newtag->name = $tag['name'];
                    $newtag->save();
                    $newtag->id = $newtag->getPrimaryKey();
                    $tags[$tag['name']] = $newtag;
                }

                if(!View::find()->where([
                    'document_id' => $document->id,
                    'tag_id' => $tags[$tag['name']]->id,
                ])->exists())
                {
                    $map = new MapDocumentTag;
                    $map->document_id = $document->id;
                    $map->tag_id = $tags[$tag['name']]->id;
                    $map->count = ($tag['count'] > 50) ? 2 : 1;
                    $map->save();
                }
            }
        }

        $params = [
            'url' => $url,
            'document' => $document,
            'tags' => $tags,
        ];

        if(!$api) {
            return $this->render('tagfound', $params);
        } else {
            Yii::$app->response->format = 'json';
            return $params;
        }
    }

    public function actionLfm($artist, $track) {
        $apy_key = Yii::$app->params['last_fm_api_key'];
        $amp = '&';
        $track = urlencode($track);
        $artist = urlencode($artist);
        $url2 = 
            "http://ws.audioscrobbler.com/2.0/".
            "?method=track.getTopTags".
            "&api_key=$apy_key&".
            "artist=$artist&".
            "track=$track&".
            "format=json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        echo "$url2";

        echo "JOUIJOJ";
        print_r($data);
    }
}
