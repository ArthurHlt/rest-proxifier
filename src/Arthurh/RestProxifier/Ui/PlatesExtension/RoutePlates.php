<?php
namespace Arthurh\RestProxifier\Ui\PlatesExtension;

use Arthurh\RestProxifier\Ui\Ui;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;


class RoutePlates implements ExtensionInterface
{

    public $engine;
    public $template;
    private $httpName;
    /**
     * @var Ui
     */
    private $ui;

    public function __construct()
    {
        if (empty($_SERVER["REQUEST_SCHEME"])) {
            if (!empty($_SERVER["HTTPS"])) {
                $_SERVER["REQUEST_SCHEME"] = 'https';
            } else {
                $_SERVER["REQUEST_SCHEME"] = 'http';
            }
        }
        $port = "";
        $servername = dirname($_SERVER['SCRIPT_NAME']);
        if ($servername == '/') {
            $servername = null;
        }
        if (!($_SERVER['SERVER_PORT'] == 80 && $_SERVER["REQUEST_SCHEME"] == 'http') &&
            !($_SERVER['SERVER_PORT'] == 443 && $_SERVER["REQUEST_SCHEME"] == 'https')
        ) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }
        $this->httpName = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $port . $servername . '/index.php';
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('route', [$this, 'getRoute']);
    }

    public function getRoute($name)
    {
        $route = $this->ui->getRoute($name);
        $route = preg_replace('#\{.*\}#i', '', $route);
        return $this->httpName . $route;
    }

    /**
     * @return Ui
     */
    public function getUi()
    {
        return $this->ui;
    }

    /**
     * @param Ui $ui
     * @Required
     */
    public function setUi($ui)
    {
        $this->ui = $ui;
    }


}