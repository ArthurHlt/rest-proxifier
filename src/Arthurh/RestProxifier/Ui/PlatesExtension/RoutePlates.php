<?php
namespace Arthurh\RestProxifier\Ui\PlatesExtension;

use Arthurh\RestProxifier\Config;
use Arthurh\RestProxifier\Ui\Ui;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;


class RoutePlates implements ExtensionInterface
{

    public $engine;
    public $template;
    private $httpName;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Ui
     */
    private $ui;

    public function __construct()
    {

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

    public function loadHttpName()
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
        if (!($_SERVER['SERVER_PORT'] == 80 && $_SERVER["REQUEST_SCHEME"] == 'http') &&
            !($_SERVER['SERVER_PORT'] == 443 && $_SERVER["REQUEST_SCHEME"] == 'https')
        ) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }
        $fileIndex = '/index.php';
        $config = $this->config;
        if ($config->get('rewriting', false)) {
            $fileIndex = "";
        }
        $this->httpName = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $port . $servername . $fileIndex;
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
        $this->loadHttpName();
    }


}