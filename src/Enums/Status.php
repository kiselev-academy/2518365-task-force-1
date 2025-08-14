<?php

declare(strict_types=1);

namespace TaskForce\Enums;

enum Status: string
{
    case New = 'new';
    case Cancelled = 'cancelled';
    case Work = 'work';
    case Done = 'done';
    case Failed = 'failed';

    /** Функция для возврата «карты» статусов
     * @return string[] возвращает массив с названием статусов
     */
    public function getStatusMap(): array
    {
        return [
            self::New->value => 'Новое',
            self::Cancelled->value => 'Отменено',
            self::Work->value => 'В работе',
            self::Done->value => 'Выполнено',
            self::Failed->value => 'Провалено'
        ];
    }
}
