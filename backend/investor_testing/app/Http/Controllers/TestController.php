<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:44
 */

namespace Newton\InvestorTesting\Http\Controllers;

use Throwable;

use Common\Base\Http\Request;
use Common\Base\Http\Response;
use Newton\InvestorTesting\Packages\Common\TestItemService;

use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * @throws Throwable
     */
    public function addTest(Request $request, TestItemService $testItemService): JsonResponse
    {
        $validatedInput = $this->validate(
            $request,
            [
                'categoryId' => 'required|int|min:1',
            ]
        );

        return Response::success(
            $testItemService
                ->generateTestItem($request->getUserId(), $validatedInput['categoryId'])
                ->toResponse()
        );
    }

    /**
     * @throws Throwable
     */
    public function getTest(Request $request, TestItemService $testItemService): JsonResponse
    {
        $validatedInput = $this->validateRoute(
            $request,
            [
                'id' => 'required|int|min:1'
            ]
        );

        return Response::success(
            $testItemService
                ->getTestItem($request->getUserId(), $validatedInput['id'])
                ->toResponse()
        );
    }

    /**
     * @throws Throwable
     */
    public function addTestAnswers(
        Request $request,
        TestItemService $testItemService
    ): JsonResponse {
        $validatedRouteInput = $this->validateRoute(
            $request,
            [
                'id' => 'required|int',
            ]
        );

        $validatedBodyInput = $this->validate(
            $request,
            [
                'answers' => 'required|array',
                'answers.*' => 'required|int',
            ]
        );

        return Response::success(
            $testItemService
                ->addTestAnswers($request->getUserId(), $validatedRouteInput['id'], $validatedBodyInput['answers'])
                ->toResponse()
        );
    }
}
