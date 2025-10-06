<?php

namespace app\models;

use TaskForce\Models\Task as TaskBasic;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Класс модели для таблицы "tasks".
 *
 * @property int $id ID задачи.
 * @property string $title Название задачи.
 * @property string|null $description Описание задачи.
 * @property int $category_id ID категории задачи.
 * @property string|null $budget Бюджет задачи.
 * @property string $status Статус задачи.
 * @property int $city_id ID города задачи.
 * @property string|null $location Местоположение задачи.
 * @property float|null $latitude Широта местоположения задачи.
 * @property float|null $longitude Долгота местоположения задачи.
 * @property string|null $deadline Дедлайн задачи.
 * @property int $customer_id ID заказчика.
 * @property int|null $executor_id ID исполнителя.
 * @property string|null $created_at Дата создания.
 * @property string|null $updated_at Дата изменения.
 *
 * @property Category $category Категория задачи.
 * @property City $city Город задачи.
 * @property User $customer Заказчик задачи.
 * @property User $executor Исполнитель задачи.
 * @property File[] $files Файлы, прикрепленные к задаче.
 * @property Response[] $responses Отклики на задачу.
 * @property Review[] $reviews Отзывы о задачи.
 */
class Task extends ActiveRecord
{
    /**
     * Возвращает имя таблицы в базе данных.
     *
     * @return string Имя таблицы в базе данных.
     */
    public static function tableName(): string
    {
        return 'tasks';
    }

    /**
     * Начинает выполнение задачи и назначает исполнителя.
     *
     * @param int $executorId Идентификатор исполнителя.
     * @return bool Возвращает true, если задача успешно сохранена, иначе - false.
     * @throws Exception
     */
    public function startWork(int $executorId): bool
    {
        $this->status = TaskBasic::STATUS_WORK;
        $this->executor_id = $executorId;
        return $this->save();
    }

    /**
     * Помечает задачу как проваленную.
     *
     * @return bool Возвращает true, если задача успешно сохранена, иначе - false.
     * @throws Exception
     */
    public function failTask(): bool
    {
        $this->status = TaskBasic::STATUS_FAILED;
        return $this->save();
    }

    /**
     * Отменяет задачу.
     *
     * @return bool Возвращает true, если задача успешно сохранена, иначе - false.
     * @throws Exception
     */
    public function cancelTask(): bool
    {
        $this->status = TaskBasic::STATUS_CANCELLED;
        return $this->save();
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
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
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
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
     * Получает запрос для [[Category]].
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
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
     * Получает запрос для [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Получает запрос для [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Получает запрос для [[Files]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(File::class, ['task_id' => 'id']);
    }

    /**
     * Получает запрос для [[Responses]].
     *
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(Response::class, ['task_id' => 'id']);
    }

    /**
     * Получает запрос для [[Reviews]].
     *
     * @return ActiveQuery
     */
    public function getReviews(): ActiveQuery
    {
        return $this->hasMany(Review::class, ['task_id' => 'id']);
    }

}
