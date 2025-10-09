<?php

namespace app\models\forms;

use app\models\City;
use app\models\User;
use Yii;
use yii\base\InvalidRouteException;
use yii\base\Model;
use yii\db\Exception;

class RegistrationForm extends Model
{
    public string $name = '';
    public string $email = '';
    public string $city = '';
    public string $password = '';
    public string $passwordRepeat = '';
    public bool $isExecutor = false;

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'email' => 'Электронная почта',
            'city' => 'Город',
            'password' => 'Пароль',
            'passwordRepeat' => 'Повтор пароля',
            'isExecutor' => 'Я собираюсь откликаться на задания'
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
            [['name', 'email', 'city', 'password', 'passwordRepeat', 'isExecutor'], 'safe'],
            [['name', 'email', 'city', 'password', 'passwordRepeat'], 'required',
                'message' => 'Поле должно быть заполнено'],
            [['name', 'email', 'city', 'isExecutor'], 'filter', 'filter' => 'strip_tags'],
            ['email', 'email', 'message' => 'Введите корректный Email'],
            [['email'], 'unique', 'targetClass' => User::class, 'targetAttribute' => ['email' => 'email'],
                'message' => 'Пользователь с таким email уже зарегистрирован'],
            [['city'], 'exist', 'targetClass' => City::class, 'targetAttribute' => ['city' => 'id']],
            [['password'], 'string', 'min' => 8, 'message' => 'Пароль должен быть не менее 8 символов'],
            [['passwordRepeat'], 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
            [['isExecutor'], 'boolean'],
            [['name', 'email', 'city'], 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * Создает и сохраняет нового пользователя на основе данных формы.
     * @throws Exception
     * @throws InvalidRouteException
     * @throws \yii\base\Exception
     */
    public function createUser(): void
    {
        if ($this->validate()) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
            $user = $this->getUser();
            if ($user->save(false)) {
                Yii::$app->response->redirect(['tasks']);
            }
        }
    }

    /**
     * Создает новый объект пользователя на основе данных формы.
     *
     * @return User Новый объект пользователя.
     */
    protected function getUser(): User
    {
        $user = new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->city_id = $this->city;
        $user->role = $this->isExecutor ? User::ROLE_EXECUTOR : User::ROLE_CUSTOMER;
        return $user;
    }
}