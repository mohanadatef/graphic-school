<?php

namespace Modules\CMS\Sliders\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\Sliders\Models\Slider;
use Modules\CMS\Sliders\Repositories\Interfaces\SliderRepositoryInterface;
use App\Services\EntityTranslationService;

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
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $data['image_path'] = $image->store('sliders', 'public');
        unset($data['image']);

        $slider = $this->sliderRepository->create($data);

        // Save translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($slider, $translations);
        }

        return $slider;
    }

    public function update(Slider $slider, array $data, ?UploadedFile $image = null): Slider
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        if ($image) {
            Storage::disk('public')->delete($slider->image_path);
            $data['image_path'] = $image->store('sliders', 'public');
        }

        unset($data['image']);

        $slider = $this->sliderRepository->update($slider, $data);

        // Update translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($slider, $translations);
        }

        return $slider;
    }

    public function delete(Slider $slider): void
    {
        // Delete translations
        $translationService = app(EntityTranslationService::class);
        $translationService->deleteTranslations($slider);

        Storage::disk('public')->delete($slider->image_path);
        $this->sliderRepository->delete($slider);
    }
}

