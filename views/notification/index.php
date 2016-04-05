<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Notification', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'event',
            [
                'attribute'=>'from',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->fromUser->email;
                },
            ],
            [
                'attribute'=>'to',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getTo();
                },
            ],
            // 'title',
            // 'message:ntext',
            // 'type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
