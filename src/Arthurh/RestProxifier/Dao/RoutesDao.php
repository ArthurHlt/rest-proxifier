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

namespace Arthurh\RestProxifier\Dao;


use Arthurh\RestProxifier\DatabaseConnector;

class RoutesDao
{
    /**
     * @var DatabaseConnector
     */
    private $databaseConnector;


    /**
     * @return array
     */
    public function getRoutes()
    {
        $connexion = $this->getDatabaseConnector()->getConnexion();
        if (empty($connexion)) {
            return array();
        }
        $routes = [];
        $sql = sprintf("SELECT * FROM %s", DatabaseConnector::TABLE_NAME);
        $query = $connexion->query($sql);

        while ($data = $query->fetch()) {
            $intermediate = null;
            $intermediate['api'] = $data['api'];
            $intermediate['route'] = $data['route'];
            $intermediate['from'] = 'Database';

            $intermediate['request-header'] = $data['request_header'];
            if (!empty($intermediate['request-header'])) {
                $intermediate['request-header'] = json_decode($intermediate['request-header'], true);
            }
            $intermediate['response-header'] = $data['response_header'];
            if (!empty($intermediate['response-header'])) {
                $intermediate['response-header'] = json_decode($intermediate['response-header'], true);
            }
            $intermediate['response-content'] = $data['response_content'];
            $routes[] = $intermediate;
        }
        return $routes;
    }

    public function findByRoute($route)
    {

        $connexion = $this->getDatabaseConnector()->getConnexion();
        if (empty($connexion)) {
            return array();
        }
        $sql = sprintf("SELECT * FROM %s WHERE route=:route", DatabaseConnector::TABLE_NAME);
        $sth = $connexion->prepare($sql);
        $sth->bindParam(':route', $route);
        $sth->execute();
        $data = $sth->fetch();
        if (empty($data)) {
            return null;
        }
        $route = [];
        $route['api'] = $data['api'];
        $route['route'] = $data['route'];
        $route['from'] = 'Database';
        $route['request-header'] = $data['request_header'];
        if (!empty($route['request-header'])) {
            $route['request-header'] = json_decode($route['request-header'], true);
        }
        $route['response-header'] = $data['response_header'];
        if (!empty($route['response-header'])) {
            $route['response-header'] = json_decode($route['response-header'], true);
        }
        $route['response-content'] = $data['response_content'];

        return $route;

    }

    public function updateRoute($route, $api, $responseHeaders = null, $requestHeaders = null, $responseContent = null)
    {
        $connexion = $this->databaseConnector->getConnexion();
        $sql = sprintf("UPDATE %s SET api=:api, request_header=:request_header,
        response_header=:response_header, response_content=:response_content WHERE route=:route", DatabaseConnector::TABLE_NAME);
        $sth = $connexion->prepare($sql);
        $sth->bindParam(':route', $route);
        $sth->bindParam(':api', $api);
        $sth->bindParam(':request_header', $requestHeaders);
        $sth->bindParam(':response_header', $responseHeaders);
        $sth->bindParam(':response_content', $responseContent);
        $sth->execute();
    }

    public function deleteRoute($route)
    {
        $connexion = $this->databaseConnector->getConnexion();
        $sql = sprintf("DELETE FROM %s WHERE route=:route", DatabaseConnector::TABLE_NAME);
        $sth = $connexion->prepare($sql);
        $sth->bindParam(':route', $route);
        $sth->execute();
    }

    public function addRoute($route, $api, $responseHeaders = null, $requestHeaders = null, $responseContent = null)
    {
        $connexion = $this->databaseConnector->getConnexion();
        $sql = sprintf("INSERT INTO %s (route, api, request_header, response_header, response_content)
        VALUES (:route, :api, :request_header, :response_header, :response_content)", DatabaseConnector::TABLE_NAME);
        $sth = $connexion->prepare($sql);
        $sth->bindParam(':route', $route);
        $sth->bindParam(':api', $api);
        $sth->bindParam(':request_header', $requestHeaders);
        $sth->bindParam(':response_header', $responseHeaders);
        $sth->bindParam(':response_content', $responseContent);
        $sth->execute();
    }

    /**
     * @return DatabaseConnector
     */
    public function getDatabaseConnector()
    {
        return $this->databaseConnector;
    }

    /**
     * @param DatabaseConnector $databaseConnector
     * @Required
     */
    public function setDatabaseConnector(DatabaseConnector $databaseConnector)
    {
        $this->databaseConnector = $databaseConnector;
    }


}
