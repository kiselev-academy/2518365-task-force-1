<?php

namespace app\models\forms;

use app\models\Response;
use Yii;
use yii\base\Model;
use yii\db\Exception;

class NewResponseForm extends Model
{
    public string $comment = '';
    public string $price = '';

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'comment' => 'Ваш комментарий',
            'price' => 'Стоимость',
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
            [['comment', 'price'], 'safe'],
            [['price'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            [['comment', 'price'], 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * Создает и сохраняет новый отклик.
     *
     * @param int $taskId ID задачи.
     * @return bool Возвращает true в случае успешного создания отклика, иначе false.
     * @throws Exception
     */
    public function createResponse(int $taskId): bool
    {
        if ($this->validate()) {
            $newResponse = $this->newResponse($taskId);
            $newResponse->save(false);
            return true;
        }
        return false;
    }

    /**
     * Создает новый экземпляр отклика.
     *
     * @param int $taskId ID задачи.
     * @return Response Экземпляр отклика.
     */
    protected function newResponse(int $taskId): Response
    {
        $response = new Response;
        $response->comment = $this->comment;
        $response->price = $this->price;
        $response->task_id = $taskId;
        $response->executor_id = Yii::$app->user->getId();
        return $response;
    }
}