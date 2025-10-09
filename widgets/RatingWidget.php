<?php

namespace app\widgets;

use yii\base\Widget;

class RatingWidget extends Widget
{
    public float $rating = 0;

    public function run(): string
    {
        $count = (int)round($this->rating);
        $filledStars = "<span class=\"fill-star\">&nbsp;</span>";
        $emptyStars = "<span>&nbsp;</span>";

        return str_repeat($filledStars, $count) . str_repeat($emptyStars, 5 - $count);
    }
}