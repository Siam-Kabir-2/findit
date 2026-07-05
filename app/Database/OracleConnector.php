<?php

namespace App\Database;

use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;
use PDO;

class OracleConnector extends Connector implements ConnectorInterface
{
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];

    public function connect(array $config): PDO
    {
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        if (isset($config['schema'])) {
            $connection->exec("ALTER SESSION SET CURRENT_SCHEMA = {$config['schema']}");
        }

        if (isset($config['session'])) {
            foreach ($config['session'] as $key => $value) {
                $connection->exec("ALTER SESSION SET {$key} = '{$value}'");
            }
        }

        return $connection;
    }

    protected function getDsn(array $config): string
    {
        if (! empty($config['tns'])) {
            return "oci:dbname={$config['tns']};charset={$config['charset']}";
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '1521';
        $database = $config['database'] ?? 'XE';
        $charset = $config['charset'] ?? 'AL32UTF8';

        $tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$host})(PORT={$port}))(CONNECT_DATA=(SERVICE_NAME={$database})))";

        return "oci:dbname={$tns};charset={$charset}";
    }
}
