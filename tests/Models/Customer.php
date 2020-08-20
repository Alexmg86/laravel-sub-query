<?php

namespace Alexmg86\LaravelSubQuery\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $table = 'customers';
    public $timestamps = false;
    protected $fillable = ['country_id', 'name'];
}
