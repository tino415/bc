<?php

use miloschuman\highcharts\Highcharts;
use yii\helpers\ArrayHelper;

?>
<div class="panel panel-default">
    <?php foreach($users as $user): ?>
    <?php
        $topTags = $user->getTopTags($top);
        $categories = [];
        $series = ['name' => $user->username, 'data' => []];
        foreach($topTags as $topTag) {
            $categories[] = $topTag['name'];
            $series['data'][] = $topTag['count'];
        }
    ?>

    <div class="col-md-6">
        <?= Highcharts::widget([
                'options' => [
                    'title' => ['text' => 'Tag view statistics'],
                    'xAxis' => [
                        'categories' => $categories,
                    ],
                    'yAxis' => [
                        'title' => ['text' => 'Viewed']
                    ],
                    'series' => [$series]
                ]
        ]); ?>
    </div>
    <?php endforeach; ?>

    <?php
        $categories = [];
        $series = ['name' => $user->username, 'data' => []];
        foreach($mostViewesTags as $topTag) {
            $categories[] = $topTag['name'];
            $series['data'][] = $topTag['count'];
        }
    ?>

    <?= Highcharts::widget([
            'options' => [
                'title' => ['text' => 'Most viewes tags'],
                'xAxis' => [
                    'categories' => $categories,
                ],
                'yAxis' => [
                    'title' => ['text' => 'Viewed']
                ],
                'series' => [$series]
            ]
        ]); ?>
</div>

