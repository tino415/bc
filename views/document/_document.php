<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $document app\models\Document */
/* @var $possition integer */
?>

<div class="<?=(isset($class)) ? $class : '';?>">
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><a href="<?= Url::toRoute([
            'document/view', 
            'id' => $model->id,
            'possition' => $index + ((isset($offset)) ? $offset : 0),
        ]); ?>">
            <?= $model->name ?>
        </a>
        </h4>
    </div>
    <div class="panel-body">
        <div><?= $model->interpret->name ?></div>
        <div><?= $model->type->name ?></div>
        <div>
        <?php foreach($model->getTagsOrdered()->limit(20)->each() as $tag): ?>
        <span class="label label-default"><?= $tag; ?></span>
        <?php endforeach; ?>
        </div>
    </div>
</div>
</div>
