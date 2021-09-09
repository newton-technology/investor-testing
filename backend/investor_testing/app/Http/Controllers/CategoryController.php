<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:07
 */

namespace Newton\InvestorTesting\Http\Controllers;

use Throwable;

use Common\Base\Http\Request;
use Common\Base\Http\Response;
use Newton\InvestorTesting\Packages\Common\CategoryItemRepository;
use Newton\InvestorTesting\Packages\Common\Exception\ExceptionWithoutReport;

use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * @throws Throwable
     */
    public function getCategories(Request $request, CategoryItemRepository $categoryItemRepository): JsonResponse
    {
        return Response::success(
            array_map(
                fn($item) => $item->toResponse(),
                $categoryItemRepository->getCategoryItems($request->getUserId())
            )
        );
    }

    /**
     * @throws Throwable
     */
    public function getCategory(Request $request, CategoryItemRepository $categoryItemRepository): JsonResponse
    {
        $validatedInput = $this->validateRoute(
            $request,
            [
                'id' => 'required|int|min:1',
            ]
        );

        $categoryItem = $categoryItemRepository->getCategoryItem($request->getUserId(), $validatedInput['id']);
        if (empty($categoryItem)) {
            throw ExceptionWithoutReport::categoryNotFound();
        }

        return Response::success($categoryItem->toResponse());
    }

    /**
     * @throws Throwable
     */
    public function getCategoryByCode(Request $request, CategoryItemRepository $categoryItemRepository): JsonResponse
    {
        $validatedInput = $this->validateRoute(
            $request,
            [
                'code' => 'required|string'
            ]
        );

        $categoryItem = $categoryItemRepository->getCategoryItemByCode($request->getUserId(), $validatedInput['code']);
        if (empty($categoryItem)) {
            throw ExceptionWithoutReport::categoryNotFound();
        }

        return Response::success($categoryItem->toResponse());
    }
}
