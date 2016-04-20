<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'Development' => [
        'path' => 'dev',
        'setWritable' => [
            'pay/runtime',
            'pay/web/assets',
            'pay/views',
            'pay/models',
            'pay/controllers',
            'pay',
            //android手机客户端
            'mbandroid/runtime',
            'mbandroid/views',
            'mbandroid/models',
            'mbandroid/controllers',
            'mbandroid/web/assets',
            'mbandroid'

        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'pay/config/main-local.php',
            'mbandroid/config/main-local.php',
        ],
    ],
    'Production' => [
        'path' => 'prod',
        'setWritable' => [
            'pay/runtime',
            'mbandroid/runtime',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'pay/config/main-local.php',
        ],
    ],
];