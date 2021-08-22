<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionErrorsResource extends JsonResource
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
      '_type'               => 'BankAccountOperations',
      'errors' =>           $this->resource,
    ];
  }
}
