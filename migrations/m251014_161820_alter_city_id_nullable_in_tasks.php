<?php

use yii\db\Migration;

class m251014_161820_alter_city_id_nullable_in_tasks extends Migration
{
    /**
     * Изменяет столбец city_id
     */
    public function safeUp(): void
    {
        $this->alterColumn('tasks', 'city_id', $this->integer()->unsigned());
    }

    /**
     * Изменяет столбец city_id в исходное состояние
     */
    public function safeDown(): void
    {
        $this->alterColumn('tasks', 'city_id', $this->integer()->unsigned()->notNull());
    }
}
