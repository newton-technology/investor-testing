<?php

declare(strict_types=1);

namespace Plugins\Example\ExampleView\App\Packages\Email;

use Newton\InvestorTesting\Packages\Common\TestResultMail as CommonTestResultMail;

class TestResultMail extends CommonTestResultMail
{
    public function build(): self
    {
        return $this->view('emails.test_result')
            ->subject('Уведомление о результате тестирования')
            ->with(
                [
                    'categoryDescription' => $this->categoryDescription,
                    'testStatus' => $this->testStatus,
                    'brokerName' => $this->brokerName
                ]
            );
    }
}
