<?php

declare(strict_types=1);

namespace Newton\InvestorTesting\Packages\Common;

use Illuminate\Mail\Mailable;

class TestResultMail extends Mailable
{
    /**
     * Описание категории теста
     */
    protected string $categoryDescription;

    /**
     * Статус прохождения теста
     */
    protected string $testStatus;

    /**
     * Наименование брокера
     */
    protected string $brokerName;

    public function __construct(
        string $categoryDescription,
        string $testStatus,
        string $brokerName
    ) {
        $this->testStatus = $testStatus;
        $this->categoryDescription = $categoryDescription;
        $this->brokerName = $brokerName;
    }

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
