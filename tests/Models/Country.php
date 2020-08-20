<?php

namespace Alexmg86\LaravelSubQuery\Tests\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use LaravelSubQueryTrait;

    public $table = 'countries';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function posts()
    {
        return $this->hasManyThrough(Post::class, Customer::class, 'country_id', 'user_id', 'id', 'id');
    }
}
