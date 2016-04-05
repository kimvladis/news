<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $models \app\models\UserNotification[] */
/* @var $pages \yii\data\Pagination */

$this->title = 'My Notifications';
?>
<div class="site-index">

    <div class="col-lg-9">
        <?php foreach ($models as $model) { ?>
            <div class="alert <?=$model->notified == 1 ? 'alert-warning' : 'alert-info'?>" role="alert">
                <?php if ($model->notified == 0) {?>
                    <button type="button" class="btn btn-primary pull-right js-notified" data-id="<?=$model->id?>">Ok</button>
                <?php } ?>
                <h4><?=$model->title?></h4>
                <?=$model->message?>
                <p class="text-muted">From: <?= htmlspecialchars($model->fromUser->name) ?><span
                        class="pull-right"><?= date("m/d/y", strtotime($model->created_at)); ?></span></p>
            </div>
        <?php } ?>

        <?= LinkPager::widget(
            [
                'pagination' => $pages,
            ]); ?>
    </div>
</div>
