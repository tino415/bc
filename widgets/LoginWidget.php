<?php
namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\LoginForm;
use yii\helpers\Html;

class LoginWidget extends Widget {

    public function run() {
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post())) $model->login();
        if(Yii::$app->user->isGuest) {
            return $this->render('login_form', [
                'model' => $model,
            ]);
        } else return Html::a('Logout ('.Yii::$app->user->identity->username.')',
            ['/user/logout'],
            [
                'class' => 'btn btn-danger navbar-right',
                'data-method' => 'post',
                'style' => "margin-top: 8px",
            ]
        );
    }

}
