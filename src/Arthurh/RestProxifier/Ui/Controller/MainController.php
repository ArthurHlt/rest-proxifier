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


class MainController extends AbstractController
{

    public function action()
    {
        $error = null;
        if (!empty($_SESSION['error'])) {
            $error = $_SESSION['error'];
        }
        unset($_SESSION['error']);
        $connection = $this->getRoutesDao()->getDatabaseConnector()->getConnexion();
        return $this->getEngine()->render('main/main.php', [
            'proxies' => $this->getProxies(),
            'error' => $error,
            'asDb' => !empty($connection)
        ]);
    }
}
