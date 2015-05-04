<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Tag;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\filter\AccessControl;

class TagController extends Controller {

    public function behaviours() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'matchCallback' => function($rule, $action) {
                            return Yii::$app->user->id == 1;
                        }
                    ],
                ]
            ]
        ];
    }

    public function actionIndex($top = 50) {
        return $this->render('index', [
            'users' => User::find()->all(),
            'mostViewedTags' => Tag::getTop($top)->all(),
            'top' => $top
        ]);
    }
}
