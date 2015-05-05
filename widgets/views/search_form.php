<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\jui\AutoComplete;
use app\assets\AutocompleteAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

//$search_url = Url::toRoute($search_route);
//$is_search_page = (Url::current() == $search_url);
$search_url = '';
$is_search_page = '';

AutocompleteAsset::register($this);
$this->registerJS("
    $('#searchform-phrase').autocomplete({
        serviceUrl: '".Url::toRoute(['tag/autocomplete'])."',
        maxHeight: 500,
        onSelect: function() {send();},
        triggerSelectOnValidInput: false,
        beforeRender: function(container) {
            container.css('background-color', '#FFF');
            container.css('font-size', '20px');
            container.css('border', '1px solid transparent');
            container.css('border-color', '#ddd');
            container.css('border-bottom-left-radius', '3px');
            container.css('border-bottom-right-radius', '3px');
            container.children('div').each(function() {
                $(this).css('padding', '5px');
            })
        }
    })")
?>

<div class="col-md-7 col-sm-6" >
    <?php $form = ActiveForm::begin([
        'id' => 'search-form',
        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'template' => "{beginWrapper}\n{input}\n{endWrapper}",
        ],
        'options' => [
            'class' => "navbar-form-custom",
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
    <?php ActiveForm::end(); ?>
    </div>
    </div>
</div>

<script>
function send() {

    window.location = '<?= Url::toRoute(['/document']) ?>' +
        '?<?= $url_param; ?>=' + encodeURI($('#searchform-phrase').val());

}
</script>
