<?php

namespace app\models\forms;

use app\models\Category;
use app\models\City;
use app\models\File;
use app\models\Task;
use app\services\FileUploader;
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
            ['location', \app\validators\LocationValidator::class],
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
        $uploader = new FileUploader();

        if ($files) {
            foreach ($files as $file) {
                $savedPath = $uploader->upload($file);
                if ($savedPath) {
                    File::saveFile($savedPath, $newTask->id);
                }
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
    protected function newTask(): Task
    {
        $task = new Task;
        $task->title = $this->title;
        $task->description = $this->description;
        $task->category_id = $this->category;
        [$location, $cityId, $longitude, $latitude] = $this->getLocation();
        $task->location = $location;
        $task->city_id = $cityId;
        $task->longitude = $longitude;
        $task->latitude = $latitude;
        $task->budget = $this->budget;
        $task->deadline = $this->deadline;
        $task->status = TaskBasic::STATUS_NEW;
        $task->customer_id = Yii::$app->user->getId();
        return $task;
    }

    /**
     * Создает массив с локацией.
     *
     * @return array Массив с локацией.
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    protected function getLocation(): array
    {
        if (!$this->location) {
            return [null, null, null, null];
        }

        $locationData = Geocoder::getLocationData($this->location);

        if (!isset($locationData['city'])) {
            return [null, null, null, null];
        }
        $city = City::findOne(['name' => $locationData['city']]);
        if (!$city) {
            return [null, null, null, null];
        }

        if (!isset($locationData['address'])) {
            return [null, null, null, null];
        }

        if (!isset($locationData['coordinates'])) {
            return [null, null, null, null];
        }

        return [$locationData['address'], $city->id, $locationData['coordinates'][0], $locationData['coordinates'][1]];
    }
}