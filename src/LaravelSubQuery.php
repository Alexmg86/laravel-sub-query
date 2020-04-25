<?php

namespace Alexmg86\LaravelSubQuery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\QueriesRelationships;
use Illuminate\Support\Str;

class LaravelSubQuery extends Builder
{
    use QueriesRelationships;

    /**
     * The relationship sums that should be eager loaded on every query.
     *
     * @var array
     */
    protected $withSum = [];

    /**
     * The relationship min value that should be eager loaded on every query.
     *
     * @var array
     */
    protected $withMin = [];

    /**
     * The relationship max value that should be eager loaded on every query.
     *
     * @var array
     */
    protected $withMax = [];

    /**
     * The relationship avg value that should be eager loaded on every query.
     *
     * @var array
     */
    protected $withAvg = [];

    public function withSum($relations)
    {
        return $this->withSubQuery($relations, 'sum');
    }

    public function withMin($relations)
    {
        return $this->withSubQuery($relations, 'min');
    }

    public function withMax($relations)
    {
        return $this->withSubQuery($relations, 'max');
    }

    public function withAvg($relations)
    {
        return $this->withSubQuery($relations, 'avg');
    }

    protected function withSubQuery($relations, $type)
    {
        if (empty($relations)) {
            return $this;
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from.'.*']);
        }

        $relations = is_array($relations) ? $relations : array_slice(func_get_args(), 0, 1);

        foreach ($this->parseForSubQueryRelations($relations) as $name => $constraints) {
            $nameExplode = explode(':', $name);
            $name = $nameExplode[0];
            $columns = isset($nameExplode[1]) ? explode(',', $nameExplode[1]) : [];

            $relation = $this->getRelationWithoutConstraints($name);

            // Here we will get the relationship sum query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // sum query. We will normalize the relation name then append _{column}_sum as the name.
            foreach ($columns as $column) {
                $query = $relation->getRelationExistenceSubQuery(
                // $query = $relation->getRelationExistenceCountQuery(
                    $relation->getRelated()->newQuery(), $this, $column, $type
                );

                $query->callScope($constraints);

                $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

                $query->orders = null;

                $query->setBindings([], 'order');

                if (count($query->columns) > 1) {
                    $query->columns = [$query->columns[0]];

                    $query->bindings['select'] = [];
                }

                // Finally we will add the proper result column alias to the query and run the subselect
                // statement against the query builder. Then we will return the builder instance back
                // to the developer for further constraint chaining that needs to take place on it.
                $this->selectSub($query, Str::snake($name.'_'.$column.'_'.$type));
            }
        }

        return $this;
    }

    /**
     * Parse a list of relations into individuals.
     *
     * @param  array  $relations
     * @return array
     */
    protected function parseForSubQueryRelations(array $relations)
    {
        $results = [];

        foreach ($relations as $name => $constraints) {

            // If the "name" value is a numeric key, we can assume that no constraints
            // have been specified. We will just put an empty Closure there so that
            // we can treat these all the same while we are looping through them.
            if (is_numeric($name)) {
                $name = $constraints;

                [$name, $constraints] = [$name, static function () {
                }];
            }

            $results[$name] = $constraints;
        }

        return $results;
    }

    public function setWithSum($withSum)
    {
        return $this->withSum($withSum);
    }

    public function setWithMin($withMin)
    {
        return $this->withMin($withMin);
    }

    public function setWithMax($withMax)
    {
        return $this->withMax($withMax);
    }

    public function setWithAvg($withAvg)
    {
        return $this->withAvg($withAvg);
    }
}
