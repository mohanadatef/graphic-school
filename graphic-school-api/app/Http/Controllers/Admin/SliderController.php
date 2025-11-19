<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\ListSliderRequest;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use App\Services\SliderService;

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

        return SliderResource::make($slider);
    }

    public function destroy(Slider $slider)
    {
        $this->sliderService->delete($slider);

        return response()->json(['message' => 'Deleted']);
    }
}
