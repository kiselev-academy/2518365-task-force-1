<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс модели для таблицы "categories".
 *
 * @property int $id ID категории.
 * @property string $name Название категории.
 * @property string $icon Знак категории.
 *
 * @property Task[] $tasks Задачи, связанные с данной категорией.
 */
class Category extends ActiveRecord
{
    /**
     * Возвращает имя таблицы в базе данных.
     *
     * @return string Имя таблицы в базе данных.
     */
    public static function tableName(): string
    {
        return 'categories';
    }

    /**
     * Возвращает название категории по ID.
     *
     * @param int $id ID категории.
     * @return string|null Название категории.
     */
    public static function getCategoryName(int $id): ?string
    {
        return self::find()->select('name')->where(['id' => $id])->one()['name'] ?? null;
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
     */
    public function rules(): array
    {
        return [
            [['name', 'icon'], 'required'],
            [['name', 'icon'], 'string', 'max' => 128],
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
            'icon' => 'Icon',
        ];
    }

    /**
     * Получает запрос для [[Tasks]].
     *
     * @return ActiveQuery Запрос для задач, связанных с данной категорией.
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['category_id' => 'id']);
    }

}
