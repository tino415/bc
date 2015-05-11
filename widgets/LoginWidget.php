<?php
namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\widgets\models\LoginForm;
use app\widgets\models\UserRegisterForm;
use yii\helpers\Html;

class LoginWidget extends Widget {

    public function run() {
        $model = new LoginForm();
        $register = new UserRegisterForm();
        if($model->load(Yii::$app->request->post())) $model->login();
        if($register->load(Yii::$app->request->post())) $register->register();
        if(Yii::$app->user->isGuest) {
            return $this->render('login_form', [
                'model' => $model,
                'register' => $register,
            ]);
        } else return Html::a(
            Yii::t('app', 'Logout').' ('.Yii::$app->user->identity->username.')',
            ['/user/logout'],
            [
                'class' => 'btn btn-danger navbar-right',
                'data-method' => 'post',
                'style' => "margin-top: 8px",
            ]
        );
    }

}
