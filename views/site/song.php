<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'song');
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="row">
    <div class="col-md-9">
        <div class="pane panel-default">
            <div class="panele-heading">
                <h2><?= $model->name ?></h2>
            </div>
            <div class="modal-body">
                <iframe class='col-md-9'style='height:764px;width:100%'
                    src='http://www.supermusic.sk/<?= $model->link ?>'></iframe>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="pane panel-default">
            <div class="panel-heading">Song name 1</div>
            <div class="modal-body">
                <div>Interpret</div>
                <div>tags</div>
            </div>
        </div>
    </div>
</div>


