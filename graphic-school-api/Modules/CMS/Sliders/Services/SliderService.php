<?php

namespace Modules\CMS\Sliders\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\Sliders\Models\Slider;
use Modules\CMS\Sliders\Repositories\Interfaces\SliderRepositoryInterface;

class SliderService
{
    public function __construct(private SliderRepositoryInterface $sliderRepository)
    {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->sliderRepository->paginateWithFilters($filters, $perPage);
    }

    public function create(array $data, UploadedFile $image): Slider
    {
        $data['image_path'] = $image->store('sliders', 'public');
        unset($data['image']);

        return $this->sliderRepository->create($data);
    }

    public function update(Slider $slider, array $data, ?UploadedFile $image = null): Slider
    {
        if ($image) {
            Storage::disk('public')->delete($slider->image_path);
            $data['image_path'] = $image->store('sliders', 'public');
        }

        unset($data['image']);

        return $this->sliderRepository->update($slider, $data);
    }

    public function delete(Slider $slider): void
    {
        Storage::disk('public')->delete($slider->image_path);
        $this->sliderRepository->delete($slider);
    }
}

