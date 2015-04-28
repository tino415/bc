<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'User Merging');

$users = ArrayHelper::map(User::find()->all(), 'id', 'username');
$user_names = ArrayHelper::getColumn($users, 'username');
?>

<div class="row">
    
    <div class="col-md-12">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'users')->inline(true)->checkBoxList(
            $users
        ); ?>

        <div class="form-group">
            <?= Html::submitButton('Create', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php $form->end(); ?>

        <table class="table">
            <?php foreach($documents as $document): ?>
            <tr>
                <td>
                <?= Html::a(
                    $document->name,
                    ['/document/view', 'id' => $document->id]
                ) ?>
                </td>
                <td><?= $document->interpret->name ?></td>
                <td><?= $document->type->name ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php $this->registerJsFile(
    Yii::$app->request->baseUrl.'/js/autocomplete.js', 
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
