<?php

namespace Alexmg86\LaravelSubQuery\Traits;

use Alexmg86\LaravelSubQuery\Collection\LaravelSubQueryCollection;
use Alexmg86\LaravelSubQuery\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\LaravelSubQueryCache;

trait LaravelSubQueryTrait
{
    /**
     * Eager load relation sums on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadSum($relations, $column = null)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->newCollection([$this])->loadSum($relations);

        return $this;
    }

    /**
     * Eager load relation min value on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadMin($relations, $column = null)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->newCollection([$this])->loadMin($relations);

        return $this;
    }

    /**
     * Eager load relation max value on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadMax($relations, $column = null)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->newCollection([$this])->loadMax($relations);

        return $this;
    }

    /**
     * Eager load relation max value on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadAvg($relations, $column = null)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->newCollection([$this])->loadAvg($relations);

        return $this;
    }

    /**
     * Eager load latest relation on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadOneLatest($relations)
    {
        return $this->newCollection([$this])->loadOneLatest($relations);
    }

    /**
     * Eager load oldest relation on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadOneOldest($relations)
    {
        return $this->newCollection([$this])->loadOneOldest($relations);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        $builder = new LaravelSubQueryCache($conn, $grammar, $conn->getPostProcessor());

        if (isset($this->rememberFor)) {
            $builder->remember($this->rememberFor);
        }

        if (isset($this->rememberCacheTag)) {
            $builder->cacheTags($this->rememberCacheTag);
        }

        if (isset($this->rememberCachePrefix)) {
            $builder->prefix($this->rememberCachePrefix);
        }

        if (isset($this->rememberCacheDriver)) {
            $builder->cacheDriver($this->rememberCacheDriver);
        }

        return $builder;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($builder)
    {
        $newEloquentBuilder = new LaravelSubQuery($builder);
        $newEloquentBuilder->setModel($this);

        if (isset($this->withSum)) {
            $newEloquentBuilder->setWithSum($this->withSum);
        }

        if (isset($this->withMin)) {
            $newEloquentBuilder->setWithMin($this->withMin);
        }

        if (isset($this->withMax)) {
            $newEloquentBuilder->setWithMax($this->withMax);
        }

        if (isset($this->withAvg)) {
            $newEloquentBuilder->setWithAvg($this->withAvg);
        }

        return $newEloquentBuilder;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new LaravelSubQueryCollection($models);
    }
}
