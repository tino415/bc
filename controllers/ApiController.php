<?php

namespace app\controllers;


use Yii;
use yii\web\Controller;
use app\models\SearchForm;
use app\models\Document;
use app\models\Query;

class ApiController extends Controller
{
    public function getSong($id)
    {
        
    }
    public function actionSearch()
    {
        Yii::$app->response->format = 'json';
        $model = new SearchForm;
        if($model->load(Yii::$app->request->get())) {

            $query = new Query;
            $query->query = $model->phrase;
            if(Yii::$app->user->isGuest)
                $query->user_id = Yii::$app->params['anonymousUserId'];
            else $query->user_id = Yii::$app->user->id;
            $query->save();

            return [
                'phrase' => $model->phrase,
                'results' => Document::search($model->phrase),
            ];
        } else return [
            'phrase' => 'None',
            'results' => [
                'song' => 'None',
                'interpret' => 'None',
                'tags' => ['None', 'none'],
            ],
        ];
    }
}
