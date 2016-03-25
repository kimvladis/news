<?php

use kartik\markdown\Markdown;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $isPdf boolean */

$this->title = Html::encode($model->title);
?>
<div class="article-view">

    <div class="body-content">
        <div class="col-lg-4">
            <img src="/uploads/<?= $model->photo ?>" class="img-responsive"/>
        </div>
        <div class="col-lg-8">
            <h1><?= $this->title ?></h1>

            <p class="lead"><?= HtmlPurifier::process(Markdown::convert($model->content)) ?></p>

            <hr>

            <p class="text-muted">Author
                - <?= htmlspecialchars($model->author->name) ?><?= (isset($isPdf) && $isPdf) ? "\n" : '' ?><span
                    class="pull-right"><?= date("m/d/y", strtotime($model->created_at)); ?></span></p>
            <?php if (!isset($isPdf) || !$isPdf) { ?>
                <p>
                    <?= Html::a('Export PDF', ['pdf', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?php if ($model->author_id == Yii::$app->user->getId()) { ?>

                        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data'  => [
                                'confirm' => 'Are you sure you want to delete this article?',
                                'method'  => 'post',
                            ],
                        ]) ?>

                    <?php } ?>
                </p>
            <?php } ?>
        </div>
    </div>
</div>
