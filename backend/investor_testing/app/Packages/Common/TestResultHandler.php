<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 11.08.2021
 * Time: 18:18
 */

namespace Newton\InvestorTesting\Packages\Common;

class TestResultHandler
{
    public const RESULT_PASSED = 'passed';
    public const RESULT_FAILED = 'failed';
    public const RESULT_INCOMPLETE = 'incomplete';

    /**
     * @var TestQuestionResultHandler[]
     */
    protected array $testQuestionResultHandlers = [];

    /**
     * @param TestQuestion[] $testQuestions
     */
    public function handleQuestions(array $testQuestions): self
    {
        foreach ($testQuestions as $testQuestion) {
            $this->testQuestionResultHandlers[$testQuestion->getId()] = new TestQuestionResultHandler($testQuestion);
        }
        return $this;
    }

    /**
     * @param TestAnswer[] $testAnswers
     */
    public function handleAnswers(array $testAnswers): self
    {
        foreach ($testAnswers as $testAnswer) {
            $this->testQuestionResultHandlers[$testAnswer->getTestQuestionId()]->handleAnswer($testAnswer);
        }
        return $this;
    }

    public function getResult(): string
    {
        $result = null;
        foreach ($this->testQuestionResultHandlers as $testQuestionResultHandler) {
            if (!$testQuestionResultHandler->isComplete()) {
                return self::RESULT_INCOMPLETE;
            }
            if (!$testQuestionResultHandler->isPassed()) {
                $result = self::RESULT_FAILED;
            }
        }

        return $result ?? self::RESULT_PASSED;
    }
}
