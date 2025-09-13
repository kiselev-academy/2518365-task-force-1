<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\City;
use app\models\User;

return [
    'name' => $faker->name,
    'email' => $faker->unique()->email,
    'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
    'role' => $faker->randomElement([User::ROLE_CUSTOMER, User::ROLE_EXECUTOR]),
    'city_id' => $faker->randomElement(City::find()->select('id')->column()),
    'avatar' => $faker->imageUrl(200, 200, 'people',  'png'),
    'telegram' => $faker->userName,
    'phone' => substr($faker->e164PhoneNumber, 1, 11),
    'show_contacts' => $faker->boolean,
    'birthday' => $faker->date('Y-m-d', '2000-01-01'),
    'info' => $faker->paragraph,
    'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
    'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
];