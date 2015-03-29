<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-1">
    <h3>Hľadaná fráza: <span id="phrase" class="label label-default">None</span></h3>
    </div>
    <div class="col-md-8">
        <h3>
        <div id="load-prog" class="progress" style='display: none;'>
        <div class="progress-bar progress-bar-striped active"
            role="progressbar"
            aria-valuenow="100"
            aria-valuemin="0"
            aria-valuemax="100"
            style="width: 100%;">
        </div>
        </div>
        </h3>
    </div>
</div>

<div id='results' class="row"></div>
