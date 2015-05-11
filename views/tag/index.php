<?php

use miloschuman\highcharts\Highcharts;
use yii\helpers\ArrayHelper;
use app\models\MapDocumentTag;
use yii\db\Query;
use yii\db\Expression;

$this->title = Yii::t('app', 'Tag Statystics');
?>
<div class="panel panel-default">
    <?php foreach($users as $user): ?>
    <?php 
        if($user->username == "test_user") continue;
        $topTags = $user->getTagWeights()->limit($top)
            ->addSelect(new Expression('COUNT(*) AS count'))
            ->all();
        $documentTags = (new Query)->select(
                new Expression('tag_id AS id, AVG(weight) AS weight')
            ) ->where(['tag_id' => ArrayHelper::getColumn($topTags, 'id')])
            ->from('map_document_tag')
            ->groupBy(['tag_id'])
            ->indexBy('id')
            ->all();
        $mapWeights = [];
        $selectWeights = [];

        foreach($topTags as $topTag) {
            $mapWeights[] = round($documentTags[$topTag['id']]['weight'], 2);
            $selectWeights[] = round(
                $documentTags[$topTag['id']]['weight'] * $topTag['weight'],
                2
            );
        }
    ?>

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
                            'data' => array_map('intval', 
                                ArrayHelper::getColumn($topTags, 'count')),
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
                        'data' => $selectWeights,
                    ],
                    [
                        'name' => 'Avarage document weights',
                        'data' => $mapWeights,
                    ],
                    [
                        'name' => 'User weights',
                        'data' => ArrayHelper::getColumn($topTags, function($element){
                            return round($element['weight'], 2);
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

