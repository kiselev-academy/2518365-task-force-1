<?php

use yii\db\Migration;

class m251009_132500_create_initial_schema extends Migration
{
    /*
     * Создание таблиц БД.
     */
    public function safeUp(): void
    {
        $this->createTable('cities', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(128)->notNull()->unique(),
            'latitude' => $this->decimal(11,8)->notNull(),
            'longitude' => $this->decimal(11,8)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createTable('categories', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(128)->notNull()->unique(),
            'icon' => $this->string(128)->notNull(),
        ]);

        $this->createTable('users', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(128)->notNull(),
            'email' => $this->string(128)->notNull()->unique(),
            'password' => $this->string(128)->notNull(),
            'role' => "ENUM('customer', 'executor') NOT NULL",
            'birthday' => $this->dateTime(),
            'phone' => $this->char(11),
            'telegram' => $this->string(128),
            'info' => $this->text(),
            'specializations' => $this->string(255),
            'avatar' => $this->string(255),
            'successful_tasks' => $this->integer(),
            'failed_tasks' => $this->integer(),
            'city_id' => $this->integer()->unsigned()->notNull(),
            'vk_id' => $this->integer()->unsigned(),
            'hidden_contacts' => $this->boolean()->defaultValue(false),
            'total_score' => $this->float()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_users_city', 'users', 'city_id', 'cities', 'id');

        $this->createTable('tasks', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'budget' => $this->integer()->unsigned(),
            'status' => $this->string(128)->notNull()->defaultValue('new'),
            'city_id' => $this->integer()->unsigned()->notNull(),
            'location' => $this->string(255),
            'latitude' => $this->decimal(11,8),
            'longitude' => $this->decimal(11,8),
            'deadline' => $this->dateTime(),
            'customer_id' => $this->integer()->unsigned()->notNull(),
            'executor_id' => $this->integer()->unsigned(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_tasks_category', 'tasks', 'category_id', 'categories', 'id');
        $this->addForeignKey('fk_tasks_city', 'tasks', 'city_id', 'cities', 'id');
        $this->addForeignKey('fk_tasks_customer', 'tasks', 'customer_id', 'users', 'id');
        $this->addForeignKey('fk_tasks_executor', 'tasks', 'executor_id', 'users', 'id');

        $this->createTable('responses', [
            'id' => $this->primaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'executor_id' => $this->integer()->unsigned()->notNull(),
            'price' => $this->integer(),
            'comment' => $this->text(),
            'status' => $this->string(128)->notNull()->defaultValue('new'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_responses_task', 'responses', 'task_id', 'tasks', 'id');
        $this->addForeignKey('fk_responses_executor', 'responses', 'executor_id', 'users', 'id');

        $this->createTable('reviews', [
            'id' => $this->primaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'customer_id' => $this->integer()->unsigned()->notNull(),
            'executor_id' => $this->integer()->unsigned()->notNull(),
            'rating' => $this->integer(),
            'comment' => $this->text(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_reviews_task', 'reviews', 'task_id', 'tasks', 'id');
        $this->addForeignKey('fk_reviews_customer', 'reviews', 'customer_id', 'users', 'id');
        $this->addForeignKey('fk_reviews_executor', 'reviews', 'executor_id', 'users', 'id');

        $this->createTable('files', [
            'id' => $this->primaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'path' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk_files_task', 'files', 'task_id', 'tasks', 'id');

        $this->execute("CREATE FULLTEXT INDEX task_title_search ON tasks(title);");
        $this->execute("CREATE FULLTEXT INDEX task_description_search ON tasks(description);");
    }

    /*
     * Удаление таблиц БД.
     */
    public function safeDown(): void
    {
        $this->execute("DROP INDEX IF EXISTS task_title_search ON tasks;");
        $this->execute("DROP INDEX IF EXISTS task_description_search ON tasks;");

        $this->dropTable('files');
        $this->dropTable('reviews');
        $this->dropTable('responses');
        $this->dropTable('tasks');
        $this->dropTable('users');
        $this->dropTable('categories');
        $this->dropTable('cities');
    }
}
