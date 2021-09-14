<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 11.08.2021
 * Time: 18:29
 */

namespace Newton\InvestorTesting\Packages\Common;

class TestQuestionResultHandler
{
    protected const RESULT_PASSED = 'passed';
    protected const RESULT_FAILED = 'failed';

    protected TestQuestion $testQuestion;
    protected int $answersCount = 0;
    protected bool $hasCorrect = false;

    protected ?string $result = null;

    public function __construct(TestQuestion $testQuestion)
    {
        $this->testQuestion = $testQuestion;
    }

    public function handleAnswer(TestAnswer $testAnswer): self
    {
        if ($testAnswer->isSelected()) {
            $this->answersCount++;
        }

        if ($testAnswer->isCorrect()) {
            $this->hasCorrect = true;
        }

        if ($this->result === self::RESULT_FAILED) {
            return $this;
        }

        if ($testAnswer->isSelected() && $testAnswer->isCorrect()) {
            $this->result = self::RESULT_PASSED;
            return $this;
        }

        if ($testAnswer->isSelected() && !$testAnswer->isCorrect()) {
            $this->result = self::RESULT_FAILED;
            return $this;
        }

        if (!$testAnswer->isSelected() && $testAnswer->isCorrect()) {
            $this->result = self::RESULT_FAILED;
            return $this;
        }

        return $this;
    }

    public function isComplete(): bool
    {
        return ($this->result !== null || !$this->hasCorrect) && $this->isEnoughAnswers();
    }

    public function isPassed(): bool
    {
        return ($this->result === self::RESULT_PASSED || !$this->hasCorrect) && $this->isEnoughAnswers();
    }

    public function getWeight(): int
    {
        return $this->testQuestion->getQuestionWeight();
    }

    protected function isEnoughAnswers(): bool
    {
        return ($this->answersCount >= $this->testQuestion->getAnswersCountToChooseMin())
            && ($this->answersCount <= ($this->testQuestion->getAnswersCountToChooseMax() ?? INF));
    }
}
