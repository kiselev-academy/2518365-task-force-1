<?php

declare(strict_types=1);

namespace app\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\RuntimeException;
use Yii;

class Geocoder
{
    /**
     * Получение данных о местоположении по адресу.
     *
     * @param string $location Адрес для определения координат.
     * @param string $format Формат данных, который необходимо вернуть. Возможные значения:
     *  - 'coordinates' - координаты в формате [широта, долгота];
     *  - 'city' - название города;
     *  - 'address' - адрес объекта;
     *  - 'allData' - все данные в формате ['coordinates' => [...], 'city' => string|null, 'address' => string|null].
     * @return null|string|array Массив или строка с данными, или null если данные не найдены.
     * @throws GuzzleException При ошибках запроса.
     * @throws \JsonException При ошибках парсинга.
     * @throws \InvalidArgumentException При недопустимом формате.
     */
    public static function getLocationData(string $location, string $format = 'allData'): null|string|array
    {
        $apiKey = Yii::$app->params['ymaps']['apiKey'];
        if (empty($apiKey)) {
            throw new RuntimeException("Не указан параметр ключа яндекс карт");
        }

        $client = new Client([
            'base_uri' => 'https://geocode-maps.yandex.ru/',
            'timeout' => 5.0,
        ]);

        $response = $client->request('GET', '1.x', [
            'query' => [
                'geocode' => $location,
                'apikey' => $apiKey,
                'format' => 'json',
                'results' => 1,
            ],
        ]);

        $content = (string)$response->getBody();
        $responseData = json_decode($content, true, 512, JSON_THROW_ON_ERROR);


        $featureMember = $responseData['response']['GeoObjectCollection']['featureMember'] ?? null;

        if (empty($featureMember) || empty($key = array_key_first($featureMember)) || !isset($featureMember[$key]['GeoObject'])) {
            return null;
        }

        $key = array_key_first($featureMember);
        $geoObject = $featureMember[$key]['GeoObject'];

        $posString = $geoObject['Point']['pos'] ?? null;
        if (empty($posString) || !is_string($posString)) {
            return null;
        }

        $arr = explode(' ', $posString);
        if (count($arr) !== 2) {
            throw new RuntimeException('Некорректно получены широта и долгота');
        }
        [$longitude, $latitude] = $arr;

        $latitude = (float)$latitude;
        $longitude = (float)$longitude;

        $addressDetails = $geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails'] ?? null;
        $city = null;
        if ($addressDetails && isset($addressDetails['AddressDetails'])) {
            $city = self::getCityName($addressDetails['AddressDetails']);
        }

        $address = $geoObject['name'] ?? null;

        return match ($format) {
            'coordinates' => [$latitude, $longitude],
            'city' => $city,
            'address' => $address,
            'allData' => ['coordinates' => [$latitude, $longitude], 'city' => $city, 'address' => $address],
            default => throw new \InvalidArgumentException('Недопустимый формат данных: ' . $format),
        };
    }

    /**
     * Поиск значение ключа 'LocalityName' в массиве и возврат его.
     * Если не найден, возврат 'AdministrativeAreaName', если есть.
     *
     * @param array $array Массив для поиска
     * @return string|null
     */
    public static function getCityName(array $array): ?string
    {
        foreach ($array as $key => $value) {
            if ($key === 'LocalityName' && is_string($value)) {
                return $value;
            }

            if (is_array($value) && ($result = self::getCityName($value)) !== null) {
                return $result;
            }
        }

        return isset($array['AdministrativeAreaName']) && is_string($array['AdministrativeAreaName'])
            ? $array['AdministrativeAreaName']
            : null;
    }
}