<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 08/03/2015
 */

namespace Arthurh\RestProxifier;


use orange\cfhelper\CfHelper;
use orange\cfhelper\services\Service;

class DatabaseConnector
{

    /**
     * @var Config
     */
    private $config;
    /**
     * @var \PDO;
     */
    private $connexion;

    const TABLE_NAME = 'rest_proxify';
    const SENTENCE_PDO = "%s:host=%s;%sdbname=%s";
    const DBTYPE_PG = "(postgres|pgsql)";
    const DBTYPE_MYSQL = "(maria|my)";
    const DBTYPE_ORACLE = "(oracle|oci)";
    const DBTYPE_SQLITE = "sqlite";

    private function loadDatabase()
    {
        if (CfHelper::getInstance()->isInCloudFoundry()) {
            $this->loadDatabaseFromCloudFoundry();
        } else {
            $this->loadDatabaseFromConfig();
        }
    }

    private function loadSchema()
    {
        if (empty($this->connexion)) {
            return;
        }
        $schemaSql = file_get_contents(__DIR__ . '/../../../assets/sql/create_schema.sql');
        $this->connexion->exec(sprintf($schemaSql, self::TABLE_NAME));
    }

    private function parseDbFromService(Service $service)
    {
        $url = $service->getValue('(uri|url)');
        if (!empty($url)) {
            return $this->parseDbUrl($url);
        }
        $host = $service->getValue('.*host.*');
        $port = $service->getValue('.*port.*');
        if (!empty($port)) {
            $port = sprintf("port=%s;", $port);
        } else {
            $port = "";
        }
        $user = $service->getValue('.*(user|login).*');
        $password = $service->getValue('.*pass.*');
        $type = $service->getValue('.*(type).*');
        $database = $service->getValue('.*(name|database|db).*');
        if (empty($type)) {
            $type = $this->getDbTypeFromServiceName($service->getName());
        }
        $toReturn['sentencePdo'] = sprintf(self::SENTENCE_PDO, $type,
            $host, $port, $database);
        $toReturn['user'] = $user;
        $toReturn['pass'] = $password;
        return $toReturn;
    }

    private function getDbTypeFromServiceName($serviceName)
    {
        if (preg_match('#.*' . self::DBTYPE_MYSQL . '.*#i', $serviceName)) {
            return "mysql";
        }
        if (preg_match('#.*' . self::DBTYPE_ORACLE . '.*#i', $serviceName)) {
            return "oci";
        }
        if (preg_match('#.*' . self::DBTYPE_PG . '.*#i', $serviceName)) {
            return "pgsql";
        }
        if (preg_match('#.*' . self::DBTYPE_SQLITE . '.*#i', $serviceName)) {
            return "sqlite";
        }
    }

    private function loadDatabaseFromConfig()
    {
        $db = $this->getConfig()->get('database', false);
        if (empty($db)) {
            return;
        }
        $dbParsed = $this->parseDbUrl($db);
        $this->loadDatabaseFromDbParsed($dbParsed);
    }

    private function parseDbUrl($url)
    {
        if (stristr($url, 'sqlite') !== FALSE) {
            return $url;
        }
        $toReturn = [];
        $port = "";
        $parsedUrl = parse_url($url);

        if (!empty($parsedUrl['port'])) {
            $port = sprintf("port=%s;", $parsedUrl['port']);
        }
        $toReturn['sentencePdo'] = sprintf(self::SENTENCE_PDO, $parsedUrl['scheme'],
            $parsedUrl['host'], $port, str_replace('/', '', $parsedUrl['path']));
        $toReturn['user'] = $parsedUrl['user'];
        $toReturn['pass'] = $parsedUrl['pass'];
        return $toReturn;
    }

    private function loadDatabaseFromCloudFoundry()
    {
        $dbToFind = implode('|', [
            self::DBTYPE_ORACLE,
            self::DBTYPE_MYSQL,
            self::DBTYPE_PG,
            self::DBTYPE_SQLITE
        ]);
        $dbService = CfHelper::getInstance()->getServiceManager()->getService('.*(db|database|(' . $dbToFind . ')).*');
        if ($dbService == null) {
            return;
        }
        $dbParsed = $this->parseDbFromService($dbService);
        $this->loadDatabaseFromDbParsed($dbParsed);
    }

    private function loadDatabaseFromDbParsed($dbParsed)
    {
        if (is_array($dbParsed)) {
            $this->connexion = new \PDO($dbParsed['sentencePdo'], $dbParsed['user'], $dbParsed['pass']);
        } else {
            $this->connexion = new \PDO($dbParsed);
        }
        $this->connexion->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @Required
     */
    public function setConfig($config)
    {
        $this->config = $config;
        $this->loadDatabase();
        $this->loadSchema();
    }

    /**
     * @return \PDO
     */
    public function getConnexion()
    {
        return $this->connexion;
    }

}
