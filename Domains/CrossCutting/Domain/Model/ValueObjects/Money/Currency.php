<?php

namespace Domains\CrossCutting\Domain\Model\ValueObjects\Money;

class Currency
{

    private $amount;

    public function __construct(string $amount)
    {

        if(!preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $amount) || empty($amount)){
             $this->amount = '0.00';
        } else {
             $this->amount = number_format($amount, 2, '.', '');
        }        

    }

    public function __toString(): string
    {
        return $this->amount;
    }

}