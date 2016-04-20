<?php

/* @var $this \yii\web\View */
/* @var $content string */

use pay\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '好哇学堂',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '首页', 'url' => ['/site/index-hooray']],
        ['label' => '充值', 'url' => ['/pay/create']],
        ['label' => '充值记录', 'url' => ['/recharge-order/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = [
            'label' => '退出 (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>


        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>

</div>
<div class="frame-container" >
    <div class="container" >
        <div class="row feature-block ">
            <div class="pad-download" id="pad" style="display:none">
                <div class=" text-right icon ">
                    <a href="javascript:void(0)">
                        <img src="/imgs/k-icon.png" id="close1"alt="">
                    </a>
                </div>
                <div class="text-center first">
                    <p >用Pad扫描左侧二维码, 或直接点击下载</p>

                    <div class="  two">
                        <div class="pad">
                            <img  src="/imgs/pad-code.png"  alt="">
                        </div>
                        <div class="pad1">
                            <p >支持系统:  Android4.0及以上</p>
                            <a href="#" onclick="validataOS()"><img  src="/imgs/android-d.png" alt="" id="pad-download"></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="phone-download text-left" id="phone" style="display:none">
                <div class="text-right icon">
                    <a href="javascript:void(0)" >
                        <img src="/imgs/k-icon.png" class="text-right" id="close2"alt="">
                    </a>
                </div>
                <div class="first text-center">
                    <p >用手机扫描左侧二维码, 或直接下载</p>


                    <div class="two">
                        <div class="phone">
                            <img  src="/imgs/phone-code.png"  id="phone-code" alt="">
                        </div>
                        <div class="phone1 ">
                            <div><a  href="#"><img  src="/imgs/apple-d.png" alt="" id="ios-download" ></a></div>
                            <div><a  href="#"><img  src="/imgs/android-d.png" alt="" id="and-download" ></a></div>
                            <p >支持系统:  Android4.0及以上</p>

                        </div>
                    </div>
                </div>
            </div>

            <div id="about" style="display:none">
                <div style="text-align:right">
                    <a href="javascript:void(0)" onclick="about1()"><img src="/imgs/k-icon.png" class="text-right" alt="" ></a>
                </div>
                <h1 style="">关于我们</h1>
                    <span class="text-left" style=""> 如果你拥有创业的激情，如果你热爱互联网教育，来吧！和我们共同成就一家伟大的公司！
                    </span>
                    <span class="span2" >
                            <h>我们的定位：K12、理科、在线答疑/辅导</h>
                    </span>
                    <span class="span3" >
                            <h>我们的产品：独到特别，让学生的成绩因我们的产品得到有效提升</h>
                    </span>
                    <span class="span3" >
                                <h>我们的团队：内心是狂热的，氛围是融洽的，精神是向上的</h>
                    </span>
                    <span class="span3" >
                                <h>你必须懂得：责任、忠诚、宽容、善良、分享</h>
                    </span>
                    <span class="span4">
                        <h>公司地址：上海市漕河泾开发区漕宝路1243号勇卫商务大厦3A88室</h>
                    </span>

            </div>
            <script>

            </script>
        </div>
    </div>

</div>
<footer class="footer main_foot">
    <div class="container">
        <div class="col-xs-4 col-md-4">
            <div class="row text-left">
                <h4 class="col-xs-12 col-md-12 col-md-offset-6 ">下载客户端</h4>
                <p class="col-xs-12  col-md-12 col-md-offset-6"><a href="javascript:void(1)" onclick="pc_download()">PC版下载</a></p>
                <p class="col-xs-12  col-md-12 col-md-offset-6"><a href="javascript:void(1)" onclick="phone()" >手机版下载</a></p>
                <p class="col-xs-12  col-md-12 col-md-offset-6"><a href="javascript:void(1)" onclick="pad()" >Pad版本下载</a></p>
            </div>
        </div>
        <div class="col-xs-4 col-md-4 middle">
            <div class="row text-left">
                <h4 class="col-xs-12 col-md-12 col-md-offset-3 ">联系我们</h4>
                <p class="col-xs-12  col-md-12 col-md-offset-3">官方QQ群/313729517</p>
                <p class="col-xs-12  col-md-12 col-md-offset-3">微信公众号/hooray好哇学堂</p>
                <p class="col-xs-12  col-md-12 col-md-offset-3">客服邮箱/service@hihooray.com</p>
            </div>
        </div>
        <div class="col-xs-4 col-md-4">
            <div class="row text-left">
                <h4 class="col-xs-12 col-md-12 col-md-offset-2 ">加入我们</h4>
                <p class="col-xs-12  col-md-12 col-md-offset-2"><a href="http://www.hihooray.com/site/main">招聘老师</a></p>
                <p class="col-xs-12  col-md-12 col-md-offset-2">技术团队</p>
                <p class="col-xs-12 col-md-12 col-md-offset-2" ><a href="javascript:void(1)" id="click_about">关于我们</a></p>
            </div>
        </div>
        <div class="col-xs-12 col-md-12 text-center bottom">
            <p>Copyright @ 上海燃耀信息科技有限公司 版权所有</p>
        </div>
    </div>

</footer>
<div id="body" >

</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">提示</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script>
        //识别电脑 pad
    tag =1;
    $("a").click(function(){
        location.href=this.href;
    });


    $("#and-download").click(function(){
        tag=2;
        validataOS();
    });
    $("#ios-download").click(function(){
        tag=3;
        validataOS();
    });
    $("#pad-download").click(function(){
        tag=4;
        validataOS();
    });


    function validataOS(){

        var u = navigator.userAgent, app = navigator.appVersion;
        var isAndroidPhone = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1 || u.indexOf('Windows') > -1; //android手机
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1 || u.indexOf('Windows') > -1; //android终端或者uc浏览器
        var isIPhone = u.indexOf('iPhone OS') > -1;
        var isPad = u.indexOf('iPad') > -1;
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        var currentUrl = window.location.href;

        //手机
        if(tag==2){
            if (isAndroidPhone) {
                var matchUrl = "http://hihooray.echalk.cn";
                if(String(currentUrl).indexOf(matchUrl)>= 0){
                    window.location.href = "http://t.cn/R4ovdDO";
                }else{
                    window.location.href = "http://t.cn/R48AW3L";
                }
            }else{
                $('#myModal').modal('show');
                $(".modal-body").html("请用安卓手机下载");
            }
            tag=1;
        }
        if(tag==3){
            if (isIPhone || isPad) {
                window.location.href = "https://itunes.apple.com/cn/app/hooray/id979058110?mt=8";
            }else{
                $('#myModal').modal('show');
                $(".modal-body").html("请用苹果手机下载");

            }
            tag=1;
        }
        //pad
        if(tag==4) {
            if (isAndroid) {
                var matchUrl = "http://hihooray.echalk.cn";
                if(String(currentUrl).indexOf(matchUrl)>= 0){
                    window.location.href = "http://t.cn/R4ovDDl";
                }else{
                    window.location.href = "http://t.cn/R48AMfs";
                }
            }else{
                $('#myModal').modal('show');
                $(".modal-body").html("请用安卓Pad下载");
            }
            tag=1;
        }

    }

    function pc_download(){
        $('#myModal').modal('show');
        $(".modal-body").html("请用pc版下载");
    }
    function phone(){
        $("#phone").show()
        $('#body').show().css('height',$(document.body).height());
    }
    function pad(){
        $("#pad").show()
        $('#body').show().css('height',$(document.body).height());
    }

    function close1(){
        $('#pad').hide();
        $('#body').hide();
    }
    function close2(){
        $('#phone').hide();
        $('#body').hide();
    }

    $('#close1').click(function(){
        close1()
    })
    $('#close2').click(function(){
        close2()
    })

    $('#click_about').click(function(){
        $('#about').show();
        $('#body').show().css('height',$(document.body).height());
    });

    function about1(){
        $('#about').hide();
        $('#body').hide();
    }
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
