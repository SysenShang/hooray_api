<?php
namespace common\components;

class JPushNotice
{
    /**
     * @param $alias
     * @param $content
     * @author grg
     */
    public function send($alias, $content)
    {
        if (null === $alias[0]) {
            return false;
        }
        proc_close(proc_open('php ' . __DIR__ . '/../../console/yii JPush/send \'' . json_encode($alias) . '\' \'' . json_encode($content) . '\' &', [], $foo));
    }
}
