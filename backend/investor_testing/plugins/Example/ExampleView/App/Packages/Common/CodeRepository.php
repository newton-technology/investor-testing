<?php
/**
 * Created by PhpStorm.
 * User: Kairat Kaneshov
 * Date: 23.08.2021
 * Time: 13:47
 */

namespace Plugins\Example\ExampleView\App\Packages\Common;

use Newton\InvestorTesting\Packages\Authorization\CodeInfo;
use Newton\InvestorTesting\Packages\Common\User;
use Plugins\Example\ExampleView\App\Packages\Email\CodeMail;

use Illuminate\Support\Facades\Mail;

class CodeRepository extends \Newton\InvestorTesting\Packages\Authorization\CodeRepository
{
    protected function sendCode(User $user, CodeInfo $codeInfo, string $code)
    {
        Mail::to($user->getEmail())
            ->send(new CodeMail($code, $codeInfo, $user));
    }
}
