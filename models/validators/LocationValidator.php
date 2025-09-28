<?php

namespace app\models\validators;

use app\models\City;
use app\services\Geocoder;
use yii\validators\Validator;

class LocationValidator extends Validator
{
    protected function validateValue($value): ?array
    {
        $cityName = Geocoder::getLocationData($value, 'city');

        if (!$cityName || !is_string($cityName)) {
            return ["Не удалось определить название города", []];
        }
        if (!City::findOne(['name' => $cityName])) {
            return ["Города $cityName нет в базе", []];
        }
        return null;
    }
}