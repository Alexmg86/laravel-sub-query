<?php

namespace Alexmg86\LaravelSubQuery\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    public $table = 'goods';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('app', function ($builder) {
            $builder->where('price', '>', 5);
        });
    }
}
