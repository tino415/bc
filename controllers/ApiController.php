<?php

namespace app\controllers;


use Yii;
use yii\web\Controller;
use app\models\SearchForm;
use app\models\Document;

class ApiController extends Controller
{
    public function actionSearch()
    {
        Yii::$app->response->format = 'json';
        $model = new SearchForm;
        if($model->load(Yii::$app->request->get())) 
            return [
                'phrase' => $model->phrase,
                'results' => Document::search($model->phrase),
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
