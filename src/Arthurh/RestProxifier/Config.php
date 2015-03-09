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


class Config extends \Noodlehaus\Config
{
    public function __construct()
    {
        parent::__construct(__DIR__ . '/../../../config');
    }
}
