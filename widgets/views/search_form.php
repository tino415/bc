<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

//$search_url = Url::toRoute($search_route);
//$is_search_page = (Url::current() == $search_url);
$search_url = '';
$is_search_page = '';

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
        '?query=' + encodeURI($('#searchform-phrase').val());

}
/*
    <?php //if($is_search_page): ?>
        var phrase = $('#searchform-phrase').serialize();
        $('#load-prog').css('display', 'block');
        $.ajax({
            type : 'GET',
            url: '<?= Url::toRoute('api/search') ?>',
            data : phrase,
            error : function(data) {
                console.log('Error '+data);
            },
            success : function(data) {
                console.log(data);
                display_results(data.phrase, data.results);
                $('#load-prog').css('display', 'none');
            },
        })
    <?php //else: ?>
        window.location = "<?= $search_url ?>#" + encodeURI($('#searchform-phrase').val());
    <?php //endif; ?>
}

<?php if($is_search_page): ?>
    function display_results(phrase, results) {
        console.log(results);
        $('#results').empty();
    
        window.location.hash = encodeURI(phrase);
        $('#phrase').html(phrase);
    
        $.each(results, function(index, value) {
            console.log(value);
            tags = '';
            $.each(value.tags, function(index, value) {
                tags += '<span class="label label-info">' +value+'</span> '
            });
            $('#results').append($('\
                <div class="col-sm-6 col-md-4 col-lg-3">\
                    <div class="panel panel-default">\
                        <div class="panel-heading">\
                            <a href="'+value.link+'">\
                                <h4>'+value.name+'</h4>\
                            </a>\
                        </div>\
                        <div class="modal-body">\
                            <div>Interpret: <span>'+value.interpret+'</span></div>\
                            '+tags+'\
                        </div>\
                    </div>\
                </div>'
            ));
        });
    }
<?php endif; ?>
*/
</script>
    
<?php if($is_search_page): ?>
    <?php /*$this->registerJS("
        if(window.location.hash) {
            $('#searchform-phrase').val(window.location.hash.substring(1));
            send();
        }
    "); */?>
<?php endif; ?>
