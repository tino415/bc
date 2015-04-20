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
    <h3>Hľadaná fráza: <span id="phrase" class="label label-default"><?= $phrase ?></span></h3>
    </div>
</div>

<div id='results' class="row">
<?php foreach($results as $result) : ?>
<div class="col-sm-6 col-md-4 col-lg-3">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="<?= Url::toRoute(['document/view','id' => $result->id])?>">
                <h4><?= $result->name ?></h4>
            </a>
        </div>
        <div class="modal-body">
            <div>Interpret: <span><?= $result->interpret->name ?></span></div>
            <span class="label label-info"><?= $result->type->name ?></span>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
