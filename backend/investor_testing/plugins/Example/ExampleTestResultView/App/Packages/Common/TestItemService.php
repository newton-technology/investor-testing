<?php

declare(strict_types=1);

namespace Plugins\Example\ExampleTestResultView\App\Packages\Common;

use Newton\InvestorTesting\Packages\Common\Test;
use Newton\InvestorTesting\Packages\Common\TestItemService as CommonTestItemService;
use Newton\InvestorTesting\Packages\Common\TestResultMail;
use Newton\InvestorTesting\Packages\Common\UserRepository;

use Illuminate\Support\Facades\Mail;

class TestItemService extends CommonTestItemService
{
    /**
     * Отправляет пользователю письмо с результатом о
     * прохождении тестирования
     *
     * @param Test $test
     */
    protected function notification(Test $test): void
    {
        if (in_array(
            $test->getStatus(),
            [
                Test::STATUS_PASSED,
                Test::STATUS_FAILED
            ]
        )) {
            Mail::to($this->userRepository->getUserById($test->getUserId())->getEmail())
                ->send(
                    new TestResultMail(
                        $this->categoryRepository
                            ->getCategoryById($test->getCategoryId())
                            ->getDescription(),
                        $test->getStatus(),
                        config('broker.test_result.broker_name')
                    )
                );
        }
    }
}
