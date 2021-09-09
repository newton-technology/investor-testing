<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:55
 */

namespace Newton\InvestorTesting\Packages\Common;

use Generator;
use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class TestAnswerRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'test_answers';
    protected string $entity = TestAnswer::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @param int[] $testQuestionIds
     * @return TestAnswer[]
     * @throws Throwable
     */
    public function getAnswersByQuestionIds(array $testQuestionIds): array
    {
        return iterator_to_array($this->getAnswersByQuestionIdsIterator($testQuestionIds));
    }

    /**
     * @param int[] $testQuestionIds
     * @return Generator|TestAnswer[]
     * @throws Throwable
     */
    public function getAnswersByQuestionIdsIterator(array $testQuestionIds): Generator
    {
        return $this->getCollectionIterator(
            [
                ['test_question_id', 'in', $testQuestionIds],
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function addAnswer(TestAnswer $answer)
    {
        $this->addEntityWithApplyResult($answer);
    }

    /**
     * @param TestAnswer[] $answers
     * @throws Throwable
     */
    public function addAnswers(array $answers)
    {
        $this->addEntities($answers);
    }

    /**
     * @param TestAnswer[] $answers
     */
    public function updateAnswers(array $answers)
    {
        foreach ($answers as $answer) {
            $this->updateEntityWithApplyResult($answer);
        }
    }
}
