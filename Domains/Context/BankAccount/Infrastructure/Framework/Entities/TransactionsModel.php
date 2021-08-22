<?php

namespace Domains\Context\BankAccount\Infrastructure\Framework\Entities;

use Illuminate\Database\Eloquent\Model;

class TransactionsModel extends Model
{

    protected $table = 'transactions';

    protected $fillable = ['account_id', 'balance', 'description', 'check_path_file', 'approved'];
}
