<?php

namespace Alexmg86\LaravelSubQuery\Relations;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryJoinRelationTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongsToSubQuery extends BelongsTo
{
    use LaravelSubQueryJoinRelationTrait;
}
