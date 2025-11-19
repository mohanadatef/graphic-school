<?php

namespace App\Repositories\Eloquent;

use App\Models\CourseReview;
use App\Repositories\Contracts\CourseReviewRepositoryInterface;

class CourseReviewRepository extends BaseRepository implements CourseReviewRepositoryInterface
{
    public function __construct(CourseReview $model)
    {
        parent::__construct($model);
    }

    public function updateOrCreate(array $attributes, array $values): CourseReview
    {
        return $this->model->newQuery()->updateOrCreate($attributes, $values);
    }
}


