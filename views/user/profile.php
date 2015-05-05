<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = $this->title;
$topTags = $model->topTags->limit(50)->all();
?>
<div class="user-profile">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Edit'), ['edit'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'email:email',
            'username',
        ],
    ]) ?>

    <?= Highcharts::widget([
        'options' => [
            'title' => ['text' => Yii::t('app', 'Most viewes tags')],
            'xAxis' => [
                'categories' => ArrayHelper::getColumn($topTags, 'name'),
            ],
            'yAxis' => [
                'title' => ['text' => Yii::t('app', 'Viewed')]
            ],
            'series' => [[
                'name' => $model->username,
                'data' => ArrayHelper::getColumn($topTags, 'count')
            ]]
        ]
    ]); ?>

</div>
