<?php

namespace Alexmg86\LaravelSubQuery\Traits;

use Alexmg86\LaravelSubQuery\LaravelSubQuery;
use Alexmg86\LaravelSubQuery\Relations\LaravelSubQueryRelation;

trait LaravelSubQueryJoinRelationTrait
{
    use LaravelSubQueryRelation;

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($this->getQuery() instanceof LaravelSubQuery) {
            $this->getQuery()->relationClauses[] = [$method => $parameters];
        }

        return parent::__call($method, $parameters);
    }
}
