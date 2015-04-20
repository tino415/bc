<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'song');
?>
<div class="row">
    <div class="col-md-2">
        <?php if(count($document->schemas) > 0): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5>Chords</h5>
            </div>
            <div class="panel-body">
                <?php foreach($document->schemas as $schema): ?>
                <img
                    src="http://www.supermusic.sk/akord2.php?akord=<?=$schema->content?>"/>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><strong><?= $document->interpret->name ?></strong> - <?= $document->name ?></h4>
                <div class="row">
                    <div class="col-md-4">
                            Transposition:
                            <span id="transposition" class="label label-default">0</span>
                        </span>
                        <a  class="btn btn-default"
                            href="javascript:transposition(2)">
                            +2
                        </a>
                        <a  class="btn btn-default"
                            href="javascript:transposition(1)">
                            +1
                        </a>
                        <a  class="btn btn-default"
                            href="javascript:transposition(-1)">
                            -1
                        </a>
                        <a  class="btn btn-default"
                            href="javascript:transposition(-2)">
                            -3
                        </a>
                    </div>
                    <div class="col-md-8">
                        <a  class="btn btn-default"
                            href="javascript:pprint(<?= $document->id ?>)">
                            <span class="glyphicon glyphicon-print"></span>
                            Print
                        </a>
                        <a  class="btn btn-default"
                            href="javascript:pexport(<?= $document->id ?>,'TXT')">
                            <span class="glyphicon glyphicon-export"></span>
                            Text
                        </a>
                        <a  class="btn btn-default"
                            href="javascript:pexport(<?= $document->id ?>, 'AGAMA')">
                            <span class="glyphicon glyphicon-export"></span>
                            Agama
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <?= $document->content ?>
            </div>
        </div>
    </div>


    <div class="col-md-3">
        <?php foreach($recommendations as $document) : ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="<?= Url::toRoute(['document/rview', 'id' => $document->id]); ?>">
                    <?= $document->name ?>
                </a>
            </div>
            <div class="modal-body">
                <div><?= $document->interpret->name ?></div>
                <div><?= $document->type->name ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/moduluj.js'); ?>
