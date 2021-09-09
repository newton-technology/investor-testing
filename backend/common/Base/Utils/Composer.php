<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.09.2020
 * Time: 20:46
 */

namespace Common\Base\Utils;

use stdClass;

abstract class Composer
{
    /**
     * @var stdClass[]
     */
    private static array $composerContent = [];

    /**
     * @param string|null $composerPath
     * @return stdClass
     */
    public static function getComposerContent(?string $composerPath = null): stdClass
    {
        if (empty($composerPath)) {
            $composerPath = base_path('composer.json');
        }

        if (!array_key_exists($composerPath, self::$composerContent)) {
            self::$composerContent[$composerPath] = json_decode(file_get_contents($composerPath));
        }

        return self::$composerContent[$composerPath];
    }

    /**
     * @param string|null $composerPath
     * @return string
     */
    public static function getApplicationName(?string $composerPath = null): string
    {
        $composer = self::getComposerContent($composerPath);
        return $composer->name;
    }

    /**
     * @param string|null $composerPath
     * @return string
     */
    public static function getApplicationShortName(?string $composerPath = null): string
    {
        $applicationName = self::getApplicationName($composerPath);
        $newtonPrefix = 'newton-technology/';
        if (str_starts_with($applicationName, $newtonPrefix)) {
            $applicationName = substr($applicationName, strlen($newtonPrefix));
        }

        return $applicationName;
    }

    /**
     * @param string|null $composerPath
     * @return string|null
     */
    public static function getApplicationVersion(?string $composerPath = null): ?string
    {
        $composer = self::getComposerContent($composerPath);
        return $composer->version ?? null;
    }

    /**
     * @param string|null $composerPath
     * @return string|null
     */
    public static function getApplicationStage(?string $composerPath = null): ?string
    {
        $composer = self::getComposerContent($composerPath);
        return empty($extra = $composer->extra) ? null : $extra->stage ?? null;
    }

    /**
     * @param string|null $composerPath
     * @return object|null
     */
    public static function getApplicationDependencies(?string $composerPath = null): ?object
    {
        $composer = self::getComposerContent($composerPath);
        return $composer->{'require'} ?? null;
    }

    /**
     * @param string|null $composerPath
     * @return object|null
     */
    public static function getApplicationDevDependencies(?string $composerPath = null): ?object
    {
        $composer = self::getComposerContent($composerPath);
        return $composer->{'require-dev'} ?? null;
    }

    /**
     * Вернет основной namespace проекта
     * @param string|null $composerPath
     * @return string
     */
    public static function getApplicationNamespace(?string $composerPath = null): string
    {
        $composer = self::getComposerContent($composerPath);
        foreach ($composer->{'autoload'}->{'psr-4'} ?? [] as $namespace => $directory) {
            if ($directory === 'app/') {
                return substr($namespace, 0, strlen($namespace) - 1);
            }
        }

        return ucfirst(basename(dirname($composerPath)));
    }

    /**
     * Вернет директорию соответствующую namespace в проекте
     * @param string $namespace
     * @param string|null $composerPath
     * @return string|null
     */
    public static function getApplicationNamespaceDirectory(string $namespace, ?string $composerPath = null): ?string
    {
        $composer = self::getComposerContent($composerPath);
        foreach ($composer->{'autoload'}->{'psr-4'} ?? [] as $psrNamespace => $directory) {
            if ($namespace === $psrNamespace || "$namespace\\" === $psrNamespace) {
                return $directory;
            }
        }

        return null;
    }

}
