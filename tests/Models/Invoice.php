<?php

namespace Alexmg86\LaravelSubQuery\Tests\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use LaravelSubQueryTrait;

    public $table = 'invoices';

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(Item::class, 'invoice_id');
    }

    public function goods()
    {
        return $this->hasMany(Good::class, 'invoice_id');
    }

    public function allGoods()
    {
        return $this->goods()->withoutGlobalScopes();
    }
}
