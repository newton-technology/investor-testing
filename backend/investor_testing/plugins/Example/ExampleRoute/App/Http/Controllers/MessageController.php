<?php

namespace Plugins\Example\ExampleRoute\App\Http\Controllers;

use Throwable;

use Common\Base\Http\Request;
use Common\Base\Http\Response;
use Newton\InvestorTesting\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * @throws Throwable
     */
    public function getWelcomeMessage(Request $request): JsonResponse
    {
        $validatedInput = $this->validate(
            $request,
            [
                'name' => 'required|string',
            ]
        );

        return Response::success(
            [
                'message' => config('example.hello.world') . ' ' . $validatedInput['name'] . '!',
            ]
        );
    }
}
