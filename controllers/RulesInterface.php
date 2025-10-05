<?php

namespace app\controllers;

interface RulesInterface
{
    /**
     * Метод, определяющий правила доступа для пользователей.
     *
     * @return array Массив правил доступа.
     */
    public function getRules(): array;
}