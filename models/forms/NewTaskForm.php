<?php

namespace app\models\forms;

use app\models\Category;
use app\models\City;
use app\models\File;
use app\models\Task;
use app\services\Geocoder;
use GuzzleHttp\Exception\GuzzleException;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\UploadedFile;

class NewTaskForm extends Model
{
    public string $title = '';
    public string $description = '';
    public string $category = '';
    public string $location = '';
    public string $budget = '';
    public string $deadline = '';
    public array $files = [];

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Опишите суть работы',
            'description' => 'Подробности задания',
            'category' => 'Категория',
            'location' => 'Локация',
            'budget' => 'Бюджет',
            'deadline' => 'Срок исполнения',
            'files' => 'Файлы',
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
            [['title', 'description', 'category'], 'required'],
            [['category'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => ['category' => 'id']],
            ['budget', 'integer', 'min' => 1],
            ['title', 'string', 'min' => 10],
            ['description', 'string', 'min' => 30],
            [['deadline'], 'date', 'format' => 'php:Y-m-d'],
            [['deadline'], 'compare', 'compareValue' => date('Y-m-d'),
                'operator' => '>', 'type' => 'date',
                'message' => 'Срок выполнения не может быть в прошлом'],
            [['files'], 'file', 'maxFiles' => 0],
            [['title', 'description', 'category', 'location', 'budget', 'deadline'], 'filter', 'filter' => 'strip_tags'],
            ['location', \app\models\validators\LocationValidator::class],
        ];
    }

    /**
     * Создает и сохраняет новую задачу, основанную на данных формы.
     *
     * @return int|bool Возвращает ID созданной задачи, если задача успешно создана и сохранена, иначе false.
     * @throws GuzzleException
     * @throws \JsonException
     * @throws Exception
     */
    public function createTask(): false|int
    {
        $files = UploadedFile::getInstances($this, 'files');

        if (!$this->validate()) {
            return false;
        }
        $newTask = $this->newTask();
        $newTask->save(false);
        if ($files) {
            foreach ($files as $file) {
                $newFileName = uniqid('upload') . '.' . $file->getExtension();
                $file->saveAs('@webroot/uploads/' . $newFileName);
                $filePath = '/uploads/' . $newFileName;
                File::saveFile($filePath, $newTask->id);
            }
        }
        return $newTask->id;
    }

    /**
     * Создает новый объект задачи.
     *
     * @return Task Новый объект задачи.
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function newTask(): Task
    {
        $task = new Task;
        $task->title = $this->title;
        $task->description = $this->description;
        $task->category_id = $this->category;

        if ($this->location) {
            $locationData = Geocoder::getLocationData($this->location);

            $task->city_id = City::findOne(['name' => $locationData['city']])->id;
            $task->location = $locationData['address'];
            $task->longitude = $locationData['coordinates'][0];
            $task->latitude = $locationData['coordinates'][1];
        }

        $task->budget = $this->budget;
        $task->deadline = $this->deadline;
        $task->status = TaskBasic::STATUS_NEW;
        $task->customer_id = Yii::$app->user->getId();
        return $task;
    }

}