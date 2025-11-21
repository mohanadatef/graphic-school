<?php

namespace Modules\LMS\CourseReviews\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Modules\LMS\CourseReviews\Repositories\Interfaces\CourseReviewRepositoryInterface;

class CourseReviewRepository extends BaseRepository implements CourseReviewRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new CourseReview();
    }

    public function updateOrCreate(array $attributes, array $values): CourseReview
    {
        return $this->model->newQuery()->updateOrCreate($attributes, $values);
    }
}

