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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5>Chords</h5>
            </div>
            <div class="panel-body">
                <?php foreach($schemas as $schema): ?>
                <img src="http://www.supermusic.sk/akord2.php?akord=<?=$schema?>"/>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><?= $document->name ?></h5>
            </div>
            <div class="panel-body">
                <?= $content ?>
            </div>
        </div>
    </div>


    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-heading">Song name 1</div>
            <div class="modal-body">
                <div>Interpret</div>
                <div>tags</div>
            </div>
        </div>
    </div>
</div>
