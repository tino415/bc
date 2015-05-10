<?php

use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Stats | '.$model->name;
?>

<h1><?= $model->name ?></h1>

<h2><?= $model->interpret->name ?></h2>

<a
    class="btn btn-default"
    href="<?= Url::toRoute(['document/stats', 
        'id' => $model->id,
        'action' => 'createTagsFromAtts'
    ]); ?>"
>
<?= Yii::t('app', 'Create tags from documet attributes'); ?>
</a>

<?= GridView::widget([
    'dataProvider' => $tags,
    'columns' => [
        'id',
        'type_id',
        'tag.name',
        'count',
        'weight',
    ]
]) ?>
