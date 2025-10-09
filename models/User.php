<?php

namespace app\models;

use app\services\UserRatingService;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель для таблицы "users".
 *
 * @property int $id ID пользователя.
 * @property string $name Имя пользователя.
 * @property string $email Электронная почта пользователя.
 * @property string $password Пароль пользователя.
 * @property string $role Роль пользователя.
 * @property string|null $birthday Дата рождения пользователя.
 * @property string|null $phone Номер телефона пользователя.
 * @property string|null $telegram Имя пользователя в Telegram.
 * @property string|null $info Информация о пользователе.
 * @property string|null $specializations Специализации пользователя.
 * @property string|null $avatar URL аватара пользователя.
 * @property int|null $successful_tasks Кол-во выполненных задач.
 * @property int|null $failed_tasks Кол-во проваленных задач.
 * @property int $city_id ID города пользователя.
 * @property int|null $vk_id ID пользователя ВКонтакте.
 * @property int $hidden_contacts Скрыть контакты для всех, кроме заказчика.
 * @property float $total_score Рейтинг пользователя.
 * @property string|null $created_at Дата создания.
 * @property string|null $updated_at Дата изменения.
 *
 * @property City $city Связь с моделью города.
 * @property Response[] $responses Связь с моделью откликов.
 * @property Review[] $customerReviews Связь с моделью отзывов заказчиков.
 * @property Review[] $executorReviews Связь с моделью отзывов исполнителей.
 * @property Task[] $customerTasks Связь с моделью задач заказчиков.
 * @property Task[] $executorTasks Связь с моделью задач исполнителей.
 */
class User extends ActiveRecord implements IdentityInterface
{

    /**
     * ENUM field values
     */
    const string ROLE_CUSTOMER = 'customer';
    const string ROLE_EXECUTOR = 'executor';

    /**
     * Возвращает название таблицы.
     *
     * @return string Название таблицы.
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * Возвращает ID пользователя.
     *
     * @return int|null ID пользователя.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Находит объект пользователя по ID.
     *
     * @param int $id ID пользователя.
     * @return User|null Возвращает найденного пользователя или null, если пользователь не найден.
     */
    public static function findIdentity($id): ?User
    {
        return self::findOne($id);
    }

    /**
     * Находит объект пользователя по маркеру доступа
     *
     * @param string $token Маркер доступа.
     * @param string|null $type Тип маркера доступа.
     * @return User|null Возвращает User|null.
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
     */
    public function rules(): array
    {
        return [
            [['birthday', 'phone', 'telegram', 'info', 'specializations', 'avatar', 'successful_tasks', 'failed_tasks', 'vk_id'], 'default', 'value' => null],
            [['total_score'], 'default', 'value' => 0],
            [['name', 'email', 'password', 'role', 'city_id'], 'required'],
            [['role', 'info'], 'string'],
            [['birthday', 'created_at', 'updated_at'], 'safe'],
            [['successful_tasks', 'failed_tasks', 'city_id', 'vk_id', 'hidden_contacts'], 'integer'],
            [['total_score'], 'number'],
            [['name', 'email', 'password', 'telegram'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 11],
            [['specializations', 'avatar'], 'string', 'max' => 255],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * Возвращает роль пользователя.
     *
     * @return string[] Роль пользователя.
     */
    public static function optsRole(): array
    {
        return [
            self::ROLE_CUSTOMER => 'customer',
            self::ROLE_EXECUTOR => 'executor',
        ];
    }

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Role',
            'birthday' => 'Birthday',
            'phone' => 'Phone',
            'telegram' => 'Telegram',
            'info' => 'Info',
            'specializations' => 'Specializations',
            'avatar' => 'Avatar',
            'successful_tasks' => 'Successful Tasks',
            'failed_tasks' => 'Failed Tasks',
            'city_id' => 'City ID',
            'vk_id' => 'Vk ID',
            'hidden_contacts' => 'Hidden Contacts',
            'total_score' => 'Total Score',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Получает запрос для [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Получает запрос для [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['executor_id' => 'id']);
    }

    /**
     * Получает запрос для [[customerReviews]].
     *
     * @return ActiveQuery
     */
    public function getCustomerReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['customer_id' => 'id']);
    }

    /**
     * Получает запрос для [[customerTasks]].
     *
     * @return ActiveQuery
     */
    public function getCustomerTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['customer_id' => 'id']);
    }

    /**
     * Получает запрос для [[executorTasks]].
     *
     * @return ActiveQuery
     */
    public function getExecutorTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Отображает название текущей роли пользователя.
     *
     * @return string Название роли, соответствующее текущему значению $role.
     */
    public function displayRole(): string
    {
        return self::optsRole()[$this->role];
    }

    /**
     * Проверяет, является ли роль пользователя "Заказчик".
     *
     * @return bool true, если роль текущего пользователя — ROLE_CUSTOMER; иначе false.
     */
    public function isRoleCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Устанавливает роль пользователя как "Заказчик".
     *
     * @return void
     */
    public function setRoleToCustomer(): void
    {
        $this->role = self::ROLE_CUSTOMER;
    }

    /**
     * Проверяет, является ли роль пользователя "Исполнитель".
     *
     * @return bool true, если роль текущего пользователя — ROLE_EXECUTOR; иначе false.
     */
    public function isRoleExecutor(): bool
    {
        return $this->role === self::ROLE_EXECUTOR;
    }

    /**
     * Устанавливает роль пользователя как "Исполнитель".
     *
     * @return void
     */
    public function setRoleToExecutor(): void
    {
        $this->role = self::ROLE_EXECUTOR;
    }

    /**
     * Возвращает ключ аутентификации.
     *
     * @return string|null Возвращает string|null.
     * @throws \yii\base\Exception
     */
    public function getAuthKey(): ?string
    {
        return Yii::$app->getSecurity()->generatePasswordHash($this->id . $this->password);
    }

    /**
     * Валидирует ключ аутентификации.
     *
     * @param string $authKey Ключ аутентификации.
     * @return bool Возвращает true|false.
     */
    public function validateAuthKey($authKey): bool
    {
        return Yii::$app->getSecurity()->validatePassword($this->id . $this->password, $authKey);
    }

    /**
     * Проверяет правильность пароля.
     *
     * @param string $password Пароль для проверки.
     * @return bool Возвращает true, если пароль правильный, иначе false.
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }


    /**
     * Получает запрос для [[executorReviews]].
     *
     * @return ActiveQuery
     */
    public function getExecutorReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    /**
     * Возвращает статус пользователя.
     *
     * @return string Статус пользователя.
     */

    public function getUserStatus(): string
    {
        if (Task::findOne(['executor_id' => $this->id, 'status' => TaskBasic::STATUS_WORK])) {
            return 'Занят';
        }
        return 'Открыт для новых заказов';
    }

    /**
     * Получает экземпляр UserRatingService
     *
     * @return UserRatingService
     */
    public function getRatingService(): UserRatingService
    {
        return new UserRatingService($this);
    }

}
