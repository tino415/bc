<?php

use yii\grid\GridView;
?>

<div class="col-md-6">
    <div class="row">
    <div class="panel panel-default">
        <div class="panel-header">
            <div class="col-sm-offset-1">
            <h2><?= Yii::t('app', 'Tag Frequency') ?></h2>
            </div>
        </div>
        <div class="panel-body">
        <?= GridView::widget([
            'dataProvider' => $full,
            'columns' => [
                'name',
                [
                    'attribute' => 'interpret',
                    'value' => 'interpret.name',
                ],
                [
                    'attribute' => 'type',
                    'value' => 'type.name',
                ],
            ]
        ]); ?>
        </div>
    </div>
    </div>
</div>

<div class="col-md-6">
    <div class="row">
    <div class="panel panel-default">
        <div class="panel-header">
            <div class="col-sm-offset-1">
            <h2><?= Yii::t('app', 'Log Term Slices') ?></h2>
            </div>
        </div>
        <div class="panel-body">
        <?= GridView::widget([
            'dataProvider' => $timeAware,
            'columns' => [
                'name',
                [
                    'attribute' => 'interpret',
                    'value' => 'interpret.name',
                ],
                [
                    'attribute' => 'type',
                    'value' => 'type.name',
                ],
            ]
        ]); ?>
        </div>
    </div>
    </div>
</div>
