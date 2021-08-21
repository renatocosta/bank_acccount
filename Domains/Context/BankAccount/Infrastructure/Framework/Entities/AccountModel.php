<?php

namespace Domains\Context\BankAccount\Infrastructure\Framework\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountModel extends Model
{

    protected $table = 'accounts';

    protected $fillable = ['customer_id', 'account_name', 'current_balance'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            //$model->created_by = \Auth::User()->id;
        });
    }
}
