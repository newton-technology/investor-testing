<?php /** @noinspection PhpMissingFieldTypeInspection */

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:24
 */

namespace Newton\InvestorTesting\Console\Commands;

use Exception;
use Throwable;

use Newton\InvestorTesting\Packages\Common\Answer;
use Newton\InvestorTesting\Packages\Common\AnswerRepository;
use Newton\InvestorTesting\Packages\Common\Category;
use Newton\InvestorTesting\Packages\Common\CategoryRepository;
use Newton\InvestorTesting\Packages\Common\Question;
use Newton\InvestorTesting\Packages\Common\QuestionRepository;

use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import' .
    '{type           : Type of data (categories, questions, answers)}' .
    '{filePath       : File path}' .
    '{--separator=;  : CSV separator}' .
    '{--dropExisting : Drop existing data}';

    /**
     * @var string
     */
    protected $description = 'Import data from CSV';

    /**
     * @throws Throwable
     */
    public function handle(
        CategoryRepository $categoryRepository,
        QuestionRepository $questionRepository,
        AnswerRepository $answerRepository
    ) {
        $type = $this->argument('type');

        $filePath = $this->argument('filePath');
        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        switch ($type) {
            case 'categories':
                $this->importCategories($filePath, $categoryRepository);
                break;
            case 'questions':
                $this->importQuestions($filePath, $questionRepository, $categoryRepository);
                break;
            case 'answers':
                $this->importAnswers($filePath, $answerRepository, $questionRepository);
                break;
            default:
                throw new Exception("Invalid type: {$type}");
        }

        $this->info('import completed');
    }

    /**
     * @throws Throwable
     */
    protected function importCategories(string $filePath, CategoryRepository $categoryRepository)
    {
        $raw = file($filePath);
        $csv = [];
        foreach ($raw as $row) {
            if (empty($row)) {
                continue;
            }
            $csv[] = str_getcsv($row, $this->option('separator'));
        }

        $existingCategoryCodes = array_map(
            fn($category) => $category->getCode(),
            $categoryRepository->getCategories()
        );

        $categories = [];
        foreach ($csv as $item) {
            $item = array_map('trim', $item);
            if (!isset($headers)) {
                $item[0] = ltrim($item[0], "\xEF\xBB\xBF");
                $headers = $item;
                continue;
            }

            $category = Category::fromArray(array_combine($headers, $item));
            if (!$this->option('dropExisting') && $category->getCode() !== null && in_array($category->getCode(), $existingCategoryCodes)) {
                throw new Exception("Category already exists: {$category->getCode()}");
            }

            $categories[] = $category;
        }

        $categoryRepository->executeTransaction(function () use ($categoryRepository, $categories) {
            if ($this->option('dropExisting')) {
                $categoryRepository->deleteAllCategories();
            }
            $categoryRepository->addCategories($categories);
        });
    }

    /**
     * @throws Throwable
     */
    protected function importQuestions(
        string $filePath,
        QuestionRepository $questionRepository,
        CategoryRepository $categoryRepository
    ) {
        $raw = file($filePath);
        $csv = [];
        foreach ($raw as $row) {
            $csv[] = str_getcsv($row, $this->option('separator'));
        }

        $existingQuestionIds = array_map(
            fn($question) => $question->getId(),
            $questionRepository->getQuestions()
        );

        $questions = [];
        $categoryCodeToIdMap = [];
        $positionAnswersCountToChooseMax = false;
        $positionCategoryCode = false;
        foreach ($csv as $item) {
            $item = array_map('trim', $item);
            if (!isset($headers)) {
                $item[0] = ltrim($item[0], "\xEF\xBB\xBF");
                $headers = $item;
                $positionAnswersCountToChooseMax = array_search('answers_count_to_choose_max', $headers);
                $positionCategoryCode = array_search('category_code', $headers);

                if ($positionCategoryCode !== false) {
                    $categories = $categoryRepository->getCategories();
                    foreach ($categories as $category) {
                        $categoryCodeToIdMap[$category->getCode()] = $category->getId();
                    }
                    $headers[$positionCategoryCode] = 'category_id';
                }
                continue;
            }

            if ($positionCategoryCode !== false) {
                $categoryCode = $item[$positionCategoryCode];
                if (!array_key_exists($categoryCode, $categoryCodeToIdMap)) {
                    throw new Exception("Category does not exist: {$categoryCode}");
                }
                $item[$positionCategoryCode] = $categoryCodeToIdMap[$categoryCode];
            }

            if ($positionAnswersCountToChooseMax !== false && $item[$positionAnswersCountToChooseMax] === '') {
                $item[$positionAnswersCountToChooseMax] = null;
            }

            $question = Question::fromArray(array_combine($headers, $item));
            if (!$this->option('dropExisting') && $question->getId() !== null && in_array($question->getId(), $existingQuestionIds)) {
                throw new Exception("Question already exists: {$question->getId()}");
            }

            $questions[] = $question;
        }

        $questionRepository->executeTransaction(function () use ($questionRepository, $questions) {
            if ($this->option('dropExisting')) {
                $questionRepository->deleteAllQuestions();
            }
            $questionRepository->addQuestions($questions);
        });
    }

    /**
     * @throws Throwable
     */
    protected function importAnswers(
        string $filePath,
        AnswerRepository $answerRepository,
        QuestionRepository $questionRepository
    ) {
        $raw = file($filePath);
        $csv = [];
        foreach ($raw as $row) {
            $csv[] = str_getcsv($row, $this->option('separator'));
        }

        $existingQuestionIds = array_map(
            fn($question) => $question->getId(),
            $questionRepository->getQuestions()
        );

        $answers = [];
        $answersCorrectPosition = false;
        foreach ($csv as $item) {
            $item = array_map('trim', $item);
            if (!isset($headers)) {
                $item[0] = ltrim($item[0], "\xEF\xBB\xBF");
                $headers = $item;
                $answersCorrectPosition = array_search('correct', $headers);
                continue;
            }

            if ($answersCorrectPosition !== false && $item[$answersCorrectPosition] === '') {
                $item[$answersCorrectPosition] = null;
            }

            $answer = Answer::fromArray(array_combine($headers, $item));
            if (!in_array($answer->getQuestionId(), $existingQuestionIds)) {
                throw new Exception("Question does not exists: {$answer->getQuestionId()}");
            }

            $answers[] = $answer;
        }

        $answerRepository->executeTransaction(function () use ($answerRepository, $answers) {
            if ($this->option('dropExisting')) {
                $answerRepository->deleteAllAnswers();
            }
            $answerRepository->addAnswers($answers);
        });
    }
}
