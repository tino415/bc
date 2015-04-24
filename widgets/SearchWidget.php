<?php 
namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\SearchForm;

class SearchWidget extends Widget {

    public function run() {
        $model = new SearchForm();
        $model->load(Yii::$app->request->post());
        return $this->render('search_form', [
            'model' => $model,
        ]);
    }
}
