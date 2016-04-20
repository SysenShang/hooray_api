<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '帐号登录';
$model->rememberMe=1;
?>
<style>
body{ background: #fbf8f3;}
</style>
<div class="container">
    <div class="site-login">

        <div class="row">
            <div class="col-sm-6 img ">
                <img src='/imgs/bg_img.png' class="img_bg" alt="Timesheet Preview">
            </div>

            <div class="col-sm-6 login_box">
                <h3 style="margin: 50px 0 20px 0"><?= Html::encode($this->title) ?></h3>
                <div class="">
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'options' => ['class' => 'form-horizontal'],
                        'fieldConfig' => [
                            'template' => "<div style='height:46px; margin-top: 10px;'><div class='col-lg-7 input-group'><div class='input-group-addon' style='background:#fff; color: #aaa; padding-top:0;'>{label}</div>{input}</div></div>", //\n<div class="col-lg-5"  style="height:46px; margin-top:18px">{error}</div>
                            'inputOptions' => ['class'=>'form-control', 'style'=>'height:46px;' ],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username', ['inputOptions' => ['placeholder' => '手机号', ]])->textInput(['maxlength' => true])->label('<i class=\'glyphicon glyphicon-user\'></i>') ?>

                    <?= $form->field($model, 'password', ['inputOptions' => ['placeholder' => '请输入密码']])->passwordInput()->label('<i class=\'glyphicon glyphicon-lock\'></i>') ?>

                    <div class="form-group col-lg-12"  style="margin-top: 70px">
                        <?= Html::submitButton('登 录', ['class' => 'btn btn-lg col-lg-7', 'style' => 'background: #25b4b2; color:#fff', 'name' => 'login-button']) ?>
                        <input type="hidden" id="loginform-rememberme" class="form-control" name="LoginForm[rememberMe]" value="1" >
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>