<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $items = [
        ['label' => 'My articles', 'url' => ['/article/index'], 'visible' => Yii::$app->user->can('createArticle')],
        ['label' => 'Notification manager', 'url' => ['/notification/index'], 'visible' => Yii::$app->user->can('createNotification')],
        ['label' => 'Logout (' . Yii::$app->user->identity->name . ')', 'url' => ['/site/logout'], 'visible' => Yii::$app->user->can('createArticle')]
    ];
    if (!Yii::$app->user->isGuest) {
        $count = Yii::$app->user->identity->getNotificationsCount();
        $items = ['<li><a href="/notification/my">Notifications' . (($count > 0) ? (' <span class="badge">'.$count.'</span>') : '') . '</a></li>'] + $items;
    }
    NavBar::begin(
        [
            'brandLabel' => 'Vladislav Kim',
            'brandUrl'   => Yii::$app->homeUrl,
            'options'    => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
    echo Nav::widget(
        [
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items'   => $items,
        ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
