<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $birthday
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $info
 * @property string|null $specializations
 * @property string|null $avatar
 * @property int|null $successful_tasks
 * @property int|null $failed_tasks
 * @property int $city_id
 * @property int|null $vk_id
 * @property int $hidden_contacts
 * @property float $total_score
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property City $city
 * @property Response[] $responses
 * @property Review[] $reviews
 * @property Review[] $reviews0
 * @property Task[] $tasks
 * @property Task[] $tasks0
 */
class User extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const string ROLE_CUSTOMER = 'customer';
    const string ROLE_EXECUTOR = 'executor';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews0]].
     *
     * @return ActiveQuery
     */
    public function getReviews0(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return ActiveQuery
     */
    public function getTasks0(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole(): array
    {
        return [
            self::ROLE_CUSTOMER => 'customer',
            self::ROLE_EXECUTOR => 'executor',
        ];
    }

    /**
     * @return string
     */
    public function displayRole(): string
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function setRoleToCustomer(): void
    {
        $this->role = self::ROLE_CUSTOMER;
    }

    /**
     * @return bool
     */
    public function isRoleExecutor(): bool
    {
        return $this->role === self::ROLE_EXECUTOR;
    }

    public function setRoleToExecutor(): void
    {
        $this->role = self::ROLE_EXECUTOR;
    }
}
