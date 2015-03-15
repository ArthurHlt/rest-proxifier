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

namespace Arthurh\RestProxifier\Ui;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class TemplateEngine
{

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var ExtensionInterface[]
     */
    private $extensionPlate;

    public function __construct()
    {
        $this->loadEngine();
    }

    private function loadEngine()
    {
        $this->engine = new Engine(__DIR__ . '/../../../../view');
        $this->engine->setFileExtension(null);
    }

    /**
     * @return \League\Plates\Extension\ExtensionInterface[]
     */
    public function getExtensionPlate()
    {
        return $this->extensionPlate;
    }

    /**
     * @param \League\Plates\Extension\ExtensionInterface[] $extensionPlate
     */
    public function setExtensionPlate(array $extensionPlate)
    {
        $this->extensionPlate = $extensionPlate;
        $this->loadExtension();
    }

    public function loadExtension()
    {
        foreach ($this->extensionPlate as $extensionPlate) {
            $this->engine->loadExtension($extensionPlate);
        }
    }

    /**
     * @return Engine
     */
    public function getEngine()
    {
        return $this->engine;
    }


}
