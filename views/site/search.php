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

$ress = [];
for ($i=0;$i<10;$i++) {
    $ress[] = "results $i";
}
?>
<div class="search-form row">
    <div class="col-md-7 col-md-offset-1">
    <div class="panel panel-defaultm">
    <?php $form = ActiveForm::begin([
        'id' => 'search-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "{beginWrapper}\n{input}\n{endWrapper}",
        ],
        'options' => [
            'onsubmit' => "return false",
            'onkeypress' => "if(event.keycode == 13) send()",
        ],
    ]); ?>
        <div class="input-group">
        <?= $form->field($model, 'phrase'); ?>
        <div class="input-group-btn">
        <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i>', [
            'class' => 'btn btn-default btn-primary',
            'onclick' => 'send();',
        ]) ?>
        </div>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
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

<div id='results' class="row">
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

<script>
function send() {
    console.log("Requesting search");
    var data = $('#searchform-phrase').serialize();
    $('#load-prog').css('display', 'block');
    $.ajax({
        type : 'GET',
        url: '<?= Url::toRoute('api/search') ?>',
        data : data,
        error : function(data) {
            console.log('Error '+data);
        },
        success : function(data) {
            $('#results').empty();
            $('#phrase').html(data.phrase);
            $.each(data.results, function(index, value) {
                console.log(value);
                tags = '';
                $.each(value.tags, function(index, tag) {
                    tags += ' <span class="label label-info">' + tag + '</span> ';
                });
                console.log(index);
                $('#results').append($('\
    <div class="col-md-4">\
        <div class="panel panel-default">\
        <div class="panel-heading"><h4>'+value.song+'</h4></div>\
        <div class="modal-body">\
            <div>Interpret: <span>'+value.interpret+'</span></div>\
        ' + tags + '\
        </div>\
        </div>\
    </div>'
    ));
            });
        $('#load-prog').css('display', 'none');
        },
    })
}
</script>
