<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use app\models\Document;
use app\models\Action;
use app\models\ActionType;
use app\models\View;
use app\models\Tag;
use app\models\Schema;
use app\models\Session;
use app\models\User;
use app\models\MapDocumentTag;
use app\components\Globals;

class DocumentController extends Controller {

    protected function loadDocument($id) {
        if($document = Document::findOne($id)) {
            return $document;
        }
        throw new HttpException(404, 'Document not found');
    }

    public function actionIndex($search = null) {
        Session::create();
        if(is_null($search)) {
            $query = Document::recommend();
        } else {
            $query = Document::search($search);
        }

        return $this->render('index',[
            'phrase' => $search,
            'results' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 50,
                ]
            ])
        ]);
    }

    public function actionView($id, $possition = null) {
        $document = $this->loadDocument($id);

        $session = Session::getSession();
        if($session) $session->renev();
        else $session = Session::create();

        foreach($document->tags as $tag) {
            $view = new View;
            $view->document_id = $document->id;
            $view->user_id = (Yii::$app->user->isGuest) ? 
                Yii::$app->params['anonymousUserId'] : Yii::$app->user->id;
            $view->tag_id = $tag->id;
            $view->session_id = $session->id;
            $view->possition = $possition;
            $view->save();
        };


        return $this->render('view', [
            'document' => $document,
            'recommendations' => new ActiveDataProvider([
                'query' => Document::recommend(),
                'pagination' => [
                    'pageSize' => 40,
                ]
            ]),
            'similiar_documents' => new ActiveDataProvider([
                'query' => $document->getSimiliar(),
                'pagination' => [
                    'pageSize' => 1,
                ]
            ]),
        ]);
    }

    public function actionStats($id, $action = false) {
        $model = $this->loadDocument($id);
        if($action && $action == 'createTagsFromAtts')
            $model->createTagsFromAtts();
        return $this->render('stats', [
            'model' => $model,
            'tags' => new ActiveDataProvider([
                'query' => $model->getMapDocumentTags()
            ]),
        ]);
    }

    public function actionSimiliar($id1, $id2) {
        $document1 = $this->loadDocument($id1);
        $document2 = $this->loadDocument($id2);

        return $this->render('similiar', [
            'document1' => $document1,
            'document2' => $document2,
            'similiar' => Document::similiarTags($document1, $document2)->all()
        ]);
    }
}
