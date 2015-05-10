<?php

namespace app\models;

use Yii;
use miloschuman\highcharts\Highcharts;
use yii\helpers\ArrayHelper;
?>

<?php
$together = [];
$document1_w = [];
$document2_w = [];
$categories = [];
foreach($document1->tags as $tag1) {
    foreach($document2->tags as $tag2) {
        if($tag1->id == $tag2->id) {
            $categories[] = $tag1->name;
            $document1_w[] = $tag1->getMap($document1->id)->one()->weight;
            $document2_w[] = $tag2->getMap($document2->id)->one()->weight;
            $together[] = end($document1_w) * end($document2_w);
        }
    }
}
print_r($categories);
?>

<?= Highcharts::widget([
    'options' => [
        'title' => ['text' => Yii::t('app', 'Document similar tags')],
        'xAxis' => [
            'categories' => $categories,
        ],
        'yAxis' => [
            'title' => ['text' => $document2->name],
        ],
        'series' => [
            [
                'name' => 'Together',
                'data' => $together,
            ],
            [
                'name' => $document1->name,
                'data' => $document1_w,
            ],
            [
                'name' => $document2->name,
                'data' => $document2_w,
            ],
        ]
    ]
]) ?>

