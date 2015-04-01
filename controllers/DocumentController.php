<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Document;
use app\models\Action;
use app\models\ActionType;

class DocumentController extends Controller {

    public function actionIndex() {
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
        $document = Document::find($id)->one();

        $action = new Action;
        $action->type_id = ActionType::DISPLAY_ID;
        $action->document_id = $document->id;
        if(Yii::$app->user->isGuest)
            $action->user_id = Yii::$app->params['anonymousUserId'];
        else $action->user_id = Yii::$app->user->id;
        $action->save();

        return $this->render('view', [
            'model' => $document
        ]);
    }
}
