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
    protected $withSums = [];

    public function withSum($relations)
    {
        if (empty($relations)) {
            return $this;
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from.'.*']);
        }

        $relations = is_array($relations) ? $relations : func_get_args();

        foreach ($this->parseWithSumRelations($relations) as $name => $constraints) {
            $nameExplode = explode(':', $name);
            $name = $nameExplode[0];
            $columns = isset($nameExplode[1]) ? explode(',', $nameExplode[1]) : [];

            $relation = $this->getRelationWithoutConstraints($name);

            // Here we will get the relationship sum query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // sum query. We will normalize the relation name then append _{column}_sum as the name.
            foreach ($columns as $column) {
                $query = $relation->getRelationExistenceSumQuery(
                // $query = $relation->getRelationExistenceCountQuery(
                    $relation->getRelated()->newQuery(), $this, $column
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
                $this->selectSub($query, Str::snake($name.'_'.$column.'_sum'));
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
    protected function parseWithSumRelations(array $relations)
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
}
