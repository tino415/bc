<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'Search');
$this->params['breadcrumbs'][] = $this->title;

$ress = [];
for ($i=0;$i<10;$i++) {
    $ress[] = "results $i";
}
?>

<div class="row">
    <?php foreach($ress as $s): ?>
    <div class="col-md-4">
        <div class="panel panel-default">
        <div class="panel-heading"><h4>Heading</h4></div>
        <div class="modal-body">
        <?= $s ?>
        </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
