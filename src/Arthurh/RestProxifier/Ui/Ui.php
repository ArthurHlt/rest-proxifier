<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 07/03/2015
 */

namespace Arthurh\RestProxifier\Ui;


use Arthurh\RestProxifier\Config;
use Arthurh\RestProxifier\Dao\RoutesDao;
use Arthurh\RestProxifier\Ui\Controller\AbstractController;

class Ui
{

    private $routes;
    private $baseRoute = "/admin";

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RoutesDao
     */
    protected $routesDao;
    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    public function __construct()
    {
    }


    public function getRoute($name)
    {
        return $this->routes[$name]["route"];
    }

    /**
     * @return mixed
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param AbstractController[] $routes
     * @Required
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        foreach ($this->routes as &$route) {
            $route['route'] = $this->baseRoute . $route['route'];
        }
        $this->injectEngineInController();
        $this->injectDaoInController();
    }

    /**
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->baseRoute;
    }

    /**
     * @param string $baseRoute
     */
    public function setBaseRoute($baseRoute)
    {
        $this->baseRoute = $baseRoute;
    }

    /**
     * @return TemplateEngine
     */
    public function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    private function injectEngineInController()
    {
        if (empty($this->templateEngine)) {
            return;
        }
        foreach ($this->routes as &$route) {
            $route['controller']->setTemplateEngine($this->templateEngine);
        }
    }

    private function injectDaoInController()
    {
        if (empty($this->routesDao)) {
            return;
        }
        foreach ($this->routes as &$route) {
            $route['controller']->setRoutesDao($this->routesDao);
        }
    }

    /**
     * @param TemplateEngine $templateEngine
     * @Required
     */
    public function setTemplateEngine(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
        $this->injectEngineInController();
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
        $this->baseRoute = $this->config->get('admin-ui-route', $this->baseRoute);
    }

    /**
     * @return RoutesDao
     */
    public function getRoutesDao()
    {
        return $this->routesDao;
    }

    /**
     * @param RoutesDao $routesDao
     * @Required
     */
    public function setRoutesDao($routesDao)
    {
        $this->routesDao = $routesDao;
        $this->injectDaoInController();
    }

}
