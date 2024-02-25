<?php

declare(strict_types=1);

namespace Framework\Contracts;

interface RuleInterface
{
    public function validate(array $data, string $field, array $parameters): bool;

    public function getMessage(array $data, string $field, array $parameters): string;
}
