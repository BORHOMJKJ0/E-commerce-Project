<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->middleware('auth:api');
        $this->imageService = $imageService;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->imageService->getAllimages($request);
    }

    public function MyImages(Request $request): JsonResponse
    {
        return $this->imageService->getMyImages($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->imageService->createimage($request->all());
    }

    public function show(Image $image): JsonResponse
    {
        return $this->imageService->getimageById($image);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $images = $this->imageService->getimagesOrderedBy($column, $direction, $request);
    }

    public function MyImagesOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->imageService->getMyImagesOrderedBy($column, $direction, $request);
    }

    public function update(Request $request, Image $image): JsonResponse
    {
        return $this->imageService->updateimage($image, $request->all());
    }

    public function destroy(Image $image): JsonResponse
    {
        return $this->imageService->deleteimage($image);
    }
}
