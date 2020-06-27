# Laravel Sub Query

![PHP Composer](https://github.com/Alexmg86/laravel-sub-query/workflows/PHP%20Composer/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/alexmg86/laravel-sub-query/v/stable)](https://packagist.org/packages/alexmg86/laravel-sub-query)
[![License](https://poser.pugx.org/alexmg86/laravel-sub-query/license)](https://packagist.org/packages/alexmg86/laravel-sub-query)

## Why is this package needed?

With standard use of Laravel, if you want the sum or find the maximum column value in the related model, you will have two database queries.  
With this methods, it all turns into one query to the database and there is no need to load extra data.  
It is also possible to sort by related models. And this sorting works with all types of relations.  
Added ability to load relations with a limit for each model without multiple queries.  
I often use this in my work and I hope it will be useful to you!

## Say thank you

If you liked this package, please give me a star.

## Installation

Install via composer
```bash
composer require alexmg86/laravel-sub-query
```
Use LaravelSubQueryTrait trait in your model.
```php
use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use LaravelSubQueryTrait;
```

## Usage

If you want to get results from a relationship without actually loading them and by one request to the database you may use the these methods, which will place a new columns on your resulting models. For example:
```php
$invoices = Invoice::withSum('items:price')
    ->withMin('items:price')
    ->withMax('items:price')
    ->withAvg('items:price')
    ->get();

echo $invoices[0]->items_price_sum;
echo $invoices[0]->items_price_min;
echo $invoices[0]->items_price_max;
echo $invoices[0]->items_price_avg;
```
### The following methods apply to all methods!!!

You may add the sum for multiple relations as well as add constraints to the queries:
```php
use Illuminate\Database\Eloquent\Builder;

$invoices = Invoice::withSum(['items:price', 'goods:price,price2' => function (Builder $query) {
    $query->where('price','>',6);
}])->get();

echo $invoices[0]->items_price_sum;
echo $invoices[0]->goods_price_sum;
echo $invoices[0]->goods_price2_sum;
```
You may also alias the relationship sum result, allowing multiple sums on the same relationship:
```php
use Illuminate\Database\Eloquent\Builder;

$invoices = Invoice::withSum(['items:price', 'goods:price as sum_goods_price' => function (Builder $query) {
    $query->where('price','!=',1);
}])->get();

echo $invoices[0]->items_price_sum;
echo $invoices[0]->sum_goods_price;
```
If you're combining `withSum` with a `select` statement, ensure that you call `withSum` after the `select` method:
```php
$invoices = Invoice::select(['id'])->withSum('items:price')->get();

echo $invoices[0]->id;
echo $invoices[0]->items_price_sum;
```
In addition, using the `loadSum` method, you may load a relationship sum columns after the parent model has already been retrieved:
```php
$invoice = Invoice::first();
$invoice->loadSum('items:price');
```
If you need to set additional query constraints on the eager loading query, you may pass an array keyed by the relationships you wish to load. The array values should be Closure instances which receive the query builder instance:
```php
$invoice = Invoice::first();
$invoice->loadSum(['items:price' => function ($query) {
    $query->where('price', '>', 5);
}]);
```
And of course it is all compatible with scopes in models.

### Sorting

If you want to sort by field in a related model, simply use the following method:
```php
$invoices = Invoice::orderByRelation('items:price')->get();
```
or with conditions
```php
$invoices = Invoice::orderByRelation(['items:price' => function (Builder $query) {
    $query->where('price', '>', 6);
}, 'desc', 'max'])->get();
```
By default, sorting is by `max` and `desc`, you can choose one of the options `max`, `min`, `sum`, `avg`, `desc`, `acs`.
```php
$invoices = Invoice::orderByRelation('items:price', 'asc', 'sum')->get();
```

### Limit relations

If you want to load related model with limit, simply use the following method:
```php
$invoices = Invoice::all();
$invoices->loadLimit('items:1');
```
or with conditions
```php
$invoices->loadLimit(['items:2', 'goods:1' => function ($query) {
    $query->orderBy('id', 'desc')->where('price', '<', 6);
}]);
```
Note that first you write the name of the relation, and then the number of rows.
