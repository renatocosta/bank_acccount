<?php

namespace Domains\Context\BankAccount\Infrastructure\Framework\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param \Illuminate\Http\Request
   * @return array
   */
  public function toArray($request)
  {

    return [
      '_type'               => 'Account',
      'id'                  => $this->getId(),
      'customer_id'         => $this->getCustomerId(),
      'account_name'        => $this->getAccountName(),
      'balance'             => $this->getBalance()->value
    ];
  }
}
