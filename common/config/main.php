<?php
return [
    
    'name'=>'yii2',//系统名称，Yii:$app->name
    
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //配置rbac的管理类
        'authManager'=>[
            'class'=>'yii\rbac\DbManager',
        ],
    ],
];
