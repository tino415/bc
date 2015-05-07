<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\AppAsset;
use app\widgets\SearchWidget;
use app\widgets\LoginWidget;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            echo SearchWidget::widget([
                'url_param' => 'search',
            ]);

            $items = [
                ['label' => Yii::t('app', 'Home'), 'url' => ['/document/index']],
                ['label' => Yii::t('app', 'Songbook'), 'url' => ['/user/merge']],
                ['label' => Yii::t('app','Recommend My'), 'url' => ['/document/recommendmy']],
                //['label' => 'Contact', 'url' => ['/site/contact']],
            ];

            if(!Yii::$app->user->isGuest) 
                $items[] = ['label' => Yii::t('app', 'Profile'), 'url' => ['/user/actual']];

            //if(Yii::$app->user->id == 1) {
            //  $items[] = ['label' => Yii::t('app', 'Tags'), 'url' => ['/tag/index']];
            //}

            echo LoginWidget::widget();
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $items,
            ]);

            NavBar::end();
        ?>

        <?= $content ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; FIIT Martin Černák <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
