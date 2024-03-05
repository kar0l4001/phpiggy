<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Validator;
use Framework\Rules\{DateFormatRule, RequiredRule, EmailRule, InRule, LengthMaxRule, MatchRule, MinRule, NumericRule, UrlRule};

class ValidatorService
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();

        $this->validator->add('required', new RequiredRule());
        $this->validator->add('email', new EmailRule());
        $this->validator->add('min', new MinRule());
        $this->validator->add('in', new InRule());
        $this->validator->add('url', new UrlRule());
        $this->validator->add('match', new MatchRule());
        $this->validator->add('length', new LengthMaxRule());
        $this->validator->add('numeric', new NumericRule());
        $this->validator->add('date', new DateFormatRule());
    }

    public function validateRegister(array $formData)
    {
        $this->validator->validate($formData, [
            'email' => ['required', 'email'],
            'age' => ['required', 'min:18'],
            'country' => ['required', 'in:USA,Canada,Mexico'],
            'socialMediaURL' => ['required', 'url'],
            'password' => ['required'],
            'confirmPassword' => ['required', 'match:password'],
            'tos' => ['required']
        ]);
    }

    public function validateLogin(array $loginData)
    {
        $this->validator->validate($loginData, [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
    }

    public function validateTransaction(array $formData)
    {
        $this->validator->validate($formData, [
            'description' => ['required', 'length:255'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date:Y-m-d'],
        ]);
    }
}
