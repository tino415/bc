<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<!-- Button HTML (to Trigger Modal) -->
<a  href="#login-form"
    class="btn btn-success navbar-nav navbar-right"
    data-toggle="modal"
    style="margin-top: 8px">
    Sign in
</a>
         
<!-- Modal HTML -->
<div id="login-form" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => 
                        "{label}\n".
                        "<div class=\"col-lg-6\">{input}</div>\n".
                        "<div class=\"col-lg-12\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-3 control-label'],
                ],
            ]); ?>

            <div class="modal-header">
                <button type="button" class="close"
                    data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Sign in</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Login', [
                    'class' => 'btn btn-primary',
                    'name' => 'login-button'
                ]) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJS('$("#login-form").appendTo("body")');
