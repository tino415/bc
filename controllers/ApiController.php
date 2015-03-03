<?php

namespace app\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\SearchForm;

class ApiController extends Controller
{
    public function actionSearch()
    {
        Yii::$app->response->format = 'json';
        $model = new SearchForm;
        if($model->load(Yii::$app->request->get())) 

            return [
                'phrase' => $model->phrase,
                'results' => Yii::$app->superMusic->searchSong($model->phrase),
            ];
        else return [
            'phrase' => 'None',
            'results' => [
                'song' => 'None',
                'interpret' => 'None',
                'tags' => ['None', 'none'],
            ],
        ];
    }
}
