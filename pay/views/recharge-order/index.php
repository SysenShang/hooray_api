<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel pay\models\RechargeOrder */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '交易记录';
?>
<div class="recharge-order">
    <div class="top">
        <div class="container">
            <div class="col-xs-12">
                <h2 class="user">
                    hi,<?= Yii::$app->user->identity->username?>:
                </h2>
                <h3 class="user">
                    账户余额： <span class="balance"><?=$userCount->coin ?></span> 哇哇豆   <a href="/pay/create" class="btn recharge "> 充 值 </a>
                </h3>
            </div>
        </div>
    </div>
    <div class="container">
    <?php foreach($rechargeOrders as $rechargeOrder): ?>
        <div class="col-xs-12 list">
            <div class="row">
                <div class="col-xs-6 list_title">充值</div><div class="col-xs-6 text-right total_price"><?=$rechargeOrder->total_price?> 元</div>
            </div>
            <div class="row details">
                <div class="col-xs-12 col-sm-2"><?=date('Y.m.d',strtotime($rechargeOrder->createtime))?></div>
                <div class="col-xs-12 col-sm-6">支付号：<?=$rechargeOrder->trade_no?></div>
                <div class="col-xs-12 col-sm-4 text-right">
                    <?php if($rechargeOrder->status==0):?>待支付
                    <?php elseif($rechargeOrder->status==1):?>支付失败
                    <?php elseif($rechargeOrder->status==2):?>支付成功
                    <?php endif; ?>
<!--                    <a href="/pay/create" class="go_on">继续支付</a>-->
                </div>
            </div>
        </div>

    <?php endforeach; ?>
        <?= LinkPager::widget(['pagination' => $pagination]) ?>
    </div>

<?php /*

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'order_id',
                'trade_no',
                'title',
                'user_id',
                 'buyer_id',
                 'buyer_email:email',
                 'total_price',
                 'coin',
                 'status',
                 'createtime',
                 'updatetime',
                 'return_url:ntext',
                 'gateway',
            ],
        ]); ?>
*/?>
</div>
