<?php

namespace Alexmg86\LaravelSubQuery\Collection;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class LaravelSubQueryCollection extends Collection
{
    /**
     * Load a set of relationship sum of column onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadSum($relations)
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $models = $this->first()->newModelQuery()
            ->whereKey($this->modelKeys())
            ->select($this->first()->getKeyName())
            ->withSum(...func_get_args())
            ->get();

        $attributes = Arr::except(
            array_keys($models->first()->getAttributes()),
            $models->first()->getKeyName()
        );

        $models->each(function ($model) use ($attributes) {
            $this->find($model->getKey())->forceFill(
                Arr::only($model->getAttributes(), $attributes)
            )->syncOriginalAttributes($attributes);
        });

        return $this;
    }

    /**
     * Load a set of relationship min of column onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadMin($relations)
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $models = $this->first()->newModelQuery()
            ->whereKey($this->modelKeys())
            ->select($this->first()->getKeyName())
            ->withMin(...func_get_args())
            ->get();

        $attributes = Arr::except(
            array_keys($models->first()->getAttributes()),
            $models->first()->getKeyName()
        );

        $models->each(function ($model) use ($attributes) {
            $this->find($model->getKey())->forceFill(
                Arr::only($model->getAttributes(), $attributes)
            )->syncOriginalAttributes($attributes);
        });

        return $this;
    }

    /**
     * Load a set of relationship min of column onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadMax($relations)
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $models = $this->first()->newModelQuery()
            ->whereKey($this->modelKeys())
            ->select($this->first()->getKeyName())
            ->withMax(...func_get_args())
            ->get();

        $attributes = Arr::except(
            array_keys($models->first()->getAttributes()),
            $models->first()->getKeyName()
        );

        $models->each(function ($model) use ($attributes) {
            $this->find($model->getKey())->forceFill(
                Arr::only($model->getAttributes(), $attributes)
            )->syncOriginalAttributes($attributes);
        });

        return $this;
    }

    /**
     * Load a set of relationship avg of column onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadAvg($relations)
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $models = $this->first()->newModelQuery()
            ->whereKey($this->modelKeys())
            ->select($this->first()->getKeyName())
            ->withAvg(...func_get_args())
            ->get();

        $attributes = Arr::except(
            array_keys($models->first()->getAttributes()),
            $models->first()->getKeyName()
        );

        $models->each(function ($model) use ($attributes) {
            $this->find($model->getKey())->forceFill(
                Arr::only($model->getAttributes(), $attributes)
            )->syncOriginalAttributes($attributes);
        });

        return $this;
    }

    /**
     * Load a set of relationship with limit onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadLimit($relations)
    {
        if ($this->isNotEmpty()) {
            if (is_string($relations)) {
                $relations = func_get_args();
            }

            $getLimits = $this->getLimits($relations);

            $relations = $getLimits['relations'];
            $limits = $getLimits['limits'];

            $query = $this->first()->newQueryWithoutRelationships()->with($relations);

            $this->items = $query->eagerLoadRelations($this->items);
        }

        foreach ($this as &$item) {
            $relations = [];
            foreach ($limits as $key => $limit) {
                $relations[$key] = $item->$key->take($limit);
            }
            $item->setRelations($relations);
        }

        return $this;
    }

    /**
     * Separate limits and relations
     *
     * @param  array|string  $relations
     * @return array
     */
    private function getLimits($relations)
    {
        $relationsList = [];
        $limits = [];
        foreach ($relations as $key => $value) {
            if (is_string($value)) {
                $value = explode(':', $value);
                $relationsList[$key] = $value[0];
                $limits[$value[0]] = $value[1];
            } else {
                $key = explode(':', $key);
                $relationsList[$key[0]] = $value;
                $limits[$key[0]] = $key[1];
            }
        }
        return ['relations' => $relationsList, 'limits' => $limits];
    }

    /**
     * Load a set of relationship with limit one latest item onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadOneLatest($relations)
    {
        return $this->loadOne($relations);
    }

    /**
     * Load a set of relationship with limit one oldest item onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadOneOldest($relations)
    {
        return $this->loadOne($relations, 'MIN');
    }

    private function loadOne($relations, $type = 'MAX')
    {
        if ($this->isNotEmpty()) {
            if (is_string($relations)) {
                $relations = func_get_args()[0];
            }

            $query = $this->first()->newQueryWithoutRelationships()->with($relations);

            $this->items = $query->eagerLoadRelationsOne($this->items, $type);
        }

        return $this;
    }
}
