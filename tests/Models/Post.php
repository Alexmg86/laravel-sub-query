<?php

namespace Alexmg86\LaravelSubQuery\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $table = 'posts';
    public $timestamps = false;
    protected $fillable = ['user_id', 'title'];
}
