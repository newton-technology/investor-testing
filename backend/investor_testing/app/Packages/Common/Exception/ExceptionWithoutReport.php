<?php

namespace Newton\InvestorTesting\Packages\Common\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:48
 */
class ExceptionWithoutReport extends \Common\Base\Exception\ExceptionWithoutReport
{
    public static function categoryNotFound(): self
    {
        return (new self('category not found', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'category_not_found',
                    'message' => 'Искомая категория не найдена',
                ]
            );
    }

    public static function categoryNotFilled(): self
    {
        return (new self('category not filled', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'category_not_filled',
                    'message' => 'Искомая категория не содержит вопросов',
                ]
            );
    }

    public static function categoryPassed(): self
    {
        return (new self('category passed', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'category_passed',
                    'message' => 'Категория уже успешно пройдена',
                ]
            );
    }

    public static function unexpectedTestStatus(): self
    {
        return (new self('unexpected status', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'unexpected_status',
                    'message' => 'Тест находится в неподдерживаемом статусе',
                ]
            );
    }

    public static function testNotFound(): self
    {
        return (new self('test not found', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'test_not_found',
                    'message' => 'Тест не найден',
                ]
            );
    }

    public static function testAlreadyCompleted(): self
    {
        return (new self('test already completed', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'test_already_completed',
                    'message' => 'Тест уже завершен',
                ]
            );
    }

    public static function testAnswerNotFound(int $answerId): self
    {
        return (new self('test answer not found', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'test_answer_not_found',
                    'message' => "Ответ не найден ({$answerId})",
                ]
            );
    }

    public static function testTooManyAnswers(int $questionId): self
    {
        return (new self('test too many answers', Response::HTTP_UNPROCESSABLE_ENTITY, 0))
            ->setPayload(
                [
                    'code' => 'test_too_many_answers',
                    'message' => "Слишком много ответов для вопроса ({$questionId})",
                ]
            );
    }
}
