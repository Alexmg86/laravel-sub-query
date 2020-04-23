<?php

namespace Alexmg86\LaravelSubQuery\Traits;

use Alexmg86\LaravelSubQuery\Relations\BelongsToManySubQuery;
use Alexmg86\LaravelSubQuery\Relations\BelongsToSubQuery;
use Alexmg86\LaravelSubQuery\Relations\HasManySubQuery;
use Alexmg86\LaravelSubQuery\Relations\HasOneSubQuery;
use Alexmg86\LaravelSubQuery\Relations\LaravelSubQueryRelation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait LaravelSubQueryRelationsTrait
{

    protected function newHasOne(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasOneSubQuery($query, $parent, $foreignKey, $localKey);
    }

    protected function newHasMany(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return new HasManySubQuery($query, $parent, $foreignKey, $localKey);
    }

    protected function newBelongsTo(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    {
        return new BelongsToSubQuery($query, $child, $foreignKey, $ownerKey, $relation);
    }

    protected function newBelongsToMany(Builder $query, Model $parent, $table, $foreignPivotKey, $relatedPivotKey,
                                        $parentKey, $relatedKey, $relationName = null)
    {
        return new BelongsToManySubQuery($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }
}
