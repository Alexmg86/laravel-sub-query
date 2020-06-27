<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Alexmg86\LaravelSubQuery\Tests\Models\Good;
use Alexmg86\LaravelSubQuery\Tests\Models\Invoice;
use Alexmg86\LaravelSubQuery\Tests\Models\Item;

class LaravelSubQueryWithLimitTest extends DatabaseTestCase
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

    private function createBasic()
    {
        for ($id = 1; $id < 5; $id++) {
            $invoice = Invoice::create(['id' => $id, 'name' => 'text_name']);

            for ($i = 1; $i < 11; $i++) {
                Item::create(['invoice_id' => $id, 'price' => $i, 'price2' => $i + 1]);
                Good::create(['invoice_id' => $id, 'price' => $i, 'price2' => $i + 1]);
            }
        }
    }

    public function testBasic()
    {
        $this->createBasic();

        $results = Invoice::all();
        $results->loadLimit('items:1', 'goods:2');

        foreach ($results as $result) {
            $this->assertEquals(count($result->items), 1);
            $this->assertEquals(count($result->goods), 2);
        }
    }

    public function testWithConditions()
    {
        $this->createBasic();

        $results = Invoice::all();
        $results->loadLimit(['items:1', 'goods:2' => function ($query) {
            $query->orderBy('id', 'desc')->where('price', '<', 6);
        }]);

        foreach ($results as $result) {
            $this->assertEquals(count($result->items), 1);
            $this->assertEquals(count($result->goods), 2);

            foreach ($result->goods as $good) {
                $this->assertLessThan(6, $good->price);
            }
        }
    }
}
