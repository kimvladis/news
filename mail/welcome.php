<?php
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $verification \app\models\Verification */
?>
<div>
    You're almost done! Please click this link below to activate your Moz account and get started. <br>
    <a href="<?=Url::to(['site/verify', 'key' => $verification->key], true)?>">Verify!</a>
</div>