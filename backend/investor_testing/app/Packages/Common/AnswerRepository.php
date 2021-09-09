<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:22
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class AnswerRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'answers';
    protected string $entity = Answer::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @throws Throwable
     */
    public function addAnswer(Answer $answer)
    {
        $this->addEntityWithApplyResult($answer);
    }

    /**
     * @param Answer[] $answers
     * @throws Throwable
     */
    public function addAnswers(array $answers)
    {
        $this->addEntities($answers);
    }

    /**
     * @return Answer[]
     */
    public function getAvailableAnswers(int $questionId): array
    {
        return $this->getCollection(
            [
                ['status', '!=', Answer::STATUS_DISABLED],
                ['question_id', $questionId],
            ]
        );
    }

    public function deleteAllAnswers()
    {
        $this->deleteEntities([]);
    }
}
