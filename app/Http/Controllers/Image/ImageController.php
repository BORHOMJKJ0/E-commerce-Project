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
        return $this->imageService->getAllImages($request);
    }

    public function MyImages(Request $request): JsonResponse
    {
        return $this->imageService->getMyImages($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->imageService->createImage($request->all(), $request);
    }

    public function show(Image $image): JsonResponse
    {
        return $this->imageService->getImageById($image);
    }

    public function orderBy($column, $direction, Request $request): JsonResponse
    {
        return $images = $this->imageService->getImagesOrderedBy($column, $direction, $request);
    }

    public function MyImagesOrderBy($column, $direction, Request $request): JsonResponse
    {
        return $this->imageService->getMyImagesOrderedBy($column, $direction, $request);
    }

    public function update(Image $image, Request $request): JsonResponse
    {
        return $this->imageService->updateImage($image, $request->all());
    }

    public function destroy(Image $image): JsonResponse
    {
        return $this->imageService->deleteImage($image);
    }
}
