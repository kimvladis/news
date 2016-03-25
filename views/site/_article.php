<?php
use kartik\markdown\Markdown;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/** @var \app\models\Article $model */
?>
<div class="row">
    <div class="col-sm-4"><a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>"><img
                src="/uploads/<?= $model->photo ?>" class="img-responsive"></a>
    </div>
    <div class="col-sm-8">
        <h3 class="title"><a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>"
                             class="article-title-link"><?= Html::encode($model->title) ?></a></h3>
        <a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>" class="article-highlight-link">
            <p><?= StringHelper::truncateWords(HtmlPurifier::process(Markdown::convert($model->content)), 50) ?></p></a>

        <p class="text-muted">Author - <?= htmlspecialchars($model->author->name) ?><span
                class="pull-right"><?= date("m/d/y", strtotime($model->created_at)); ?></span></p>
    </div>
</div>
<hr>

