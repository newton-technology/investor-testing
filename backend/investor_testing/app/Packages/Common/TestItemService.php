<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:03
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Newton\InvestorTesting\Packages\Common\Exception\ExceptionWithoutReport;

use Illuminate\Support\Facades\Mail;

class TestItemService
{
    protected CategoryRepository $categoryRepository;
    protected QuestionRepository $questionRepository;
    protected AnswerRepository $answerRepository;
    protected TestRepository $testRepository;
    protected TestQuestionRepository $testQuestionRepository;
    protected TestAnswerRepository $testAnswerRepository;
    protected TestResultHandler $testResultHandler;
    protected UserRepository $userRepository;
    protected TestItemFactory $testItemFactory;

    public function __construct(
        CategoryRepository $categoryRepository,
        QuestionRepository $questionRepository,
        AnswerRepository $answerRepository,
        TestRepository $testRepository,
        TestQuestionRepository $testQuestionRepository,
        TestAnswerRepository $testAnswerRepository,
        TestResultHandler $testResultHandler,
        UserRepository $userRepository,
        TestItemFactory $testItemFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->questionRepository = $questionRepository;
        $this->answerRepository = $answerRepository;
        $this->testRepository = $testRepository;
        $this->testQuestionRepository = $testQuestionRepository;
        $this->testAnswerRepository = $testAnswerRepository;
        $this->testResultHandler = $testResultHandler;
        $this->userRepository = $userRepository;
        $this->testItemFactory = $testItemFactory;
    }

    /**
     * @throws Throwable
     */
    public function getTestItem(int $userId, int $testId): TestItem
    {
        $test = $this->testRepository->getTestForUserById($userId, $testId);
        if (empty($test)) {
            throw ExceptionWithoutReport::testNotFound();
        }

        return $this->testItemFactory->fromTest($test);
    }

    /**
     * @throws Throwable
     */
    public function generateTestItem(int $userId, int $categoryId): TestItem
    {
        $category = $this->categoryRepository->getCategory($categoryId);
        if (empty($category) || $category->getStatus() === Category::STATUS_DISABLED) {
            throw ExceptionWithoutReport::categoryNotFound();
        }

        $questions = $this->getQuestions($userId, $category);

        $testQuestions = [];
        $testAnswersByQuestionId = [];
        foreach ($questions as $question) {
            $testQuestion = $this->getTestQuestion($question);
            $testQuestions[] = $testQuestion;

            $testAnswersByQuestionId[$testQuestion->getQuestionId()] = $this->getAnswers($question);
        }

        $this->testRepository->beginTransaction();

        $test = (new Test())->setUserId($userId)->setCategoryId($categoryId);
        $this->testRepository->addTest($test);

        $testAnswers = [];
        foreach ($testQuestions as $testQuestion) {
            $testQuestion->setTestId($test->getId());
            $this->testQuestionRepository->addQuestion($testQuestion);

            foreach ($testAnswersByQuestionId[$testQuestion->getQuestionId()] as $testAnswer) {
                $testAnswers[] = $testAnswer->setTestQuestionId($testQuestion->getId());
            }
        }
        $this->testAnswerRepository->addAnswers($testAnswers);

        $this->testRepository->commitTransaction();

        return $this->testItemFactory->fillTestItem($category, $test, $testQuestions);
    }

    /**
     * @param int $userId
     * @param int $testId
     * @param int[] $selectedAnswersIds
     * @return TestItem
     * @throws Throwable
     */
    public function addTestAnswers(int $userId, int $testId, array $selectedAnswersIds): TestItem
    {
        $test = $this->testRepository->getTestForUserById($userId, $testId);
        if (empty($test)) {
            throw ExceptionWithoutReport::testNotFound();
        }
        if ($test->getStatus() !== Test::STATUS_DRAFT) {
            throw ExceptionWithoutReport::testAlreadyCompleted();
        }

        $testQuestionsById = $this->getTestQuestionsById($test->getId());
        $testAnswersById = $this->getTestAnswersByIdWithResetsSelection(array_keys($testQuestionsById));

        $answersCountByTestQuestionId = [];
        foreach ($selectedAnswersIds as $selectedAnswerId) {
            if (!array_key_exists($selectedAnswerId, $testAnswersById)) {
                throw ExceptionWithoutReport::testAnswerNotFound($selectedAnswerId);
            }

            $testQuestion = $testQuestionsById[$testAnswersById[$selectedAnswerId]->getTestQuestionId()];
            $testAnswersById[$selectedAnswerId]->setSelected(true);
            $answersCountByTestQuestionId[$testQuestion->getId()] =
                ($answersCountByTestQuestionId[$testQuestion->getId()] ?? 0) + 1;
            if ($answersCountByTestQuestionId[$testQuestion->getId()] > ($testQuestion->getAnswersCountToChooseMax() ?? INF)) {
                throw ExceptionWithoutReport::testTooManyAnswers($testQuestion->getId());
            }
        }

        $this->handleTestResult($test, $testQuestionsById, $testAnswersById);
        $this->notification($test);

        return $this->getTestItem($userId, $testId);
    }

