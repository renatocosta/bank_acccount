<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\Entities;

use Illuminate\Database\Eloquent\Model;

class TransactionsModel extends Model
{

    public $timestamps = false;

    protected $table = 'transactions';

    protected $fillable = ['account_id', 'balance', 'description', 'check_path_file', 'approved'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
