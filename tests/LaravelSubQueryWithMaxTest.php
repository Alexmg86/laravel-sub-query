<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LaravelSubQueryWithMaxTest extends DatabaseTestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
        });

        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('price');
            $table->integer('price2');
        });

        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('price');
            $table->integer('price2');
        });
    }

    public function testBasic()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::withMax('items:price,price2');

        $this->assertEquals([
            ['id' => 1, 'name' => 'text_name', 'items_price_max' => 10, 'items_price2_max' => 11],
        ], $results->get()->toArray());
    }

    public function testWithConditions()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::withMax(['items:price', 'goods:price,price2' => function (Builder $query) {
            $query->where('price', '>', 6);
        }]);

        $this->assertEquals([
            ['id' => 1, 'name' => 'text_name', 'items_price_max' => 10, 'goods_price_max' => 10, 'goods_price2_max' => 11],
        ], $results->get()->toArray());
    }

    public function testWithSelect()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::select(['id'])->withMax('items:price');

        $this->assertEquals([
            ['id' => 1, 'items_price_max' => 10],
        ], $results->get()->toArray());
    }

    public function testLoadMax()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::first();
        $results->loadMax('items:price');

        $this->assertEquals(['id' => 1, 'name' => 'text_name', 'items_price_max' => 10], $results->toArray());
    }

    public function testLoadMaxWithConditions()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $results = Invoice::first();
        $results->loadMax(['items:price' => function ($query) {
            $query->where('price', '>', 5);
        }]);

        $this->assertEquals(['id' => 1, 'name' => 'text_name', 'items_price_max' => 10], $results->toArray());
    }

    public function testGlobalScopes()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Good::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $result = Invoice::withMax('goods:price')->first();
        $this->assertEquals(10, $result->goods_price_max);

        $result = Invoice::withMax('allGoods:price')->first();
        $this->assertEquals(10, $result->all_goods_price_max);
    }

    public function testSortingScopes()
    {
        $invoice = Invoice::create(['id' => 1, 'name' => 'text_name']);
        for ($i = 1; $i < 11; $i++) {
            Item::create(['invoice_id' => $invoice->id, 'price' => $i, 'price2' => $i + 1]);
        }

        $result = Invoice::withMax('items:price')->toSql();

        $this->assertSame('select "invoices".*, (select max(price) from "items" where "invoices"."id" = "items"."invoice_id") as "items_price_max" from "invoices"', $result);
    }
}
