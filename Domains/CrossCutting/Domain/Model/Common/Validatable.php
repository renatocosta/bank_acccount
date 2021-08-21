<?php

namespace Domains\CrossCutting\Domain\Model\Common;

interface Validatable
{

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return array
     */
    public function getErrors(): array;  

}