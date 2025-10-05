<?php

namespace app\models;

use TaskForce\Exceptions\SourceFileException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс модели для таблицы "cities".
 *
 * @property int $id ID города.
 * @property string $name Название города.
 * @property float $latitude Широта города.
 * @property float $longitude Долгота города.
 * @property string|null $created_at Дата создания
 * @property string|null $updated_at Дата обновления
 *
 * @property Task[] $tasks Задачи, связанные с данным городом.
 * @property User[] $users Пользователи, связанные с данным городом.
 */
class City extends ActiveRecord
{
    /**
     * Возвращает имя таблицы в базе данных.
     *
     * @return string Имя таблицы в базе данных.
     */
    public static function tableName(): string
    {
        return 'cities';
    }

    /**
     * Возвращает ID города по его названию.
     *
     * @param string $name Название города.
     * @return int ID города.
     * @throws SourceFileException
     */
    public static function getIdByName(string $name): int
    {
        $city = City::findOne(['name' => $name]);
        if (!$city) {
            throw new SourceFileException("Города $name нет в БД");
        }
        return $city->id;
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
     */
    public function rules(): array
    {
        return [
            [['name', 'latitude', 'longitude'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['name'], 'unique'],
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
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Получает запрос для [[Tasks]].
     *
     * @return ActiveQuery Запрос для задач, связанных с данным городом.
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['city_id' => 'id']);
    }

    /**
     * Получает запрос для [[Users]].
     *
     * @return ActiveQuery Запрос для пользователей, связанных с данным городом.
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['city_id' => 'id']);
    }

}
