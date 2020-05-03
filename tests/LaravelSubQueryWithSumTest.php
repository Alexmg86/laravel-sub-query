<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Alexmg86\LaravelSubQuery\Tests\Models\Good;
use Alexmg86\LaravelSubQuery\Tests\Models\Invoice;
use Alexmg86\LaravelSubQuery\Tests\Models\Item;
use Illuminate\Database\Eloquent\Builder;

class LaravelSubQueryWithSumTest extends DatabaseTestCase
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

    public function testBasic()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::withSum('items:price,price2');

        $this->assertEquals([
            ['id' => 1, 'name' => 'text_name', 'items_price_sum' => 55, 'items_price2_sum' => 65],
        ], $results->get()->toArray());
    }

    public function testWithConditions()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::withSum(['items:price', 'goods:price,price2' => function (Builder $query) {
            $query->where('price', '>', 6);
        }]);

        $this->assertEquals([
            ['id' => 1, 'name' => 'text_name', 'items_price_sum' => 55, 'goods_price_sum' => 34, 'goods_price2_sum' => 38],
        ], $results->get()->toArray());
    }

    public function testWithSelect()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::select(['id'])->withSum('items:price as price_sum');

        $this->assertEquals([
            ['id' => 1, 'price_sum' => 55],
        ], $results->get()->toArray());
    }

    public function testLoadSum()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::first();
        $results->loadSum('items:price');

        $this->assertEquals(['id' => 1, 'name' => 'text_name', 'items_price_sum' => 55], $results->toArray());
    }

    public function testLoadSumWithConditions()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::first();
        $results->loadSum(['items:price' => function ($query) {
            $query->where('price', '>', 5);
        }]);

        $this->assertEquals(['id' => 1, 'name' => 'text_name', 'items_price_sum' => 40], $results->toArray());
    }

    public function testGlobalScopes()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $result = Invoice::withSum('goods:price')->first();
        $this->assertEquals(40, $result->goods_price_sum);

        $result = Invoice::withSum('allGoods:price')->first();
        $this->assertEquals(55, $result->all_goods_price_sum);
    }

    public function testSortingScopes()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $result = Invoice::withSum('items:price')->toSql();

        $this->assertSame('select "invoices".*, (select sum(price) from "items" where "invoices"."id" = "items"."invoice_id") as "items_price_sum" from "invoices"', $result);
    }
}
