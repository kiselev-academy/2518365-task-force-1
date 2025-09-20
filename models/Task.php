<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $category_id
 * @property string|null $budget
 * @property string $status
 * @property int $city_id
 * @property string|null $location
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $deadline
 * @property int $customer_id
 * @property int|null $executor_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Category $category
 * @property City $city
 * @property User $customer
 * @property User $executor
 * @property File[] $files
 * @property Response[] $responses
 * @property Review[] $reviews
 */
class Task extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['description', 'budget', 'location', 'latitude', 'longitude', 'deadline', 'executor_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['title', 'category_id', 'city_id', 'customer_id'], 'required'],
            [['description'], 'string'],
            [['category_id', 'city_id', 'customer_id', 'executor_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['deadline', 'created_at', 'updated_at'], 'safe'],
            [['title', 'location'], 'string', 'max' => 255],
            [['budget', 'status'], 'string', 'max' => 128],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'budget' => 'Budget',
            'status' => 'Status',
            'city_id' => 'City ID',
            'location' => 'Location',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'deadline' => 'Deadline',
            'customer_id' => 'Customer ID',
            'executor_id' => 'Executor ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
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
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(File::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['task_id' => 'id']);
    }

}
