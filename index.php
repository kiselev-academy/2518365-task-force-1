<?php

declare(strict_types=1);

ini_set('display_errors', 'On');
error_reporting(E_ALL);

use TaskForce\Converters\CsvToSqlConverter;
use TaskForce\Exceptions\FileFormatException;

require_once __DIR__ . '/vendor/autoload.php';

$categoriesConverter = new CsvToSqlConverter('data/categories.csv', 'categories');
try {
    $categoriesConverter->saveToFile('data/categories.sql');
} catch (FileFormatException $e) {
    error_log("Неверная форма файла импорта: " . $e->getMessage());
}

$citiesConverter = new CsvToSqlConverter('data/cities.csv', 'cities');
try {
    $citiesConverter->saveToFile('data/cities.sql');
} catch (FileFormatException $e) {
    error_log("Неверная форма файла импорта: " . $e->getMessage());
}
