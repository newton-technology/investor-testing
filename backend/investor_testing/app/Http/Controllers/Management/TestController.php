<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.08.2021
 * Time: 21:46
 */

namespace Newton\InvestorTesting\Http\Controllers\Management;

use Throwable;

use Common\Base\Http\Headers;
use Common\Base\Http\JsonResponse;
use Common\Base\Http\Request;
use Common\Base\Http\Response;
use Common\Base\Utils\DateTimeUtils;
use Common\Base\Utils\TransformationUtils;
use Newton\InvestorTesting\Http\Controllers\Controller;
use Newton\InvestorTesting\Packages\Common\Test;
use Newton\InvestorTesting\Packages\Management\TestItemService;
use Newton\InvestorTesting\Packages\Management\TestListRepository;

class TestController extends Controller
{
    /**
     * @throws Throwable
     */
    public function getTests(Request $request, TestListRepository $testListRepository): JsonResponse
    {
        $allowedSort = [
            'completedAt,asc',
            'completedAt,desc',
            'createdAt,asc',
            'createdAt,desc',
            'updatedAt,asc',
            'updatedAt,desc'
        ];

        $validatedInput = $this->validate(
            $request,
            [
                'status' => 'array',
                'status.*' => 'string|in:' . implode(',', Test::getAvailableStatuses()),
                'dateStart' => 'int',
                'dateEnd' => 'int',
                'email' => ['string', 'regex:/^[A-Za-zА-Яа-я0-9\!\#\$%\&\'\*\+\-\/\=\?\^_`\{\|\}~.@]+$/'],
                'limit' => 'int|min:1|max:100',
                'offset' => 'int|min:0',
                'sort' => 'array',
                'sort.*' => ['string', 'regex:/^(' . implode('|', $allowedSort) . ')$/'],
            ]
        );

        $filters = [];
        if (isset($validatedInput['status'])) {
            $filters[] = ['status', 'in', $validatedInput['status']];
        }

        if (isset($validatedInput['dateStart'])) {
            $filters[] = ['completed_at', '>=', DateTimeUtils::fromTimestamp($validatedInput['dateStart'])];
        }

        if (isset($validatedInput['dateEnd'])) {
            $filters[] = ['completed_at', '<=', DateTimeUtils::fromTimestamp($validatedInput['dateEnd'])];
        }

        if (isset($validatedInput['email'])) {
            $filters[] = ['email', 'like', $validatedInput['email']];
        }

        $orderBy = [];
        if (isset($validatedInput['sort'])) {
            foreach ($validatedInput['sort'] as $item) {
                $orderBy[] = explode(',', TransformationUtils::stringCamelCaseToUnderScore($item));
            }
        }

        $limit = $validatedInput['limit'] ?? 20;
        $offset = $validatedInput['offset'] ?? 0;

        $testsCount = $testListRepository->getTestsCount($filters);

        $response = Response::success()
            ->header(Headers::X_LIST_LIMIT, $limit)
            ->header(Headers::X_LIST_OFFSET, $offset)
            ->header(Headers::X_LIST_TOTAL, $testsCount);

        if ($testsCount === 0) {
            return $response;
        }

        return $response->setData(
            array_map(
                fn($test) => $test->toResponse(),
                $testListRepository->getTests($filters, $limit, $offset, $orderBy),
            )
        );
    }

    /**
     * @throws Throwable
     */
    public function getTestItemById(Request $request, TestItemService $testItemService): JsonResponse
    {
        $validatedInput = $this->validateRoute(
            $request,
            [
                'id' => 'required|int|min:1',
            ]
        );
        return Response::success(
            $testItemService->getTestById($validatedInput['id'])
                ->toResponse(),
        );
    }
}
