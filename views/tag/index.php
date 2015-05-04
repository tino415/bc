<?php

use miloschuman\highcharts\Highcharts;
use yii\helpers\ArrayHelper;
use app\models\MapDocumentTag;

$this->title = Yii::t('app', 'Tag Statystics');
?>
<div class="panel panel-default">
    <?php foreach($users as $user): ?>
    <?php $topTags = $user->getTagWeights()->limit($top)->all(); ?>

    <div class="row">
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
                    'series' => [
                    [
                        'name' => 'Counts',
                        'data' => ArrayHelper::getColumn($topTags, 'count')
                    ],
                    ]
                ]
        ]); ?>
    </div>
    <div class="col-md-6">
        <?= Highcharts::widget([
                'options' => [
                    'title' => ['text' => 
                        Yii::t('app', 'Most weighted tags')." for $user->username"
                    ],
                    'xAxis' => [
                        'categories' => ArrayHelper::getColumn($topTags, 'name'),
                    ],
                    'yAxis' => [
                        'title' => ['text' => Yii::t('app', 'Viewed')]
                    ],
                    'series' => [
                    [
                        'name' => 'Avarage select weight',
                        'data' => ArrayHelper::getColumn($topTags, function($element) {
                            return round($element['avarage_select_weight'], 2);
                        })
                    ],
                    [
                        'name' => 'User weights',
                        'data' => ArrayHelper::getColumn($topTags, function($element){
                            return round($element['user_weight'], 2);
                        })
                    ],
                    [
                        'name' => 'Avarage document weights',
                        'data' => ArrayHelper::getColumn($topTags, function($element){
                            return round($element['avarage_document_weight'], 2);
                        })
                    ],
                    ]
                ]
        ]); ?>
    </div>
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

