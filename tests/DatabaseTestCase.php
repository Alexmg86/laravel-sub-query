<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class DatabaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createInvoicesTable();
        $this->createItemsTable();
        $this->createGoodsTable();
        $this->createCountriesTable();
        $this->createCustomersTable();
        $this->createPostsTable();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', 'true');

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function createInvoicesTable(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
        });
    }

    protected function createItemsTable(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('price');
            $table->integer('price2');
        });
    }

    protected function createGoodsTable(): void
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('price');
            $table->integer('price2');
        });
    }

    protected function createCountriesTable(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
    }

    protected function createCustomersTable(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->string('name');
        });
    }

    protected function createPostsTable(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
        });
    }
}
