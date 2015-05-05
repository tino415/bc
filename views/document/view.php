<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = Yii::t('app', 'song');
?>
<div class="row">
    <div class="col-md-3">
        <div class="row">
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
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5>Tags</h5>
                </div>
                <div class="panel-body">
                    <?php foreach($document->tags as $tag): ?>
                    <span class="label label-default"><?= $tag; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>
                    <strong><?= $document->interpret->name ?></strong> - 
                    <?= $document->name ?>
                </h4>
                <div class="row">
                    <div class="col-md-4">
                            <?= Yii::t('app', 'Transposition') ?>:
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
                            <?= Yii::t('app', 'Print') ?>
                        </a>
                        <a  class="btn btn-default"
                            href="javascript:pexport(<?= $document->id ?>,'TXT')">
                            <span class="glyphicon glyphicon-export"></span>
                            <?= Yii::t('app', 'Text') ?>
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
        <?php $possition = 1000; ?>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Similiar documents</h3>
                </div>
                <div class="panel-body">
                <?= ListView::widget([
                    'dataProvider' => $similiar_documents,
                    'itemView' => '_document',
                    'viewParams' => ['offset' => 1000],
                ]); ?>
                </div>
            </div>
        </div>
        <?php $possition = 1; ?>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Recommendation</h3>
                </div>
                <div class="panel-body">
                <?= ListView::widget([
                    'dataProvider' => $recommendations,
                    'itemView' => '_document',
                ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/moduluj.js'); ?>
