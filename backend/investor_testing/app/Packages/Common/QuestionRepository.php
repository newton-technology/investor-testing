<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:32
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class QuestionRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'questions';
    protected string $entity = Question::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @throws Throwable
     */
    public function addQuestion(Question $question)
    {
        $this->addEntityWithApplyResult($question);
    }

    /**
     * @param Question[] $questions
     * @throws Throwable
     */
    public function addQuestions(array $questions)
    {
        $this->addEntities($questions);
    }

    /**
     * @return Question[]
     */
    public function getQuestions(): array
    {
        return $this->getCollection();
    }

    /**
     * @return Question[]
     */
    public function getQuestionsAvailableForCategory(int $categoryId): array
    {
        return $this->getCollection(
            [
                ['status', '!=', Question::STATUS_DISABLED],
                ['category_id', $categoryId],
            ],
            INF,
            0,
            [
                ['weight'],
            ]
        );
    }

    public function deleteAllQuestions()
    {
        $this->deleteEntities([]);
    }
}
