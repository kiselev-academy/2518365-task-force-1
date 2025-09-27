<?php

namespace app\models;

use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\IdentityInterface;

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
 * @property Review[] $customerReviews
 * @property Review[] $executorReviews
 * @property Task[] $customerTasks
 * @property Task[] $executorTasks
 * @property mixed|null $getExecutorReviews
 */
class User extends ActiveRecord implements IdentityInterface
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
     * Gets query for [[customerReviews]].
     *
     * @return ActiveQuery
     */
    public function getCustomerReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[executorReviews]].
     *
     * @return ActiveQuery
     */
    public function getExecutorReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[customerTasks]].
     *
     * @return ActiveQuery
     */
    public function getCustomerTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[executorTasks]].
     *
     * @return ActiveQuery
     */
    public function getExecutorTasks(): ActiveQuery
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

    public static function getCurrentUser(): ?User
    {
        return User::findOne(Yii::$app->user->getId());
    }

    public static function findIdentity($id): User|IdentityInterface|null
    {
        return self::findOne($id);
    }

    /**
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return Yii::$app->getSecurity()->generatePasswordHash($this->id . $this->password);
    }

    public function validateAuthKey($authKey): bool
    {
        return Yii::$app->getSecurity()->validatePassword($this->id . $this->password, $authKey);
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function getUserRating(): string
    {
        $sum = 0;
        $reviews = $this->getExecutorReviews()->all();

        foreach ($reviews as $review) {
            $sum += $review['rating'] ?? 0;
        }

        if ($sum < 0) {
            return 0;
        }

        if (count($reviews) + $this->failed_tasks) {
            return round($sum / (count($reviews) + $this->failed_tasks), 2);
        }

        return 0;
    }

    /**
     * @throws Exception
     */
    public function getCounterCompletedTasks(): bool
    {
        $this->successful_tasks += 1;
        return $this->save();
    }

    /**
     * @throws Exception
     */
    public function getCounterFailedTasks(): bool
    {
        $this->failed_tasks += 1;
        return $this->save();
    }

    public static function getUserStars($rating): string
    {
        $count = round($rating);
        $filledStars = str_repeat('<span class="fill-star">&nbsp;</span>', $count);
        $emptyStars = str_repeat('<span>&nbsp;</span>', 5 - $count);
        return $filledStars . $emptyStars;
    }

    public function getUserStatus(): string
    {
        if (Task::findOne(['executor_id' => $this->id, 'status' => TaskBasic::STATUS_WORK])) {
            return 'Занят';
        }
        return 'Открыт для новых заказов';
    }

}
