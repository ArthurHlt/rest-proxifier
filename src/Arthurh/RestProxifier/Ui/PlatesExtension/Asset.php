<?php
namespace Arthurh\RestProxifier\Ui\PlatesExtension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class Asset implements ExtensionInterface
{
    private $dir;
    private $httpName;

    public function __construct()
    {
        $this->dir = '/assets/';
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
        $this->httpName = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $port . $servername;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('asset', [$this, 'getAsset']);
    }

    public function getAsset($asset)
    {
        $assetPath = $this->dir . $asset;
        return $this->httpName . $assetPath;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir . '/';
    }

    /**
     * @return mixed
     */
    public function getHttpName()
    {
        return $this->httpName;
    }

    /**
     * @param mixed $httpName
     */
    public function setHttpName($httpName)
    {
        $this->httpName = $httpName;
    }


}