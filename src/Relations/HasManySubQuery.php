<?php

namespace Alexmg86\LaravelSubQuery\Relations;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryJoinRelationTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManySubQuery extends HasMany
{
    use LaravelSubQueryJoinRelationTrait;
}
