<?php

namespace Alexmg86\LaravelSubQuery\Relations;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryJoinRelationTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HasOneSubQuery extends HasOne
{
    use LaravelSubQueryJoinRelationTrait;
}
