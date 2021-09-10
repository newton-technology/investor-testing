<?php
/**
 * Created by PhpStorm.
 * User: Kairat Kaneshov
 * Date: 23.08.2021
 * Time: 13:47
 */

namespace Plugins\Example\ExampleView\App\Packages\Email;

class CodeMail extends \Newton\InvestorTesting\Packages\Authorization\CodeMail
{
    public function build(): CodeMail
    {
        return $this->view('emails.plugin_authorization_code')
            ->subject($this->code . ' — ваш код для тестирования')
            ->with(
                [
                    'code' => $this->code,
                ]
            );
    }
}
