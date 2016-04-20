<?php
/**
 * Created by PhpStorm.
 * User: kevingates
 * Date: 2016-2-1
 * Time: 3:56pm
 * Alipay Express Gateway
 */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '好哇学堂充值中心';
?>
<style>
    body {
        background: #fff;
        height: 100%;
        font-size: 16px
    }
</style>
<div class="container">
    <div class="create_pay">
        <div class="recharge-order-index">
            <div class="title row ">
                <div class="col-xs-6 ">
                    <h3 style="display: inline">充值账户</h3> <a><?= Yii::$app->user->identity->username ?></a>
                </div>

                <div class="col-xs-6 text-right">
                    <a href="/recharge-order/index">查看充值记录</a>
                </div>
            </div>
            <div class="selection_amount row">
                <h3 class="col-xs-12">套餐金额</h3>
                <div class="col-xs-12 text-center">
                    <?php
                    foreach ($commonSettings as $key => $commonSetting) {
                    ?>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="thumbnail selection" coin="<?=$commonSetting->value ?>" CNY="<?=$commonSetting->key ?>">
                            <?php
                            if ($key == 0) {
                                echo('<span class="left_top">热销</span>');
                            } elseif ($key == 3) {
                                echo('<span class="left_top">大优惠</span>');
                            }
                            ?>
                            <h2><?=$commonSetting->value ?>豆</h2>
                            <h4>(<?=$commonSetting->value ?>豆=<?=$commonSetting->key ?>元)</h4>
                            <span class="right_bottom"><i class="glyphicon glyphicon-ok i"></i></span>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

                </div>
            </div>

            <h3>其它金额</h3>
            <div class="recharge-order-form row">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'form-group  col-xs-12'],
                    'fieldConfig' => [
                        'template' => "{label} {input}",
                        'inputOptions' => ['class' => 'form-control pull-left', 'style' => 'width:100px; height:46px;'],
                        'labelOptions' => ['class' => 'control-label pull-left', 'style' => 'font-size:22px; font-weight: 100'],
                    ],
                ]); ?>
                <div class="clearfix">
                    <?= $form->field($model, 'coin', ['inputOptions' => ['style' => 'width:100px']])->label("充值") ?>
                    <div class='pull-left' style='width:100px; font-size:22px; font-weight: 100; line-height: 5px;'>
                        哇哇豆
                    </div>
                    <div class="help-block"></div>
                </div>

                <h3>支付方式</h3>
                <div class="row payment_method">
                    <!--                    <div class="col-xs-12 col-sm-6 col-md-3">-->
                    <!--                        <a href="javascript:" class="payment" payment="wechatWebPay">-->
                    <!--                            <img src="/imgs/button_wechat_nor.png" class="wechatWebPay">-->
                    <!--                            <img src="/imgs/button_wechat_press.png" class="alipayExpressGateway" style="display: none">-->
                    <!--                        </a>-->
                    <!--                    </div>-->
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <a href="javascript:" class="payment" payment="alipayExpressGateway">
                            <img src="/imgs/button_allipay_press.png" class="wechatWebPay">
                            <img src="/imgs/button_allipay_nor.png" class="alipayExpressGateway" style="display: none">
                        </a>
                    </div>
                </div>
                <?= $form->field($model, 'gateway')->hiddenInput()->label(false) ?>
                <div class="help-block"></div>


                <div class="col-xs-12 col-sm-8 col-md-6">
                    <div class="thumbnail row explain">
                        <h4 class="pull-left"> 充值说明:</h4>
                        <ul class="pull-left">
                            <li>2哇哇豆=1人民币</li>
                            <li>充值后不可提现</li>
                        </ul>

                    </div>
                </div>
                <div class="col-xs-12 text-right row total">
                    应付金额：<span class="price" id="total_price">0.5</span>元
                    <?= $form->field($model, 'total_price')->hiddenInput()->label(false) ?>
                </div>
                <div class="col-xs-12 row text-right">
                    <?= Html::submitButton(Yii::t('app', '确认支付'), ['class' => 'btn btn-warning btn-lg submit_button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php if (isset($get["trade_status"]) && ($get["trade_status"] == 'TRADE_FINISHED' || $get["trade_status"] == 'TRADE_SUCCESS')) { ?>
            <!-- Modal -->
            <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 style="top:10%">
                <div class="modal-dialog" role="document">
                    <div class="modal-content text-center">
                        <div class="modal-header clearfix">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img
                                    src="/imgs/icon_close_nor.png" style="height: 30px"></button>
                            <div class="modal-title" id="myModalLabel">&nbsp;&nbsp;&nbsp;&nbsp;消息确认</div>
                        </div>
                        <div class="modal-body">
                            <h3 style="margin-top: 40px">支付结果 </h3>
                            <div class="row" style="margin: 80px 0">
                                <div class="col-xs-6 col-md-6  text-right">
                                    <button type="button" class="btn btn-lg return_button confirm_close">支付成功</button>
                                </div>
                                <div class="col-xs-6 col-md-6  text-left">
                                    <button type="button" class="btn btn-lg return_button">支付遇到问题</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 style="top:10%; ">
                <div class="modal-dialog" role="document">
                    <div class="modal-content text-center">
                        <div class="modal-header clearfix">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img
                                    src="/imgs/icon_close_nor.png" style="height: 30px"></button>
                            <div class="modal-title" id="myModalLabel">&nbsp;&nbsp;&nbsp;&nbsp;消息确认</div>
                        </div>
                        <div class="modal-body">
                            <h3>恭喜你充值成功！ </h3>
                            <div class="row" style="margin: 80px 0">
                                <div class="col-xs-12 text-center">
                                    <button type="button" class="btn btn-lg return_button success_close"> 确 定</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 style="top:10%;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content text-center">
                        <div class="modal-header clearfix">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img
                                    src="/imgs/icon_close_nor.png" style="height: 30px"></button>
                            <div class="modal-title" id="myModalLabel">&nbsp;&nbsp;&nbsp;&nbsp;消息确认</div>
                        </div>
                        <div class="modal-body">
                            <ol class="text-left">
                                <li>先确认您是否开通了网上银行</li>
                                <li>如果您开通了网银，仍然无法支付，可以打相关银行客服电话咨询。</li>
                                <li>欢迎拨打我们的客服电话，13588561958</li>
                            </ol>
                            <div class="row" style="margin: 40px 0">
                                <div class="col-xs-12 text-center">
                                    <button type="button" class="btn btn-lg return_button help_close"> 确 定</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<script language="javascript">
    var coinRateDiscount = <?= $coinRateArray['discount'] ?>;
    var coinRateNormal = <?= $coinRateArray['normal'] ?>;

    $('.selection').click(function () {
        var coin = $(this).attr('coin');
        $('#rechargeorder-coin').val(coin);
        $('.selection').removeClass('hover');
        $('.right_bottom').removeClass('show');
        $(this).addClass('hover');
        $(this).find('.right_bottom').addClass('show');
        $("#total_price").text($(this).attr('CNY'));
        $("#rechargeorder-total_price").val($(this).attr('CNY'));

        //displayUpdateRmb();
    });
    $('.payment').click(function () {
        var payment = $(this).attr('payment');
        $('#rechargeorder-gateway').val(payment);
        if (payment == 'wechatWebPay') {
            $('.alipayExpressGateway').show();
            $('.wechatWebPay').hide();
        } else {
            $('.alipayExpressGateway').hide();
            $('.wechatWebPay').show();
        }
    })

    $(function () {    //确认支付     //关闭弹出层$('#confirm').modal('hide');
        $("#confirm").modal();
    })
    $('.confirm_close').click(function () {
        $('#confirm').modal('hide');
    })

    $(function () {    //确认支付   //关闭弹出层$('#success').modal('hide');
        $("#success").modal();
    })
    $('.success_close').click(function () {
        $('#success').modal('hide');
    })

    $(function () {    //帮助     //关闭弹出层$('#help').modal('hide');
        $("#help").modal();
    })
    $('.help_close').click(function () {
        $('#help').modal('hide');
    })

    $(function () {    //帮助     //关闭弹出层$('#help').modal('hide');
        $('#success').modal('hide');
        $('#help').modal('hide');
    })

    $("#rechargeorder-coin").change(function () {
        displayUpdateRmb();
    });
    function coinToRmb(coins) {
        if (coins == 200) {
            return coins * coinRateDiscount;
        } else {
            return coins * coinRateNormal;
        }
    }

    function displayUpdateRmb() {
        var coins = $('#rechargeorder-coin').val();

        $("#total_price").text(coinToRmb(coins));
        $("#rechargeorder-total_price").val(coinToRmb(coins));
    }

    //正则验证
    function isNumber(s) {
        var regu = "^[0-9]+$";
        var re = new RegExp(regu);
        if (s.search(re) != -1) {
            return true;
        } else {
            return false;
        }
    }

    $('body').delegate('#rechargeorder-coin', 'change', function () {
        var firstChar = $(this).val().substr(0, 1);
        var char = $(this).val();
        if (isNumber($(this).val())) {
            if (firstChar == "0") {
                $("#total_price").text(0.5);
                $(this).val(1);
                $("#myModal").modal("show");
                $("#myModal .modal-body").text("第一位数字不能以0开头");
            } else {
                return true;
            }
        } else {
            if (firstChar == "-" || char.indexOf(".") > 0) {
                $("#total_price").text(0.5);
                $(this).val(1);
                $("#myModal").modal("show");
                $("#myModal .modal-body").text("只能输入正整数");
            } else {
                $("#total_price").text(0.5);
                $(this).val(1);
                $("#myModal").modal("show");
                $("#myModal .modal-body").text("只能输入数字");
            }
        }

    });

</script>

