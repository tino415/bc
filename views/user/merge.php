<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\AutoComplete;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'User Merging');
?>

<div class="row">
    
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'users') ?>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php $form->end(); ?>

        <table class="table">
            <?php foreach($documents as $document): ?>
            <tr>
                <td><?= $document->name ?></td>
                <td><?= $document->interpret->name ?></td>
                <td><?= $document->type->name ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
