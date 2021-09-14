<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:47
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Newton\InvestorTesting\Packages\Common\User;

use Illuminate\Mail\Mailable;

class CodeMail extends Mailable
{
    protected string $code;
    protected CodeInfo $codeInfo;
    protected User $user;

    public function __construct(string $code, CodeInfo $codeInfo, User $user)
    {
        $this->code = $code;
        $this->codeInfo = $codeInfo;
        $this->user = $user;
    }

    public function build(): CodeMail
    {
        return $this->view('emails.authorization_code')
            ->subject($this->code . ' — ваш код для тестирования')
            ->with(
                [
                    'code' => $this->code,
                ]
            );
    }
}
