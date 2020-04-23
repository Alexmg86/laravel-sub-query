<?php

namespace Alexmg86\LaravelSubQuery\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

trait LaravelSubQueryRelation  {

	/**
     * Add the constraints for a relationship sum of column query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Builder  $parentQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationExistenceSumQuery(Builder $query, Builder $parentQuery, $column)
    {
        return $this->getRelationExistenceQuery(
            $query, $parentQuery, new Expression('sum('.$column.')')
        )->setBindings([], 'select');
    }
}
