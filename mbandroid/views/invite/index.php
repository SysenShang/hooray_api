<html>
<head>
    <meta charset="utf-8">
    <style>
        code {
            font-size: xx-large;
        }

        div {
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>
<div>
    <h1>好哇学堂</h1>

    <h3>
        <img src="<?= \yii\helpers\Url::to(['invite/qrcode', 'code' => $u]) ?>"/>
    </h3>
    <p><a href="http://hihooray.com/">前往下载</a></p>
    <h3>学生的贴身老师 真正的提分神器</h3>
    <span>别忘记小伙伴的邀请码哦</span>
    <p>
        邀请码: <strong><code><?= $u; ?></code></strong>
    </p>
</div>

</body>
</html>
