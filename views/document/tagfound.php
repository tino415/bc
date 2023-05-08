<?php

$this->title = Yii::t('app', 'Explore Tags');
?>

<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1>Tag explore for <?= $document->name ?></h1>
            <p>
                Generated url: 
                <span class="lagel label-default">
                    <?= $url ?>
                </span>
            </p>
        </div>

        <div class="panel-body">
            <?php if(count($tags) == 0): ?>
            <p>No tags were found</p>
            <?php else: ?>
                <ul>
                <?php foreach($tags as $tag): ?>
                <li>
                <?= $tag['name']; ?>
                </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
