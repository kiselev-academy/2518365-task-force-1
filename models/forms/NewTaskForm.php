<?php

namespace app\models\forms;

use app\models\Category;
use app\models\City;
use app\models\File;
use app\models\Task;
use app\services\Geocoder;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\Model;
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

    public function rules(): array
    {
        return [
            [['title', 'description'], 'required'],
            [['category'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => ['category' => 'id']],
            ['budget', 'integer', 'min' => 1],
            [['deadline'], 'date', 'format' => 'php:Y-m-d'],
            [['deadline'], 'compare', 'compareValue' => date('Y-m-d'),
                'operator' => '>', 'type' => 'date',
                'message' => 'Срок выполнения не может быть в прошлом'],
            [['files'], 'file', 'maxFiles' => 0],
            ['location', \app\models\validators\LocationValidator::class],
        ];
    }

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

}