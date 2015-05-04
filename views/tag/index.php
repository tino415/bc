<?php

use miloschuman\highcharts\Highcharts;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Tag Statystics');
?>
<div class="panel panel-default">
    <?php foreach($users as $user): ?>
    <?php $topTags = $user->getTopTags($top)->all(); ?>

    <div class="col-md-6">
        <?= Highcharts::widget([
                'options' => [
                    'title' => ['text' => 
                        Yii::t('app', 'Most viewes tags')." for $user->username"
                    ],
                    'xAxis' => [
                        'categories' => ArrayHelper::getColumn($topTags, 'name'),
                    ],
                    'yAxis' => [
                        'title' => ['text' => Yii::t('app', 'Viewed')]
                    ],
                    'series' => [[
                        'name' => $user->username,
                        'data' => ArrayHelper::getColumn($topTags, 'count')
                    ]]
                ]
        ]); ?>
    </div>
    <?php endforeach; ?>

    <?= Highcharts::widget([
            'options' => [
                'title' => ['text' => Yii::t('app', 'Most viewes tags')],
                'xAxis' => [
                    'categories' => ArrayHelper::getColumn($mostViewedTags, 'name'),
                ],
                'yAxis' => [
                    'title' => ['text' => Yii::t('app', 'Viewed')]
                ],
                'series' => [[
                    'name' => 'All',
                    'data' => ArrayHelper::getColumn($mostViewedTags, 'count')
                ]]
            ]
        ]); ?>
</div>

