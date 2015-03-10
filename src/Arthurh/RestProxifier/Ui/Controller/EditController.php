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


class EditController extends AbstractController
{
    public function action()
    {
        if (empty($_POST['sent'])) {
            return $this->getEngine()->render('main/redirect.php');
        }
        $route = $_POST['route'];
        $api = $_POST['api'];
        $responseContent = (!empty($_POST['responseContent'])) ? $_POST['responseContent'] : null;
        $error = null;
        if (empty($api)) {
            $error[] = "Api can't be empty.";
        }
        if (!$this->routeExist($route)) {
            $error[] = "Route '$route' doesn't exist in database (proxies which not created by database can't be editable).'";
        }
        $_SESSION['error'] = $error;
        if (!empty($error)) {
            return $this->getEngine()->render('main/redirect.php');
        }
        $this->getRoutesDao()->updateRoute($route, $api, null, null, $responseContent);
        return $this->getEngine()->render('main/redirect.php');
    }
}
