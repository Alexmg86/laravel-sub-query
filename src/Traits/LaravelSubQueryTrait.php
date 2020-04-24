<?php

namespace Alexmg86\LaravelSubQuery\Traits;

use Alexmg86\LaravelSubQuery\Collection\LaravelSubQueryCollection;
use Alexmg86\LaravelSubQuery\LaravelSubQuery;

trait LaravelSubQueryTrait
{
    use LaravelSubQueryRelationsTrait;

    /**
     * Eager load relation sums on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadSum($relations)
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
    public function loadMin($relations)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->newCollection([$this])->loadMin($relations);

        return $this;
    }

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

        return $newEloquentBuilder;
    }

    public function newCollection(array $models = [])
    {
        return new LaravelSubQueryCollection($models);
    }
}
