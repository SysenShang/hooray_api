<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 9/30/15
 * Time: 9:32 AM
 */
/** 定义目录为当前目录 */

/**
 * SendClou自动加载依赖类。
 * @author delong
 */
function emayLoader() {
    /** PHP Mailer依赖 */

    /** SendCloud依赖 */
    require_once 'nusoap.php';
    require_once 'Client.php';
}

spl_autoload_register("emayLoader");