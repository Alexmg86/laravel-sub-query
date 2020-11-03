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

    /**
     * Column math
     * @param  string[]    $columns
     * @param  string      $operator
     * @param  string|null $name
     * @return $this
     */
    public function withMath($columns, $operator = '+', $name = null)
    {
        $default = [
            '+' => 'sum_',
            '-' => 'sub_',
            '*' => 'multi_',
            '/' => 'div_'
        ];

        if (!is_array($columns) || count($columns) < 2) {
            return $this;
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from . '.*']);
        }

        $query = [];
        foreach ($columns as $column) {
            $query[] = $this->query->from . "." . $column;
        }
        $query = implode(" $operator ", $query);

        $asName = implode('_', $columns);
        if ($name) {
            $asName = $name;
        } elseif (array_key_exists($operator, $default)) {
            $asName = $default[$operator] . $asName;
        } else {
            $asName = 'custom_' . $asName;
        }

        return $this->addSelect(new Expression("$query as $asName"));
    }
}
