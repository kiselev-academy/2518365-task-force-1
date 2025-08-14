<?php

declare(strict_types=1);

namespace TaskForce\Enums;

enum Action: string
{
    case Cancel = 'cancel';
    case Done = 'get done';
    case Respond = 'respond';

    /** Функция для возврата «карты» действия
     * @return string[] возвращает массив с названием действий
     */
    public function getActionMap(): array
    {
        return [
            self::Cancel->value => 'Отменить',
            self::Done->value => 'Выполнено',
            self::Respond->value => 'Откликнуться',
        ];
    }
}
