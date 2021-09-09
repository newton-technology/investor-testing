<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:22
 */

namespace Newton\InvestorTesting\Packages\Common;

use Generator;
use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class TestQuestionRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'test_questions';
    protected string $entity = TestQuestion::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @param int $testId
     * @return TestQuestion[]
     * @throws Throwable
     */
    public function getQuestionsByTestId(int $testId): array
    {
        return iterator_to_array($this->getQuestionsByTestIdIterator($testId));
    }

    /**
     * @param int $testId
     * @return Generator|TestQuestion[]
     * @throws Throwable
     */
    public function getQuestionsByTestIdIterator(int $testId): Generator
    {
        return $this->getCollectionIterator(
            [
                ['test_id', $testId],
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function addQuestion(TestQuestion $question)
    {
        $this->addEntityWithApplyResult($question);
    }

    /**
     * @param TestQuestion[] $questions
     * @throws Throwable
     */
    public function addQuestions(array $questions)
    {
        $this->addEntities($questions);
    }
}
