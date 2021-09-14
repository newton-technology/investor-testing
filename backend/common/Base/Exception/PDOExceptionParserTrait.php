<?php

namespace Common\Base\Exception;

use PDOException;

trait PDOExceptionParserTrait
{
    /**
     * Не получилось достать errorInfo из PDOException, почему-то возвращается null при
     * таймауте подключения. Этот трейт позволяет извлекать SQLSTATE из сообщения об ошибке
     *
     * https://www.postgresql.org/docs/9.4/errcodes-appendix.html
     *
     * @param \Common\Base\Exception\PDOException $exception
     * @return string|null
     */
    protected function pdoExceptionSqlState(PDOException $exception): ?string
    {
        if (preg_match('/SQLSTATE\[([\w|\d]+)\]/', $exception->getMessage(), $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Определяет по сообщению в PDOException, произошла ли ошибка
     * из-за невозможности установить соединение
     *
     * https://www.postgresql.org/docs/9.4/errcodes-appendix.html
     *
     * @param \Common\Base\Exception\PDOException $exception
     * @return bool
     */
    protected function pdoExceptionIsConnectionError(PDOException $exception): bool
    {
        $sqlState = $this->pdoExceptionSqlState($exception);
        return !empty($sqlState) && substr($sqlState, 0, 2) === '08';
    }

}
