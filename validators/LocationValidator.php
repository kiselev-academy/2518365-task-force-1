<?php

namespace app\validators;

use app\models\City;
use app\services\Geocoder;
use GuzzleHttp\Exception\ClientException;
use Yii;
use yii\validators\Validator;

class LocationValidator extends Validator
{
    /** Ключ для кэша городов */
    protected const string CITY_KEY = 'LocationValidator::validateValue::%s';

    /**
     * Валидация значения города.
     *
     * @param mixed $value Значение для валидации.
     * @return array|null Массив с ошибками или null, если валидация прошла успешно.
     */
    protected function validateValue($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        return Yii::$app->cache->getOrSet(sprintf(self::CITY_KEY, $value), static function () use ($value) {
            try {
                Yii::error('Пробуем определить город для: ' . $value);
                $cityName = Geocoder::getLocationData($value, 'city');
            } catch (ClientException|\JsonException $e) {
                return ['Не удалось подтвердить название города', []];
            }

            if (!$cityName || !is_string($cityName)) {
                return ['Не удалось определить название города', []];
            }
            if (!City::findOne(['name' => $cityName])) {
                return ["Города $cityName нет в БД", []];
            }
            return null;
        });
    }
}