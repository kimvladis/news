<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Notification */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'event',
            [
                'attribute'=>'from',
                'value'=>$model->fromUser->email,
            ],
            [
                'attribute'=>'to',
                'value'=>$model->getTo(),
            ],
            'title',
            'message:ntext',
            [
                'attribute'=>'type',
                'value'=>array_reduce($model->type, function($prev, $curr) { return $prev . ($prev ? ', ' : '') . \app\models\Notification::getTypes()[$curr];}),
            ],
        ]
    ]) ?>

</div>
