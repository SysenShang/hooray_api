<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model pay\models\RechargeOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recharge-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'trade_no') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'buyer_id') ?>

    <?php // echo $form->field($model, 'buyer_email') ?>

    <?php // echo $form->field($model, 'total_price') ?>

    <?php // echo $form->field($model, 'coin') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'createtime') ?>

    <?php // echo $form->field($model, 'updatetime') ?>

    <?php // echo $form->field($model, 'return_url') ?>

    <?php // echo $form->field($model, 'gateway') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
