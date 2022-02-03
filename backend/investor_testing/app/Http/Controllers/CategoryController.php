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
use Newton\InvestorTesting\Packages\Common\Category;
use Newton\InvestorTesting\Packages\Common\CategoryRepository;

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

    /**
     * Добавление категории
     *
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @throws Throwable
     */
    public function addCategory(Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        $validatedInput = $this->validate(
            $request,
            [
                'code' => 'required|string',
                'name' => 'required|string',
                'description' => 'string',
                'status' => 'string|in:' . implode(',', Category::getAvailableStatuses()),
                'descriptionShort' => 'string',
            ]
        );

        if ($categoryRepository->getCategoryByCode($validatedInput['code']) !== null) {
            return Response::unprocessableEntity('there is a category with the same code');
        }

        $category = (new Category())
            ->setCode($validatedInput['code'])
            ->setName($validatedInput['name'])
            ->setDescription($validatedInput['description'] ?? null)
            ->setStatus($validatedInput['status'] ?? Category::STATUS_DISABLED)
            ->setDescriptionShort($validatedInput['descriptionShort'] ?? null);

        $categoryRepository->addCategory($category);

        return Response::success($category->toResponse());
    }

    /**
     * Редактирование категории
     *
     * @param int $id
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @throws Throwable
     */
    public function editCategory(int $id, Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        $validatedInput = $this->validate(
            $request,
            [
                'code' => 'string',
                'name' => 'string',
                'description' => 'string',
                'status' => 'string|in:' . implode(',', Category::getAvailableStatuses()),
                'descriptionShort' => 'string',
            ]
        );
        $category = $categoryRepository->getCategoryById($id);

        if ($category === null) {
            throw ExceptionWithoutReport::categoryNotFound();
        }

        if (array_key_exists('code', $validatedInput) && ($categoryRepository->getCategoryByCode($validatedInput['code']) !== null)) {
            return Response::unprocessableEntity('there is a category with the same code');
        }

        $category
            ->setCode($validatedInput['code'] ?? $category->getCode())
            ->setName($validatedInput['name'] ?? $category->getName())
            ->setDescription($validatedInput['description'] ?? $category->getDescription())
            ->setStatus($validatedInput['status'] ?? $category->getStatus())
            ->setDescriptionShort($validatedInput['descriptionShort'] ?? $category->getDescriptionShort());

        $categoryRepository->updateCategory($category);

        return Response::success($category->toResponse());
    }
}
