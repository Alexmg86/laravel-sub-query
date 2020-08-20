<?php

namespace Alexmg86\LaravelSubQuery\Tests;

use Alexmg86\LaravelSubQuery\Facades\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\ServiceProvider;
use Alexmg86\LaravelSubQuery\Tests\Models\Country;
use Alexmg86\LaravelSubQuery\Tests\Models\Customer;
use Alexmg86\LaravelSubQuery\Tests\Models\Post;

class LaravelSubQueryLoadOneTest extends DatabaseTestCase
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
        $countriesNames = ['Russia', 'Spain', 'Mexico'];
        foreach ($countriesNames as $countriesName) {
            $country = Country::create(['name' => $countriesName]);

            $customersNames = ['Ivan', 'Juan', 'Julio'];
            foreach ($customersNames as $customersName) {
                $customer = Customer::create([
                    'country_id' => $country->id,
                    'name' => $customersName
                ]);

                $titles = ['title_1', 'title_2', 'title_3'];
                foreach ($titles as $title) {
                    Post::create([
                        'user_id' => $customer->id,
                        'title' => $title
                    ]);
                }
            }
        }
    }

    public function testLoadOneLatest()
    {
        $this->createBasic();

        $countries = Country::all();
        $countries->loadOneLatest('posts');

        foreach ($countries as $country) {
            $this->assertEquals(count($country->posts), 1);
            $this->assertTrue(in_array($country->posts->first()->id, [9, 18, 27]));
            $this->assertEquals($country->posts->first()->title, 'title_3');
        }
    }

    public function testLoadOneOldest()
    {
        $this->createBasic();

        $countries = Country::all();
        $countries->loadOneOldest('posts');

        foreach ($countries as $country) {
            $this->assertEquals(count($country->posts), 1);
            $this->assertTrue(in_array($country->posts->first()->id, [1, 10, 19]));
            $this->assertEquals($country->posts->first()->title, 'title_1');
        }
    }
}
