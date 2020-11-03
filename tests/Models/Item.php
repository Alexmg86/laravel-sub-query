<?php

namespace Alexmg86\LaravelSubQuery\Tests\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use LaravelSubQueryTrait;

    public $table = 'items';
    public $timestamps = false;
    protected $guarded = ['id'];
}
