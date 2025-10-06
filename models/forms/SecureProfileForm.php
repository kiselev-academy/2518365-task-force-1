<?php

namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\db\Exception;

class SecureProfileForm extends Model
{
    public string $oldPassword = '';
    public string $newPassword = '';
    public string $repeatPassword = '';
    public bool $hiddenContacts = false;

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'oldPassword' => 'Старый пароль',
            'newPassword' => 'Новый пароль',
            'repeatPassword' => 'Повтор нового пароля',
            'hiddenContacts' => 'Скрыть контакты для всех, кроме заказчика',
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
            ['oldPassword', 'validateOldPassword'],
            ['newPassword', 'validateNewPassword', 'skipOnEmpty' => false],
            ['repeatPassword', 'compare', 'compareAttribute' => 'newPassword'],
            ['hiddenContacts', 'safe'],
        ];
    }

    /**
     * Валидация старого пароля.
     *
     * @param string $attribute Атрибут для валидации.
     * @param ?array $params Параметры валидации.
     */
    public function validateOldPassword(string $attribute, ?array $params): void
    {
        $user = User::findOne(Yii::$app->user->id);

        if (!$user || !Yii::$app->security->validatePassword($this->oldPassword, $user->password)) {
            $this->addError($attribute, 'Неправильный старый пароль');
        }
    }

    /**
     * Валидация нового пароля.
     *
     * @param string $attribute Атрибут для валидации.
     * @param ?array $params Параметры валидации.
     */
    public function validateNewPassword(string $attribute, ?array $params): void
    {
        if (!empty($this->newPassword) && (empty($this->oldPassword))) {
            $this->addError('oldPassword', 'Введите старый пароль');
        }
        if (empty($this->newPassword) && !empty($this->oldPassword)) {
            $this->addError($attribute, 'Введите новый пароль');
        }
        if (!empty($this->newPassword) && empty($this->repeatPassword)) {
            $this->addError('repeatPassword', 'Повторите новый пароль');
        }
    }

    /**
     * Сохраняет данные профиля пользователя.
     *
     * @param int $userId ID пользователя.
     * @return bool Результат сохранения данных профиля пользователя.
     * @throws Exception|\yii\base\Exception Исключение.
     */
    public function saveProfile(int $userId): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findOne($userId);
        if (!$user) {
            return false;
        }

        if (!empty($this->newPassword)) {
            $user->password = Yii::$app->security->generatePasswordHash($this->newPassword);
        }

        $user->hidden_contacts = $this->hiddenContacts ? 1 : 0;

        return $user->save();
    }
}