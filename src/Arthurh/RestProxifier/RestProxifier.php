<?php
/**
 * Created by IntelliJ IDEA.
 * User: arthurhalet
 * Date: 07/03/15
 * Time: 00:30
 */

namespace Arthurh\RestProxifier;

use League\Route\RouteCollection;
use Noodlehaus\Config;
use orange\cfhelper\CfHelper;
use Proxy\Factory;
use Proxy\Response\Filter\RemoveEncodingFilter;
use Symfony\Component\HttpFoundation\Request;

class RestProxifier
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var RouteCollection
     */
    private $router;

    /**
     * @var RestProxifier
     */
    private static $_instance = null;

    private function __construct($configFolder)
    {
        $this->loadConfig($configFolder);
        $this->router = new RouteCollection();
        $this->loadAdminUi();
    }

    public static function getInstance($configFolder = null)
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new RestProxifier($configFolder);
        } elseif (!empty($configFolder)) {
            self::$_instance->loadConfig($configFolder);
        }

        return self::$_instance;
    }

    public function loadConfig($configFolder)
    {
        $this->config = new Config($configFolder);
        $this->loadProxyFromCloudFoundryServices();
    }

    private function loadAdminUi()
    {
        if (!$this->config->get('admin-ui', false)) {
            return;
        }
        $this->router->addRoute('GET', $this->config->get('admin-ui-route', '/admin'), function ($req, $resp, $args) {

        });
    }

    private function loadProxyFromCloudFoundryServices()
    {
        $cfHelper = CfHelper::getInstance();
        if (!$cfHelper->isInCloudFoundry()) {
            return;
        }
        $this->config->set('proxyfy',
            array_merge($this->config->get('proxyfy'), $cfHelper->getServiceManager()->getService('.*proxy.*')->getValues())
        );
    }

    public function proxify()
    {
        $proxify = $this->config->get('proxyfy');

        $proxy = Factory::create();

        // Add a response filter that removes the encoding headers.
        $proxy->addResponseFilter(new RemoveEncodingFilter());

        // Create a Symfony request based on the current browser request.
        $request = Request::createFromGlobals();

        foreach ($proxify as $proxyElem) {
            $this->router->addRoute('GET', $proxyElem['route'] . '{pathApi:.*}', function ($req, $resp, $args) use ($proxyElem, &$proxy, &$request) {
                $response = $proxy->forward($request)->to($proxyElem['api'] . $args['pathApi']);
                $response->headers->add(array(
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, DELETE, PUT',
                    'Access-Control-Allow-Headers' => 'x-requested-with'
                ));
                $response->send();
            });
        }
        $dispatcher = $this->router->getDispatcher();
        $request = Request::createFromGlobals();

        $dispatcher->dispatch('GET', $request->getPathInfo());

    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }


}