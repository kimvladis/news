<?php

use kartik\markdown\Markdown;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My Articles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Article', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'summary'      => '',
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'title',
                [
                    'attribute' => 'content',
                    'format'    => 'html',
                    'value'     => function ($data) {
                        return StringHelper::truncateWords(HtmlPurifier::process(Markdown::convert($data['content'])), 100);
                    },
                ],
                [
                    'attribute' => 'photo',
                    'format'    => 'html',
                    'value'     => function ($data) {
                        return Html::img(Yii::getAlias('@web') . '/uploads/' . $data['photo'],
                                         ['width' => '140px']);
                    },
                ],
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

</div>
