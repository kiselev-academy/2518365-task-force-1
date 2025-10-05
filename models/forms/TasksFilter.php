<?php

namespace app\models\forms;

use yii\base\Model;

class TasksFilter extends Model
{
    public const string ALL_TIME = 'ALL TIME';
    public const string ONE_HOUR = '1 HOUR';
    public const string HALF_DAY = '12 HOUR';
    public const string ONE_DAY = '24 HOUR';

    public array $categories = [];
    public bool $distantWork = false;
    public bool $noResponse = false;
    public ?string $period = NULL;

    /**
     * Возвращает ассоциативный массив периодов времени для фильтрации.
     *
     * @return array Ассоциативный массив периодов.
     */
    public static function getPeriodsMap(): array
    {
        return [
            self::ALL_TIME => 'за всё время',
            self::ONE_HOUR => '1 час',
            self::HALF_DAY => '12 часов',
            self::ONE_DAY => '24 часа'
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
            'categories' => 'Категории',
            'distantWork' => 'Удаленная работа',
            'noResponse' => 'Без откликов',
            'period' => 'Период'
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
            [['categories', 'distantWork', 'noResponse', 'period'], 'safe'],
        ];
    }
}