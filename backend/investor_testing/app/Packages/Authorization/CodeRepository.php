<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.08.2021
 * Time: 19:17
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Throwable;

use Newton\InvestorTesting\Packages\Common\User;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class CodeRepository
{
    /**
     * @throws Throwable
     */
    public function issueCode(User $user, int $length, int $ttl): CodeInfo
    {
        $code = random_int(1, '9' . str_pad('', $length - 1, '9'));
        $code = str_pad($code, $length, '0', STR_PAD_LEFT);

        $codeInfo = $this->addCode($user, $code, $ttl);
        $this->sendCode($user, $codeInfo, $code);

        return $codeInfo;
    }

    /**
     * @throws Throwable
     */
    public function tryToUseCode(User $user, string $uuid, string $code): ?CodeInfo
    {
        $codeRaw = Cache::get($this->getCodeKey($uuid));
        if (empty($codeRaw)) {
            return null;
        }

        $codeInfo = CodeInfo::fromJson($codeRaw);

        if ($codeInfo->getHash() !== $this->getCodeHash($user, $code)) {
            $this->updateCodeInfo(
                $codeInfo->incrementAttemptsCount()
            );

            return $codeInfo;
        }

        $this->revokeCode($uuid);
        return $codeInfo->setVerified(true);
    }

    public function revokeCode(string $uuid)
    {
        Cache::forget($this->getCodeKey($uuid));
    }

    /**
     * @throws Throwable
     */
    protected function addCode(User $user, string $code, int $ttl): CodeInfo
    {
        $codeInfo = (new CodeInfo())
            ->setUuid(method_exists(Uuid::class, 'uuid6') ? Uuid::uuid6() : Uuid::uuid4())
            ->setHash($this->getCodeHash($user, $code))
            ->setExpiredAt(time() + $ttl);

        Cache::put($this->getCodeKey($codeInfo->getUuid()), $codeInfo->toJson(), $ttl);

        return $codeInfo;
    }

    /**
     * @throws Throwable
     */
    protected function updateCodeInfo(CodeInfo $codeInfo)
    {
        $ttl = $codeInfo->getExpiredAt() - time();
        if ($ttl <= 0) {
            return;
        }

        Cache::put($this->getCodeKey($codeInfo->getUuid()), $codeInfo->toJson(), $ttl);
    }

    protected function sendCode(User $user, CodeInfo $codeInfo, string $code)
    {
        Mail::to($user->getEmail())
            ->send(new CodeMail($code, $codeInfo, $user));
    }

    private function getCodeKey(string $uuid): string
    {
        return implode(':', ['code', $uuid]);
    }

    private function getCodeHash(User $user, string $code): string
    {
        return md5(implode(':', [$user->getId() ?? $user->getEmail(), $code]));
    }
}
