<?php

namespace Plugins\Example\ExampleLetter\App\Packages\Common;

use Newton\InvestorTesting\Packages\Common\User;
use Plugins\Example\ExampleLetter\App\Packages\Email\WelcomeMail;

use Illuminate\Support\Facades\Mail;

class UserRepository extends \Newton\InvestorTesting\Packages\Common\UserRepository
{
    public function addUser(User $user)
    {
        parent::addUser($user);
        Mail::to($user->getEmail())->send(new WelcomeMail($user));
    }
}
