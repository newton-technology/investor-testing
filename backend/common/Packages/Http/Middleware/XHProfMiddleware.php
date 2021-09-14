<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 15.04.2020
 * Time: 13:57
 */

namespace Common\Packages\Http\Middleware;

use Closure;
use LogicException;

use Illuminate\Http\Request;

/**
 * Class XHProf
 *
 * Этот middleware позволяет анализировать время выполнения запроса
 *
 * Для работы необходимо выполнить установку:
 *
 * 1. Склонировать репозиторий https://github.com/longxinH/xhprof
 * 2. Установить xhprof через `pecl install xhprof` или `phpbrew ext install xhprof`
 * 3. Задать директорию для хранения данных профайлинга через переменную PHP `xhprof.output_dir`
 * 4. Установить утилиту для рисования графов, к примеру Graphviz
 * 5. Задать в .env следующие параметры конфигурации:
 *  - XHPROF_ENABLED=true
 *  - XHPROF_ROOT={путь_к_склонированному_репо}
 * 6. Настроить локальный web-сервер для просмотра отчетов, указав в качестве document_root
 *  `{путь_к_склонированному_репо}/xhprof_html`. Порт по умолчанию - 8084.
 *  Имя хоста и порт можно также задать через параметр конфигурации `XHPROF_URL`
 *
 * После конфигурации можно выполнять запрос к API, добавив хедер `X-Debug-XHProf: 1`
 * В заголовке ответа `X-XHProf-Report` будет содержаться ссылка на отчет.
 * Отчет позволяет смотреть время выполнения функций, нагрузку каждой функции на CPU,
 * анализировать использование памяти во время выполнения, смотреть граф вызовов функций.
 *
 * @package Common\Http\Middleware
 */
class XHProfMiddleware
{
    private $headerName = 'X-XHProf-Report';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isAllowed($request)) {
            return $next($request);
        }

        $this->beforeHandle();
        $response = $next($request);
        $this->afterHandle();

        return $response;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isAllowed(Request $request)
    {
        return config('xhprof.enabled') === true &&
            $request->headers->get('x-debug-xhprof') === '1';
    }

    private function beforeHandle() {
        if (empty(config('xhprof.root'))) {
            throw new LogicException('missing xhprof root');
        }
        \xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    }

    /**
     * @noinspection PhpIncludeInspection
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpUndefinedMethodInspection
     */
    private function afterHandle() {
        $xhprofData = \xhprof_disable();

        $xhprofRoot = config('xhprof.root');
        include_once "{$xhprofRoot}/xhprof_lib/utils/xhprof_lib.php";
        include_once "{$xhprofRoot}/xhprof_lib/utils/xhprof_runs.php";

        $xhprofReportUrl = config('xhprof.report.url');
        $xhprofReportName = config('xhprof.report.name');
        if (config('xhprof.report.timestamp') === true) {
            $xhprofReportName .= '_' . time();
        }

        $xhprofRuns = new \XHProfRuns_Default();
        $runId = $xhprofRuns->save_run($xhprofData, $xhprofReportName);

        header("{$this->headerName}: {$xhprofReportUrl}?run={$runId}&source={$xhprofReportName}");
    }
}
