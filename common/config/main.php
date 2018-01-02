<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'name' => 'eBestMall',
    'version' => '8.0.0',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        // 语言包设置
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/frontend' => 'frontend.php',
                        'app/backend' => 'backend.php',
                        'app/api' => 'api.php',
                        'app/mobile' => 'mobile.php',
                        'app/error' => 'error.php',
                        'app/tips' => 'tips.php',
                    ],
                ],
                // 覆盖更改yii自带语言包
                'yii' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'sourceLanguage' => 'en-US',
                    'basePath' => '@common/messages'
                ],
            ],
        ],

        // 主题视图设置
        'view' => [
            'theme' => [
                //'basePath' => '@app/themes/default',
                //'baseUrl' => '@web/themes/default',
                'pathMap' => [
                    '@app/views' => '@app/themes/default',
                ],
            ],
        ],

        // 缓存配置
        'session' => [
            'class' => 'yii\web\DbSession',
            // 'db' => 'mydb',  // the application component ID of the DB connection. Defaults to 'db'.
            // 'sessionTable' => 'my_session', // session table name. Defaults to 'session'.
            'timeout' => 3600,
        ],

        /* 自定义组件 */
        'sms' => [
            'class' => 'common\components\SendSms',
            'config' => $params['sms'],
        ],
        'sendMail' => [
            'class' => 'common\components\SendMail',
        ],
        'payment' => [
            'class' => 'common\components\Payment',
            'config' => $params['payment'],
        ],

        // 第三方登录客户端配置
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => $params['authClients'],
        ],

        /*
         * 创建从后端到前端的链接
         * Yii::$app->urlManagerFrontend->createAbsoluteUrl('/site/index')
         */
        'urlManagerFrontend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerFrontend.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'suffix' => '.html',
            'rules' => [
            ],
        ],
        'urlManagerMobile' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerMobile.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'suffix' => '.html',
            'rules' => [
            ],
        ],
        'urlManagerBackend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerBackend.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'urlManagerApi' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerApi.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'Statics' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerStatics.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'urlManagerCms' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerCms.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'urlManagerBbs' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => $params['urlManagerBbs.baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
];
