<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:59
 */

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ResponseableTrait;

class TestItemQuestionAnswer
{
    use ResponseableTrait;

    /**
     * Идентификатор ответа
     */
    protected int $id;

    /**
     * Текст ответа
     */
    protected string $answer;

    /**
     * Ответ выбран пользователем
     */
    protected bool $selected;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TestItemQuestionAnswer
     */
    public function setId(int $id): TestItemQuestionAnswer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     * @return TestItemQuestionAnswer
     */
    public function setAnswer(string $answer): TestItemQuestionAnswer
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @param bool $selected
     * @return TestItemQuestionAnswer
     */
    public function setSelected(bool $selected): TestItemQuestionAnswer
    {
        $this->selected = $selected;
        return $this;
    }
}
