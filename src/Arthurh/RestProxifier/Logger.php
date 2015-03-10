<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 10/03/2015
 */

namespace Arthurh\RestProxifier;


use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SyslogHandler;
use orange\cfhelper\CfHelper;

class Logger extends \Monolog\Logger
{
    const NAME = 'RestProxifier';
    const FOLDERLOG = '/logs';
    const FILELOG = 'trace.log';

    public function __construct()
    {
        parent::__construct(Logger::NAME);
        $this->loadHandler();
    }

    private function loadHandler()
    {
        if (CfHelper::getInstance()->isInCloudFoundry()) {
            $this->loadHandlerCloudFoundry();
        } else {
            $this->loadHandlerNative();
        }
    }

    private function loadHandlerNative()
    {
        $folderLog = __DIR__ . '/../../..' . Logger::FOLDERLOG;
        if (!is_dir($folderLog)) {
            mkdir($folderLog);
        }
        $file = $folderLog . '/' . Logger::FILELOG;

        $this->pushHandler(new RotatingFileHandler($file, 7));
    }

    private function loadHandlerCloudFoundry()
    {
        $this->pushHandler(new SyslogHandler(Logger::NAME, LOG_USER, \Monolog\Logger::DEBUG, true, LOG_PID | LOG_PERROR));
    }
}