    /**
     * @throws Throwable
     */
    protected function handleTestResult(Test $test, array $testQuestions, array $testAnswers): void
    {
        $result = (new TestResultHandler())
            ->handleQuestions($testQuestions)
            ->handleAnswers($testAnswers)
            ->getResult();

        $this->testRepository->beginTransaction();

        if ($result !== TestResultHandler::RESULT_INCOMPLETE) {
            $this->testRepository->updateTest(
                $test->setStatus(
                    $result === TestResultHandler::RESULT_PASSED
                        ? Test::STATUS_PASSED
                        : Test::STATUS_FAILED
                )
            );
        }
        $this->testAnswerRepository->updateAnswers($testAnswers);

        $this->testRepository->commitTransaction();
    }

    /**
     * @param int $testId
     * @return TestQuestion[]
     * @throws Throwable
     */
    protected function getTestQuestionsById(int $testId): array
    {
        $testQuestionsIterator = $this->testQuestionRepository->getQuestionsByTestIdIterator($testId);
        $testQuestionsById = [];
        foreach ($testQuestionsIterator as $question) {
            $testQuestionsById[$question->getId()] = $question;
        }

        return $testQuestionsById;
    }

    /**
     * @throws Throwable
     */
    protected function getTestAnswersByIdWithResetsSelection(array $testQuestionsIds): array
    {
        $testAnswersIterator = $this->testAnswerRepository->getAnswersByQuestionIdsIterator($testQuestionsIds);
        $testAnswersById = [];
        foreach ($testAnswersIterator as $answer) {
            // сбрасываем признак выбора ответа при изменениях списка ответов
            $testAnswersById[$answer->getId()] = $answer->setSelected(false);
        }

        return $testAnswersById;
    }

    /**
     * @return Question[]
     * @throws Throwable
     */
    protected function getQuestions(int $userId, Category $category): array
    {
        $existingTests = $this->testRepository->getTests(
            [
                ['user_id', $userId],
                ['category_id', $category->getId()],
                ['status', 'in', [Test::STATUS_PASSED, Test::STATUS_PROCESSING]],
            ]
        );

        if (!empty($existingTests)) {
            throw ExceptionWithoutReport::categoryPassed();
        }

        $availableQuestions = $this->questionRepository->getQuestionsAvailableForCategory($category->getId());
        if (empty($availableQuestions)) {
            throw ExceptionWithoutReport::categoryNotFilled();
        }

        return $availableQuestions;
    }

    /**
     * @return TestAnswer[]
     */
    protected function getAnswers(Question $question): array
    {
        $availableAnswers = $this->answerRepository->getAvailableAnswers($question->getId());

        $resultAnswers = [];
        $randomAnswers = [];

        foreach ($availableAnswers as $answer) {
            if ($answer->isCorrect() || $answer->getStatus() === Answer::STATUS_REQUIRED) {
                $resultAnswers[] = $answer;
            } else {
                $randomAnswers[] = $answer;
            }
        }

        $randomAnswersCount = $question->getAnswersCountMax() - count($resultAnswers);
        if ($randomAnswersCount > 0) {
            shuffle($randomAnswers);
            $resultAnswers = array_merge(
                $resultAnswers,
                array_splice($randomAnswers, 0, $randomAnswersCount)
            );
        }

        shuffle($resultAnswers);
        usort($resultAnswers, fn($a, $b) => $a->getSort() <=> $b->getSort());

        return array_map(
            fn($answer) => $this->getTestAnswer($answer),
            $resultAnswers
        );
    }

    protected function getTestQuestion(Question $question): TestQuestion
    {
        return (new TestQuestion())
            ->setQuestionId($question->getId())
            ->setQuestionText($question->getText())
            ->setQuestionWeight($question->getWeight())
            ->setAnswersCountToChooseMin($question->getAnswersCountToChooseMin())
            ->setAnswersCountToChooseMax($question->getAnswersCountToChooseMax());
    }

    protected function getTestAnswer(Answer $answer): TestAnswer
    {
        return (new TestAnswer())
            ->setAnswerId($answer->getId())
            ->setAnswerText($answer->getText())
            ->setCorrect($answer->isCorrect());
    }

    /**
     * Отправляет пользователю письмо с результатом о
     * прохождении тестирования
     *
     * @param Test $test
     */
    protected function notification(Test $test): void
    {
        if (in_array(
            $test->getStatus(),
            [
                Test::STATUS_PASSED,
                Test::STATUS_FAILED
            ]
        )) {
            Mail::to($this->userRepository->getUserById($test->getUserId())->getEmail())
                ->send(
                    new TestResultMail(
                        $this->categoryRepository
                            ->getCategoryById($test->getCategoryId())
                            ->getDescription(),
                        $test->getStatus(),
                        config('broker.test_result.broker_name')
                    )
                );
        }
    }
}
