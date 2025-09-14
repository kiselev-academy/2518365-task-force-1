<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Category;
use app\models\City;
use app\models\Task;
use app\models\User;

return [
    'title' => $faker->sentence(3),
    'description' => $faker->paragraph,
    'category_id' => $faker->randomElement(Category::find()->select('id')->column()),
    'budget' => $faker->randomFloat(2, 100, 10000),
    'status' => $faker->randomElement(array_keys(Task::optsStatus())),
    'city_id' => $faker->randomElement(City::find()->select('id')->column()),
    'latitude' => $faker->latitude,
    'longitude' => $faker->longitude,
    'ended_at' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
    'customer_id' => $faker->randomElement(
        User::find()->select('id')->where(['role' => User::ROLE_CUSTOMER])->column()
    ),
    'executor_id' => $faker->randomElement(
        User::find()->select('id')->where(['role' => User::ROLE_EXECUTOR])->column()
    ),
    'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
    'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),

];