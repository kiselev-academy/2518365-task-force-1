<?php

namespace app\models\widgets;

use yii\base\Widget;

class RatingWidget extends Widget
{
    public float $rating = 0;
    public int $maxStars = 5;

    public function run(): string
    {
        $count = round($this->rating);
        $filledStars = str_repeat('<span class="fill-star">&nbsp;</span>', $count);
        $emptyStars = str_repeat('<span>&nbsp;</span>', $this->maxStars - $count);
        return $filledStars . $emptyStars;
    }
}