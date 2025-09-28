<?php

declare(strict_types=1);

namespace app\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
     * @throws \Exception При ошибках запроса или парсинга.
     * @throws \InvalidArgumentException При недопустимом формате.
     */
    public static function getLocationData(string $location, string $format = 'allData'): null|string|array
    {
        $apiKey = 'cad6a4b5-acf7-468c-8151-528b41621e77';

        $client = new Client([
            'base_uri' => 'https://geocode-maps.yandex.ru/',
            'timeout' => 5.0,
        ]);

        try {
            $response = $client->request('GET', '1.x', [
                'query' => [
                    'geocode' => $location,
                    'apikey' => $apiKey,
                    'format' => 'json',
                    'results' => 1,
                ],
            ]);
        } catch (GuzzleException $error) {
            throw new \Exception('Ошибка при запросе к API: ' . $error->getMessage());
        }

        $content = (string)$response->getBody();
        $responseData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Ошибка парсинга JSON:  ' . json_last_error_msg());
        }

        $featureMember = $responseData['response']['GeoObjectCollection']['featureMember'] ?? null;

        if (empty($featureMember) || !isset($featureMember[0]['GeoObject'])) {
            return null;
        }

        $geoObject = $featureMember[0]['GeoObject'];

        $posString = $geoObject['Point']['pos'] ?? '';
        if (!$posString) {
            return null;
        }
        [$longitude, $latitude] = explode(' ', $posString);

        $latitude = (float)$latitude;
        $longitude = (float)$longitude;

        $addressDetails = $geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea'] ?? null;

        $city = null;
        if (is_array($addressDetails)) {
            $city = self::getCityName($addressDetails);
        }

        $address = $geoObject['name'] ?? null;

        return match ($format) {
            'coordinates' => [$longitude, $latitude],
            'city' => $city,
            'address' => $address,
            'allData' => ['coordinates' => [$longitude, $latitude], 'city' => $city, 'address' => $address],
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