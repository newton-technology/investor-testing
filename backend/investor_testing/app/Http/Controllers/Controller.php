<?php

namespace Newton\InvestorTesting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class Controller
 * @package Newton\InvestorTesting\Http\Controllers
 */
class Controller extends \Laravel\Lumen\Routing\Controller
{
    /**
     * @param array $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws ValidationException
     */
    protected function validateArray(array $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->validate(new Request($request), $rules, $messages, $customAttributes);
    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws ValidationException
     */
    protected function validateRoute(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $input = [];
        foreach ($rules as $name => $rule) {
            $input[$name] = $request->route($name);
        }

        return $this->validateArray(
            $input,
            $rules,
            $messages,
            $customAttributes
        );
    }
}
