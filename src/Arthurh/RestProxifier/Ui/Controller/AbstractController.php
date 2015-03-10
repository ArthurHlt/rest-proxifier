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

namespace Arthurh\RestProxifier\Ui\Controller;


use Arthurh\RestProxifier\Dao\RoutesDao;
use Arthurh\RestProxifier\Ui\TemplateEngine;

abstract class AbstractController
{

    /**
     * @var TemplateEngine
     */
    protected $templateEngine;

    protected $proxies;
    /**
     * @var RoutesDao
     */
    protected $routesDao;

    protected $args;

    public function getEngine()
    {
        return $this->templateEngine->getEngine();
    }

    /**
     * @return TemplateEngine
     */
    public function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    /**
     * @param TemplateEngine $templateEngine
     */
    public function setTemplateEngine(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
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
     */
    public function setRoutesDao($routesDao)
    {
        $this->routesDao = $routesDao;
    }

    /**
     * @return mixed
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * @param mixed $proxies
     */
    public function setProxies(array $proxies)
    {
        $this->proxies = $proxies;
    }

    abstract public function action();

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    protected function routeExist($routeToFind)
    {
        $proxies = $this->getProxies();
        $routeExtension = $this->getTemplateEngine()->getExtensionPlate();
        $routeExtension = $routeExtension['route'];
        if (preg_match('#' . preg_quote($routeExtension->getUi()->getBaseRoute()) . '.*#i', $routeToFind)) {
            return true;
        }
        foreach ($proxies as $proxy) {
            if ($proxy['route'] == $routeToFind) {
                return true;
            }
        }
        return false;
    }

}
