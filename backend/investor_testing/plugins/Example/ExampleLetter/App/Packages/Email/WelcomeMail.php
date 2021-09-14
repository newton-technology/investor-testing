<?php

namespace Plugins\Example\ExampleLetter\App\Packages\Email;

use Newton\InvestorTesting\Packages\Common\User;

use Illuminate\Mail\Mailable;

class WelcomeMail extends Mailable
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build(): WelcomeMail
    {
        return $this->view('letters.welcome')
            ->with(
                [
                    'user' => $this->user,
                ]
            );
    }
}
