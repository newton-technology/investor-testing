<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.08.2021
 * Time: 21:46
 */

namespace Newton\InvestorTesting\Http\Controllers\Management;

use Throwable;

use Common\Base\Http\JsonResponse;
use Common\Base\Http\Request;
use Common\Base\Http\Response;
use Common\Base\Utils\DateTimeUtils;
use Common\Base\Utils\TransformationUtils;
use Newton\InvestorTesting\Http\Controllers\Controller;
use Newton\InvestorTesting\Packages\Common\Test;
use Newton\InvestorTesting\Packages\Management\TestRepository;

class TestController extends Controller
{
    /**
     * @throws Throwable
     */
    public function getTests(Request $request, TestRepository $testRepository): JsonResponse
    {
        $allowedSort = [
            'updatedAt,asc',
            'updatedAt,desc',
            'createdAt,asc',
            'createdAt,desc',
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
            $filters[] = ['created_at', '>=', DateTimeUtils::fromTimestamp($validatedInput['dateStart'])];
        }

        if (isset($validatedInput['dateEnd'])) {
            $filters[] = ['created_at', '<=', DateTimeUtils::fromTimestamp($validatedInput['dateEnd'])];
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

        return Response::success(
            array_map(
                fn($test) => $test->toResponse(),
                $testRepository->getTests($filters, $limit, $offset, $orderBy),
            )
        );
    }
}