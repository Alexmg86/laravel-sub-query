<?php

namespace Alexmg86\LaravelSubQuery\Collection;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class LaravelSubQueryCollection extends Collection {

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
    
}
