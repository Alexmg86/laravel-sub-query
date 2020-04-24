<?php

namespace Alexmg86\LaravelSubQuery\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

trait LaravelSubQueryRelation
{
    /**
     * Add the constraints for a relationship sum of column query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Builder  $parentQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationExistenceSubQuery(Builder $query, Builder $parentQuery, $column, $type)
    {
        return $this->getRelationExistenceQuery(
            $query, $parentQuery, new Expression(''.$type.'('.$column.')')
        )->setBindings([], 'select');
    }
}
