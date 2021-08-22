<?php

namespace Domains\Context\BankAccount\Infrastructure\Framework\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountErrorsResource extends JsonResource
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
      '_type'               => 'BankAccount',
      'errors' =>           $this->resource,
    ];
  }
}
