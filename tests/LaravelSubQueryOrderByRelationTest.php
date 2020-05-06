<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Alexmg86\LaravelSubQuery\Tests\Models\Good;
use Alexmg86\LaravelSubQuery\Tests\Models\Invoice;
use Alexmg86\LaravelSubQuery\Tests\Models\Item;
use Illuminate\Database\Eloquent\Builder;

class LaravelSubQueryOrderByRelationTest extends DatabaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'laravel-sub-query' => LaravelSubQuery::class,
        ];
    }

    public function testOrderByRelation()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }
        $results = Invoice::orderByRelation('items:price')->get();
        $this->assertEquals([['id' => 1, 'name' => 'text_name', 'items_price_max' => 10]], $results->toArray());

        $results = Invoice::orderByRelation(['items:price' => function (Builder $query) {
            $query->where('price', '<', 6);
        }, 'desc', 'max'])->get();
        $this->assertEquals([['id' => 1, 'name' => 'text_name', 'items_price_max' => 5]], $results->toArray());

        $results = Invoice::orderByRelation('goods:price')->get();
        $this->assertEquals([['id' => 1, 'name' => 'text_name', 'goods_price_max' => 4]], $results->toArray());

        $results = Invoice::orderByRelation('allGoods:price')->get();
        $this->assertEquals([['id' => 1, 'name' => 'text_name', 'all_goods_price_max' => 10]], $results->toArray());
    }
}
