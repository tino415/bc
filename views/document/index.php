<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\widgets\ListView;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'Search');
$this->params['breadcrumbs'][] = $this->title;
$possition = 1;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-1">
    <?php if($phrase) : ?>
    <h3><?= Yii::t('app', 'Searched phrase') ?>: 
        <span id="phrase" class="label label-default"><?= $phrase ?></span></h3>
    <?php else: ?>
    <h3><?= Yii::t('app', 'Recommended') ?></h3>
    <?php endif; ?>
    </div>
</div>

<?= ListView::widget([
    'dataProvider' => $results,
    'itemView' => '_document',
    'viewParams' => ['class' => "col-sm-6 col-md-4 col-lg-3"],
    'options' => ['id' => 'results', 'class' => 'row'],
])?>
