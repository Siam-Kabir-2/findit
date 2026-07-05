<?php

namespace App\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Grammars\Grammar as QueryGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Grammars\Grammar as SchemaGrammar;

class OracleConnection extends Connection
{
    protected function getDefaultQueryGrammar(): QueryGrammar
    {
        return new OracleQueryGrammar($this);
    }

    protected function getDefaultSchemaGrammar(): SchemaGrammar
    {
        return new OracleSchemaGrammar($this);
    }

    protected function getDefaultPostProcessor(): Processor
    {
        return new OracleProcessor;
    }

    public function getDriverName(): string
    {
        return 'oracle';
    }
}
