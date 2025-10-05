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

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Электронная почта',
            'password' => 'Пароль',
        ];
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'safe'],
            ['password', 'validatePassword'],
            [['email'], 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * Проверяет корректность пароля.
     *
     * @param string $attribute Название атрибута.
     * @param array|null $params Дополнительные параметры, переданные в правило.
     * @return void
     */
    public function validatePassword(string $attribute, ?array $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !Yii::$app->security->validatePassword($this->password, $user->password)) {
            $this->addError($attribute, 'Неправильный email или пароль');
        }
    }

    /**
     * Возвращает пользователя по email.
     *
     * @return User|null Возвращает экземпляр пользователя или null, если пользователь не найден.
     */
    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}