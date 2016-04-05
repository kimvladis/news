<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Notification;

/* @var $this yii\web\View */
/* @var $model Notification */
/* @var $form yii\widgets\ActiveForm */
/* @var $events[] string */
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event')->dropDownList($events) ?>

    <?= $form->field($model, 'from')->dropDownList($model->getFromUsers()) ?>

    <?= $form->field($model, 'to')->dropDownList($model->getToUsers()) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>
    <div id="params"></div>

    <?= $form->field($model, 'type')->checkboxList(Notification::getTypes()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
