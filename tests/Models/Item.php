<?php
namespace Alexmg86\LaravelSubQuery\Tests\Models;


use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $table = 'items';
    public $timestamps = false;
    protected $guarded = ['id'];
}