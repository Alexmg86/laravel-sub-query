<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Alexmg86\LaravelSubQuery\Tests\Models\Good;
use Alexmg86\LaravelSubQuery\Tests\Models\Invoice;
use Alexmg86\LaravelSubQuery\Tests\Models\Item;
use Illuminate\Database\Eloquent\Builder;

class LaravelSubQueryWithMinTest extends DatabaseTestCase
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

        $results = Invoice::withMin('items:price,price2');

        $this->assertEquals([
            ['id' => 1, 'name' => 'text_name', 'items_price_min' => 1, 'items_price2_min' => 2],
        ], $results->get()->toArray());
    }

    public function testWithConditions()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::withMin(['items:price', 'goods:price,price2' => function (Builder $query) {
            $query->where('price', '<', 5);
        }]);

        $this->assertEquals([
            ['id' => 1, 'name' => 'text_name', 'items_price_min' => 1, 'goods_price_min' => 1, 'goods_price2_min' => 2],
        ], $results->get()->toArray());
    }

    public function testWithSelect()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::select(['id'])->withMin('items:price as price_min');

        $this->assertEquals([
            ['id' => 1, 'price_min' => 1],
        ], $results->get()->toArray());
    }

    public function testLoadMin()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::first();
        $results->loadMin('items:price');

        $this->assertEquals(['id' => 1, 'name' => 'text_name', 'items_price_min' => 1], $results->toArray());
    }

    public function testLoadMinWithConditions()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::first();
        $results->loadMin(['items:price' => function ($query) {
            $query->where('price', '>', 5);
        }]);

        $this->assertEquals(['id' => 1, 'name' => 'text_name', 'items_price_min' => 6], $results->toArray());
    }

    public function testGlobalScopes()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $result = Invoice::withMin('goods:price')->first();
        $this->assertEquals(1, $result->goods_price_min);

        $result = Invoice::withMin('allGoods:price')->first();
        $this->assertEquals(1, $result->all_goods_price_min);
    }

    public function testSortingScopes()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $result = Invoice::withMin('items:price')->toSql();

        $this->assertSame(
            'select "invoices".*, (select min(price) from "items"
            where "invoices"."id" = "items"."invoice_id") as "items_price_min"
            from "invoices"',
            $result
        );
    }
}
