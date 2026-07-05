<?php

namespace App\Database;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;

class OracleQueryGrammar extends Grammar
{
    public function compileSelect(Builder $query): string
    {
        if (($query->unions || $query->havings) && $query->aggregate) {
            return $this->compileUnionAggregate($query);
        }

        $original = $query->columns;

        if (is_null($query->columns)) {
            $query->columns = ['*'];
        }

        $components = $this->compileComponents($query);
        $sql = trim($this->concatenate($components));

        $query->columns = $original;

        if ($query->limit !== null || $query->offset !== null) {
            $sql = $this->compileOracleLimit($sql, $query->limit, $query->offset);
        }

        return $sql;
    }

    protected function compileOracleLimit(string $sql, $limit, $offset): string
    {
        $offset = (int) ($offset ?? 0);
        $limit = $limit !== null ? (int) $limit : null;

        if ($offset === 0 && $limit !== null) {
            return 'select * from ('.$sql.') where rownum <= '.$limit;
        }

        if ($limit !== null) {
            $max = $offset + $limit;

            return 'select * from (select findit_row_.*, rownum findit_rnum_ from ('.$sql.') findit_row_ where rownum <= '.$max.') where findit_rnum_ > '.$offset;
        }

        return 'select * from (select findit_row_.*, rownum findit_rnum_ from ('.$sql.') findit_row_) where findit_rnum_ > '.$offset;
    }

    public function compileLimit(Builder $query, $limit): string
    {
        return '';
    }

    public function compileOffset(Builder $query, $offset): string
    {
        return '';
    }

    public function compileLock(Builder $query, $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        return $value ? 'for update' : '';
    }

    public function compileExists(Builder $query): string
    {
        $existsQuery = clone $query;
        $existsQuery->columns = [];

        return $this->compileSelect($existsQuery->selectRaw('1 as "exists"')->limit(1));
    }

    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s';
    }

    protected function wrapValue($value): string
    {
        if ($value === '*') {
            return $value;
        }

        return '"'.str_replace('"', '""', strtoupper($value)).'"';
    }
}
