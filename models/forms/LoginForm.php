<?php

namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    private ?User $_user = null;

    public function attributeLabels(): array
    {
        return [
            'email' => 'Электронная почта',
            'password' => 'Пароль',
        ];
    }

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'safe'],
            ['password', 'validatePassword'],
        ];
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }

    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password)) {
                $this->addError($attribute, 'Неправильный email или пароль');
            }
        }
    }
}