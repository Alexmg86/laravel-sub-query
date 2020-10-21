<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Alexmg86\LaravelSubQuery\Tests\Models\Invoice;

class LaravelSubQuerySugarTest extends DatabaseTestCase
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
        foreach (['first', 'second', 'third'] as $name) {
            Invoice::create(['name' => $name]);
        }
    }

    public function testLikeLeft()
    {
        $this->createBasic();

        $results = Invoice::likeLeft('name', 'st')->count();

        $this->assertEquals(1, $results);
    }

    public function testLikeRight()
    {
        $this->createBasic();

        $results = Invoice::likeRight('name', 'sec')->count();

        $this->assertEquals(1, $results);
    }

    public function testLike()
    {
        $this->createBasic();

        $results = Invoice::like('name', 'ir')->count();

        $this->assertEquals(2, $results);
    }

    public function testCastColumn()
    {
        foreach (['111', '10', '2', '22'] as $name) {
            Invoice::create(['name' => $name]);
        }

        $results = Invoice::orderByDesc('name')->first();

        $this->assertEquals(22, $results->name);

        $results = Invoice::castColumn('name', 'signed')->orderByDesc('name')->first();

        $this->assertEquals(111, $results->name);
    }
}
