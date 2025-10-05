<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Класс модели для таблицы "files".
 *
 * @property int $id ID файла.
 * @property int $task_id ID задачи, к которой относится файл.
 * @property string $path Ссылка на файл.
 * @property string|null $created_at Дата создания.
 * @property string|null $updated_at Дата изменения.
 *
 * @property Task $task Задача, к которой относится данный файл.
 */
class File extends ActiveRecord
{
    /**
     * Возвращает имя таблицы в базе данных.
     *
     * @return string Имя таблицы в базе данных.
     */
    public static function tableName(): string
    {
        return 'files';
    }

    /**
     * Сохраняет файл с заданными ссылкой и ID задачи.
     *
     * @param string $path Ссылка на файл.
     * @param int $taskId ID задачи, к которой относится файл.
     * @return void
     * @throws Exception
     */
    public static function saveFile(string $path, int $taskId): void
    {
        $newFile = new self;
        $newFile->path = $path;
        $newFile->task_id = $taskId;
        $newFile->save(false);
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
     */
    public function rules(): array
    {
        return [
            [['task_id', 'path'], 'required'],
            [['task_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['path'], 'string', 'max' => 255],
            [['path'], 'unique'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
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
            'task_id' => 'Task ID',
            'path' => 'Path',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Получает запрос для [[Task]].
     *
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }
}
