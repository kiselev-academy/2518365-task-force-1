<?php

use yii\db\Migration;

class m251009_133308_insert_categories extends Migration
{
    /*
     * Добавление таблицы категорий в БД.
     */
    public function safeUp(): void
    {
        $this->batchInsert('categories', ['name', 'icon'], [
            ['Курьерские услуги', 'courier'],
            ['Уборка', 'clean'],
            ['Переезды', 'cargo'],
            ['Компьютерная помощь', 'neo'],
            ['Ремонт квартирный', 'flat'],
            ['Ремонт техники', 'repair'],
            ['Красота', 'beauty'],
            ['Фото', 'photo'],
        ]);
    }

    /*
     * Удаление таблицы категорий БД.
     */
    public function safeDown(): void
    {
        $this->delete('categories', ['name' => [
            'Курьерские услуги',
            'Уборка',
            'Переезды',
            'Компьютерная помощь',
            'Ремонт квартирный',
            'Ремонт техники',
            'Красота',
            'Фото',
        ]]);
    }
}
