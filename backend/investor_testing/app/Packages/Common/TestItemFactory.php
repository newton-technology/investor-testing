<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 22.09.2021
 * Time: 10:32
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Newton\InvestorTesting\Packages\Common\Exception\ExceptionWithoutReport;

class TestItemFactory
{
    protected CategoryRepository $categoryRepository;
    protected TestQuestionRepository $testQuestionRepository;
    protected TestAnswerRepository $testAnswerRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        TestQuestionRepository $testQuestionRepository,
        TestAnswerRepository $testAnswerRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->testQuestionRepository = $testQuestionRepository;
        $this->testAnswerRepository = $testAnswerRepository;
    }

    /**
     * @throws Throwable
     */
    public function fromTest(Test $test): TestItem
    {
        $category = $this->categoryRepository->getCategory($test->getCategoryId());
        if (empty($category)) {
            throw ExceptionWithoutReport::categoryNotFound();
        }

        $testQuestions = [];
        $testQuestionsIterator = $this->testQuestionRepository->getQuestionsByTestIdIterator($test->getId());
        foreach ($testQuestionsIterator as $testQuestion) {
            $testQuestionsIds[] = $testQuestion->getId();
            $testQuestions[] = $testQuestion;
        }

        return $this->fillTestItem($category, $test, $testQuestions);
    }

    /**
     * @param Category $category
     * @param Test $test
     * @param TestQuestion[] $testQuestions
     * @return TestItem
     * @throws Throwable
     */
    public function fillTestItem(
        Category $category,
        Test $test,
        array $testQuestions
    ): TestItem {
        $testQuestionsIds = [];
        $testItemQuestions = [];
        foreach ($testQuestions as $testQuestion) {
            $testQuestionsIds[] = $testQuestion->getId();
            $testItemQuestions[$testQuestion->getId()] = (new TestItemQuestion())
                ->setId($testQuestion->getId())
                ->setQuestion($testQuestion->getQuestionText())
                ->setAnswersCountToChooseMin($testQuestion->getAnswersCountToChooseMin())
                ->setAnswersCountToChooseMax($testQuestion->getAnswersCountToChooseMax());
        }

        $testAnswersIterator = $this->testAnswerRepository->getAnswersByQuestionIdsIterator($testQuestionsIds);
        foreach ($testAnswersIterator as $testAnswer) {
            $testItemQuestions[$testAnswer->getTestQuestionId()]->addAnswer(
                (new TestItemQuestionAnswer())
                    ->setId($testAnswer->getId())
                    ->setAnswer($testAnswer->getAnswerText())
                    ->setSelected($testAnswer->isSelected())
            );
        }

        return (new TestItem())
            ->setId($test->getId())
            ->setStatus($test->getStatus())
            ->setCreatedAt($test->getCreatedAt())
            ->setUpdatedAt($test->getUpdatedAt())
            ->setCompletedAt($test->getCompletedAt())
            ->setCategory($this->getTestCategory($category))
            ->setQuestions(array_values($testItemQuestions));
    }

    /**
     * @throws Throwable
     */
    protected function getTestCategory(Category $category): CategoryItemCategory
    {
        return (new CategoryItemCategory())->applyPropertiesArray($category->toArray());
    }
}
