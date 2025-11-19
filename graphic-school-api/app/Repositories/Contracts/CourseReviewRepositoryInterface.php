<?php

namespace App\Repositories\Contracts;

use App\Models\CourseReview;

interface CourseReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): CourseReview;
}


