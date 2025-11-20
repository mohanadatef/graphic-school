<?php

namespace Modules\LMS\CourseReviews\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Modules\LMS\CourseReviews\Models\CourseReview;

interface CourseReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): CourseReview;
}

