<?php

namespace Common\Base\Repositories\Database;

use stdClass;

/**
 * Trait OidFieldsIlluminateRepositoryTrait
 *
 * @package Common\Repositories\Traits
 *
 * @property string[] $oidFields Массив полей, которые являются идентификаторами в новой нотации,
 *     но надо преобразовать к старой (то бишь превратить суффикс `_id` в `_oid`)
 */
trait OidFieldsIlluminateRepositoryTrait
{
    use IlluminateRepositoryTrait {
        encodeFields as traitEncodeFields;
        decodeRaw as traitDecodeRaw;
    }

    /**
     * Массив для кеша трансформаций из _id поля сущности в _oid поле бд
     * @var array<string, string>
     */
    private static array $oidEntityFieldToIdField = [];

    /**
     * @param array $fields (in format [$key => $value])
     * @param array $oidFields
     *
     * @return array
     */
    protected function covertArrayIdKeysToOid($fields, $oidFields = [])
    {
        $oidFields = empty($oidFields) ? ($this->oidFields ?? []) : $oidFields;
        $oidPostfix = '_oid';
        $idPostfix = '_id';
        foreach ($fields as $key => $value) {
            if (in_array($key, $oidFields)) {
                $fields[substr($key, 0, strlen($key) - strlen($idPostfix)) . $oidPostfix] = $fields[$key];
                unset($fields[$key]);
            }
        }
        return $fields;
    }

    /**
     * @param stdClass $raw
     * @param array $oidFields
     *
     * @return stdClass
     */
    protected function convertObjectOidFieldsToId(stdClass $raw, $oidFields = [])
    {
        $oidEntityFields = empty($oidFields) ? ($this->oidFields ?? []) : $oidFields;
        $oidPostfix = '_oid';
        $idPostfix = '_id';
        foreach ($oidEntityFields as $oidEntityField) {
            $oidField = self::$oidEntityFieldToIdField[$oidEntityField] ?? null;
            if ($oidField === null) {
                $oidField = substr($oidEntityField, 0, strlen($oidEntityField) - strlen($idPostfix)) . $oidPostfix;
                self::$oidEntityFieldToIdField[$oidEntityField] = $oidField;
            }
            if (property_exists($raw, $oidField)) {
                $raw->$oidEntityField = $raw->$oidField;
                unset($raw->$oidField);
            }
        }

        return $raw;
    }

    /**
     * Encode fields for insert to database
     *
     * @param object $entity
     * @param array $fields
     *
     * @return array
     */
    protected function encodeFields(object $entity, array $fields = [])
    {
        $fields = $this->traitEncodeFields($entity, $fields);
        return $this->covertArrayIdKeysToOid($fields);
    }

    /**
     * @param stdClass $raw
     *
     * @return mixed
     */
    protected function decodeRaw(stdClass $raw)
    {
        $raw = $this->convertObjectOidFieldsToId($raw);
        return $this->traitDecodeRaw($raw);
    }
}
