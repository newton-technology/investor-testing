<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:50
 */

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ResponseableTrait;

class TestItemQuestion
{
    use ResponseableTrait;

    /**
     * Идентификатор вопроса
     */
    protected int $id;

    /**
     * Текст вопроса
     */
    protected string $question;

    /**
     * Минимальное количество правильных ответов
     */
    protected int $answersCountToChooseMin;

    /**
     * Максимальное количество правильных ответов
     */
    protected ?int $answersCountToChooseMax;

    /**
     * Варианты ответов
     * @var TestItemQuestionAnswer[]
     */
    protected array $answers = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TestItemQuestion
     */
    public function setId(int $id): TestItemQuestion
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return TestItemQuestion
     */
    public function setQuestion(string $question): TestItemQuestion
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnswersCountToChooseMin(): int
    {
        return $this->answersCountToChooseMin;
    }

    /**
     * @param int $answersCountToChooseMin
     * @return TestItemQuestion
     */
    public function setAnswersCountToChooseMin(int $answersCountToChooseMin): TestItemQuestion
    {
        $this->answersCountToChooseMin = $answersCountToChooseMin;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAnswersCountToChooseMax(): ?int
    {
        return $this->answersCountToChooseMax;
    }

    /**
     * @param int|null $answersCountToChooseMax
     * @return TestItemQuestion
     */
    public function setAnswersCountToChooseMax(?int $answersCountToChooseMax): TestItemQuestion
    {
        $this->answersCountToChooseMax = $answersCountToChooseMax;
        return $this;
    }

    /**
     * @return TestItemQuestionAnswer[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @param TestItemQuestionAnswer[] $answers
     * @return TestItemQuestion
     */
    public function setAnswers(array $answers): TestItemQuestion
    {
        $this->answers = $answers;
        return $this;
    }

    /**
     * @param TestItemQuestionAnswer $answer
     * @return TestItemQuestion
     */
    public function addAnswer(TestItemQuestionAnswer $answer): TestItemQuestion
    {
        $this->answers[] = $answer;
        return $this;
    }
}
