<?php

namespace Common\Packages\Http\Controllers;

use Throwable;

use Common\Base\Http\Response;
use Common\Base\Utils\Composer;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Class SupportController
 * @package Newton\AccountCreation\Http\Controllers
 */
class SupportControllerLaravel extends Controller
{
    use ValidatesRequests;

    /**
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function ping(Request $request)
    {
        $validatedInput = $this->validate($request, ['log' => 'boolean']);
        if ($validatedInput['log'] ?? false) {
            Log::channel('support')->info('ping');
            Log::info('ping');
        }

        return Response::success(
            [
                'ping' => 'pong',
            ]
        );
    }

    /**
     * Версия приложения
     *
     * @response {
     *   "name": "account_creation",
     *   "version": "1.44.0",
     *   "stage": "DEV"
     * }
     *
     * @return JsonResponse
     */
    public function version()
    {
        return Response::success(
            [
                'name' => Composer::getApplicationShortName(),
                'version' => Composer::getApplicationVersion(),
                'stage' => Composer::getApplicationStage(),
            ]
        );
    }
}
