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

namespace Arthurh\RestProxifier\Ui\Controller;


class DeleteController extends AbstractController
{

    public function action()
    {
        $route = $this->getArgs();
        $route = $route['route'];
        if ($this->getRoutesDao()->findByRoute($route) !== NULL) {
            $this->getRoutesDao()->deleteRoute($route);
        }
        return $this->getEngine()->render('main/redirect.php');
    }
}
