<?php

namespace Modules\CMS\Sliders\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\CMS\Sliders\Http\Requests\Admin\ListSliderRequest;
use Modules\CMS\Sliders\Http\Requests\Admin\StoreSliderRequest;
use Modules\CMS\Sliders\Http\Requests\Admin\UpdateSliderRequest;
use Modules\CMS\Sliders\Http\Resources\SliderResource;
use Modules\CMS\Sliders\Models\Slider;
use Modules\CMS\Sliders\Services\SliderService;

class SliderController extends Controller
{
    public function __construct(private SliderService $sliderService)
    {
    }

    public function index(ListSliderRequest $request)
    {
        return SliderResource::collection(
            $this->sliderService->paginate(
                $request->validated(),
                $request->integer('per_page', 10)
            )
        );
    }

    public function store(StoreSliderRequest $request)
    {
        $slider = $this->sliderService->create($request->validated(), $request->file('image'));
        $slider->load('translations');

        return SliderResource::make($slider)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Slider $slider)
    {
        return SliderResource::make($slider);
    }

    public function update(UpdateSliderRequest $request, Slider $slider)
    {
        $slider = $this->sliderService->update($slider, $request->validated(), $request->file('image'));
        $slider->load('translations');

        return SliderResource::make($slider);
    }

    public function destroy(Slider $slider)
    {
        $this->sliderService->delete($slider);

        return response()->json(['message' => 'Deleted']);
    }
}
