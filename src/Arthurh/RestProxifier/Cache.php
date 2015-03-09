<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 09/03/2015
 */

namespace Arthurh\RestProxifier;


use orange\cfhelper\CfHelper;
use Stash\Driver\FileSystem;
use Stash\Interfaces\DriverInterface;
use Stash\Pool;

class Cache
{
    /**
     * @var DriverInterface
     */
    private $driver;
    /**
     * @var Pool
     */
    private $pool;

    public function __construct()
    {

    }

    private function loadCacheFolder()
    {
        if (CfHelper::getInstance()->isInCloudFoundry()) {
            $options = array('path' => sys_get_temp_dir());
        } else {
            $folder = __DIR__ . '/../../../cache';
            if (!is_dir($folder)) {
                mkdir($folder);
            }
            $options = array('path' => $folder);
        }
        $this->driver->setOptions($options);
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param DriverInterface $driver
     * @Required
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->loadCacheFolder();
    }

    /**
     * @return Pool
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * @param Pool $pool
     * @Required
     */
    public function setPool(Pool $pool)
    {
        $this->pool = $pool;
    }

}
