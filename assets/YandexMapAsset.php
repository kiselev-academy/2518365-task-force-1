<?php

namespace app\assets;

use yii\web\AssetBundle;

class YandexMapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        "https://api-maps.yandex.ru/2.1/?apikey=cad6a4b5-acf7-468c-8151-528b41621e77&lang=ru_RU",
        'js/map-init.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];
}