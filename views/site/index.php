<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $models \app\models\Article[] */
/* @var $pages \yii\data\Pagination */

$this->title = 'News';
?>
<div class="site-index">

    <div class="col-lg-9">
        <?php foreach ($models as $model) { ?>
            <?= $this->render('_article', ['model' => $model]); ?>
        <?php } ?>

        <?= LinkPager::widget(
            [
                'pagination' => $pages,
            ]); ?>
    </div>
    <div class="col-lg-3">
        <div class="panel panel-default">
            <div class="panel-heading">RSS Feed</div>
            <div class="panel-body">
                <a href="<?= Url::to('site/rss', true) ?>"><?= Url::to('site/rss', true) ?></a>
            </div>
        </div>
    </div>
</div>
