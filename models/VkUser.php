<?php

namespace app\models;

use TaskForce\Exceptions\SourceFileException;
use Yii;
use yii\base\Exception;
use yii\base\Model;

class VkUser extends Model
{
    /**
     * Создает нового пользователя на основе данных, полученных из ВКонтакте.
     *
     * @param array $userData Массив с данными пользователя из ВКонтакте.
     * @return void
     * @throws SourceFileException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function createUser(array $userData): void
    {
        $user = new User;
        $user->name = $userData['first_name'];
        $user->email = $userData['email'];
        $birthdayDate = null;
        if (!empty($userData['bdate'])) {
            $birthdayDate = \DateTime::createFromFormat('d.m.Y', $userData['bdate']);
        }
        $user->birthday = $birthdayDate ? $birthdayDate->format('Y-m-d') : null;
        $user->password = Yii::$app->getSecurity()->generatePasswordHash('password');
        $user->city_id = City::getIdByName($userData['city']['title']);
        $user->vk_id = $userData['user_id'];
        $user->avatar = $userData['photo'];
        $user->save(false);

        Yii::$app->user->login($user);
    }
}