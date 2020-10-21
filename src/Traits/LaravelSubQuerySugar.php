<?php

namespace Alexmg86\LaravelSubQuery\Traits;

use Illuminate\Database\Query\Expression;

trait LaravelSubQuerySugar
{
    /**
     * like query left side
     */
    public function likeLeft($column, $value)
    {
        return $this->like($column, '%' . $value, true);
    }

    /**
     * like query right side
     */
    public function likeRight($column, $value)
    {
        return $this->like($column, $value . '%', true);
    }

    /**
     * like query both side
     */
    public function like($column, $value, $condition = false)
    {
        $value = $condition ? $value : '%' . $value . '%';
        return $this->query->where($column, 'like', $value);
    }

    /**
     * casting column in query
     * date, datetime, time, char, signed, unsigned, binary
     */
    public function castColumn($column, $type = null)
    {
        $columns = [];
        if (!is_array($column)) {
            $columns[$column] = $type;
        } else {
            $columns = $column;
        }

        foreach ($columns as $key => $type) {
            $this->addSelect(new Expression("CAST($key as $type) as $key"));
        }
        return $this;
    }
}
