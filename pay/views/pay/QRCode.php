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
body{ background: #fff; height: 770px; font-size:16px }
a{color: #ccc}
.create_pay .title{ margin-top: 15px}
.create_pay .title .top-title .left{margin:15px auto}
.create_pay .title .top-title h3{display: inline}
.create_pay .title .top-title h{color: #eea236; font-size:36px; }
.create_pay .title .top-title .user{font-size:36px; }
.create_pay .title .second{border-top:1px solid gray }
.create_pay a{ color: #27b3b0 }
.selection_amount{margin:118px auto}
.selection_amount .phone img{width:262px; }
.thumbnail img{width:200px; height:200px}
.thumbnail span{font-size:15px; color:gray}
</style>
<div class="container">
    <div class="create_pay">
        <div class="recharge-order-index">
            <div class="title row ">
             <div class="col-sm-12">
                    <div class="row top-title" style="margin-bottom:10px">
<!--                        <div class="col-sm-10 left"><h3 >充值账户</h3> <a class="username" href="">111</a></div>-->
                        <div class="col-sm-2 col-sm-offset-8 text-right">应付金额: <h><?php echo $totalPrice?></h>元</div>
                    </div>
<!--                    <div class="second"></div>-->
                </div>
            </div>
            <div class="selection_amount row" >
                <div class="col-sm-12 row text-center">
                    <div class="col-sm-12 col-md-offset-3 col-md-3">
                        <div class=" thumbnail selection hover" coin="60">
<!--                            <div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付</div><br/>-->
                            <img alt="扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($wechatCodeUrl);?>" />
                            <span >请使用微信扫一扫  扫描二维码支付</span>
                        </div>
                    </div>
                    <div class="col-md-2 col-md-offset-1 phone">
                        <img src="/imgs/phone.png" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


