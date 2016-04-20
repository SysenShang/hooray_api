<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$host = explode('.', $_SERVER["HTTP_HOST"]);
if (count($host) > 2) {
    define('DOMAIN', $host[1] . '.' . $host[2]);
} else {
    define('DOMAIN', $host[0] . '.' . $host[1]);
}
define('DOMAIN_HOME', '.' . DOMAIN);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);
$process = shell_exec('ps x | grep TimeTask');
if (strpos($process, 'php') === false) {
    //异步
    proc_close(proc_open('php '.__DIR__.'/../../console/yii TimeTask/do &', [], $foo));
}
$application = new yii\web\Application($config);
$application->run();
