<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class YandexMapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        "https://api-maps.yandex.ru/2.1/?apikey=&lang=ru_RU",
        'js/map-init.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

    public function init(): void
    {
        parent::init();

        $apiKey = Yii::$app->params['ymaps']['apiKey'];

        if (empty($apiKey)) {
            throw new \RuntimeException("Не указан параметр ключа яндекс карт");
        }

        $this->js[0] = $apiKey;
    }
}