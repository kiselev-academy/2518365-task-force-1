<?php

namespace app\models;

use TaskForce\Exceptions\SourceFileException;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Task[] $tasks
 * @property User[] $users
 */
class City extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['city_id' => 'id']);
    }

    /**
     * @throws SourceFileException
     */
    public static function getIdByName($name): int
    {
        $city = City::findOne(['name' => $name]);
        if (!$city) {
            throw new SourceFileException("Города $name нет в БД");
        }
        return $city->id;
    }

}
