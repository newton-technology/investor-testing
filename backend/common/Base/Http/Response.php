<?php

namespace Common\Base\Http;

use SimpleXMLElement;

use Common\Base\Utils\TransformationUtils;

/**
 * Class Response
 * @package Common\Http\Response
 */
class Response
{
    public const HEADER_RESPONSE_TIME = 'X-Response-Time';
    public const HEADER_RESPONSE_VERSION = 'X-Response-Version';
    public const HEADER_TOTAL_COUNT = 'X-Total-Count';
    public const HEADER_REQUEST_TIME_LEFT = 'X-Request-Time-Left';

    /**
     * Произвольный код ответа
     *
     * @param int $code
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public static function response(int $code, $data = [], $headers = [])
    {
        self::applyHeaders($headers);
        return new JsonResponse($data, $code, $headers, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Произвольный ответ в XML
     *
     * @param SimpleXMLElement $xml
     * @param int $status
     * @param string $charset
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public static function xml(SimpleXMLElement $xml, int $status = 200, string $charset = 'UTF-8', $headers = [])
    {
        $headers = array_merge($headers, [
            'Content-Type' => 'application/xml' . (!empty($charset) ? "; charset={$charset}" : ''),
        ]);

        return response($xml->asXML(), $status, $headers);
    }

    /**
     * Метод для возврата файла в качестве ответа
     *
     * @param mixed $file
     * @param string $fileName
     * @param string $contentType
     * @param int $status
     * @param array $headers
     *
     * @return \Illuminate\Http\Response
     */
    public static function file($file, string $fileName, string $contentType, int $status = 200, $headers = [])
    {
        $headers = array_merge($headers, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "attachment; filename=" . urlencode($fileName)
        ]);

        return response($file, $status, $headers);
    }

    /**
     * Успешный ответ
     *
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public static function success($data = [], $headers = [])
    {
        return self::response(\Illuminate\Http\Response::HTTP_OK, $data, $headers);
    }

    /**
     * Ответ сервера с кодом 404
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function notFound($data = [], $headers = [])
    {
        $payload = self::processPayload(
            TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__),
            $data
        );

        return self::response(\Illuminate\Http\Response::HTTP_NOT_FOUND, $payload, $headers);
    }

    /**
     * Ответ сервера с кодом 403
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function forbidden($data = [], $headers = [])
    {
        $payload = self::processPayload(__FUNCTION__, $data);

        return self::response(\Illuminate\Http\Response::HTTP_FORBIDDEN, $payload, $headers);
    }

    /**
     * Неавторизованный запрос
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function unauthorized($data = [], $headers = [])
    {
        $payload = self::processPayload(__FUNCTION__, $data);

        return self::response(\Illuminate\Http\Response::HTTP_UNAUTHORIZED, $payload, $headers);
    }

    /**
     * Ответ при создании новой сущности
     *
     * @param array $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function created($data = [], $headers = [])
    {
        return self::response(\Illuminate\Http\Response::HTTP_CREATED, $data, $headers);
    }

    /**
     * Сервер успешно принял запрос, может работать с указанным видом данных
     * (например, в теле запроса находится XML-документ, имеющий верный синтаксис),
     * однако имеется какая-то логическая ошибка, из-за которой невозможно произвести операцию над ресурсом
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function unprocessableEntity($data = [], $headers = [])
    {
        $payload = self::processPayload(
            TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__),
            $data
        );

        return self::response(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY, $payload, $headers);
    }

    /**
     * Сущность удалена
     *
     * @param array $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function deleted($data = [], $headers = [])
    {
        return self::response(\Illuminate\Http\Response::HTTP_NO_CONTENT, $data, $headers);
    }

    /**
     * Сущность принята к обработке
     *
     * @param array $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function accepted($data = [], $headers = [])
    {
        return self::response(\Illuminate\Http\Response::HTTP_ACCEPTED, $data, $headers);
    }

    /**
     * Непредвиденная ошибка
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function internalServerError($data = [], $headers = [])
    {
        $payload = self::processPayload(
            TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__),
            $data
        );

        return self::response(\Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR, $payload, $headers);
    }

    /**
     * Слишком много запросов
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function tooManyRequests($data = [], $headers = [])
    {
        $payload = self::processPayload(
            TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__),
            $data
        );

        return self::response(\Illuminate\Http\Response::HTTP_TOO_MANY_REQUESTS, $payload, $headers);
    }

    /**
     * Запрос конфликтует с текущим состоянием изменяемого ресурса
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function conflict($data = [], array $headers = []): JsonResponse
    {
        $payload = self::processPayload(__FUNCTION__, $data);

        return self::response(\Illuminate\Http\Response::HTTP_CONFLICT, $payload, $headers);
    }

    /**
     * Ошибка клиента при обращении к эндпойнту
     *
     * @param array|string $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public static function badRequest($data = [], $headers = [])
    {
        $payload = self::processPayload(__FUNCTION__, $data);

        return self::response(\Illuminate\Http\Response::HTTP_BAD_REQUEST, $payload, $headers);
    }

    private static function applyHeaders(array &$headers): void
    {
        $headers[self::HEADER_RESPONSE_VERSION] = 2;
        $headers[self::HEADER_RESPONSE_TIME] = microtime(true);
    }

    /**
     * @param string $code Код ошибки
     * @param array|string $inputData Входные данные
     *
     * @return array|string[]
     */
    private static function processPayload(string $code, $inputData): array
    {
        $payload = ['code' => $code];

        if (is_string($inputData)) {
            $payload['message'] = $inputData;
        } else {
            $payload = array_merge($payload, $inputData);
        }

        return $payload;
    }
}
