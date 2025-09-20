<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Category;
use app\models\City;
use app\models\User;

return [
    'name' => $faker->name,
    'email' => $faker->unique()->email,
    'password' => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
    'role' => $faker->randomElement([User::ROLE_CUSTOMER, User::ROLE_EXECUTOR]),
    'birthday' => $faker->date('Y-m-d', '2000-01-01'),
    'phone' => substr($faker->e164PhoneNumber, 1, 11),
    'telegram' => $faker->userName,
    'info' => $faker->paragraph,
    'specializations' => $faker->randomElement(Category::find()->select('name')->column()),
    'avatar' => $faker->imageUrl(200, 200, 'people', 'png'),
    'successful_tasks' => $faker->numberBetween(0, 10),
    'failed_tasks' => $faker->numberBetween(0, 10),
    'city_id' => $faker->randomElement(City::find()->select('id')->column()),
    'vk_id' => $faker->optional()->numberBetween(1000000, 99999999),
    'hidden_contacts' => $faker->numberBetween(0, 1),
    'total_score' => $faker->randomFloat(1, 1, 5),
    'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
    'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
];
