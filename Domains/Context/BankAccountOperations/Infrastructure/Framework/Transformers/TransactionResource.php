<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
      '_type'                       => 'Transaction',
      'id'                          => $this->getId(),
      'account_id'                  => $this->getAccountId(),
      'balance'                     => $this->getBalance()->value,
      'description'                 => $this->getDescription(),
      'check_path_file'             => $this->getCheckPathFile(),
      'approved'                    => $this->getApproved()
    ];
  }
}
