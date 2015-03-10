<?php
/**
 * Created by IntelliJ IDEA.
 * User: arthurhalet
 * Date: 07/03/15
 * Time: 00:30
 */

namespace Arthurh\RestProxifier;

use Arthurh\RestProxifier\Dao\RoutesDao;
use Arthurh\RestProxifier\Ui\Ui;
use League\Route\Http\Exception\NotFoundException;
use League\Route\RouteCollection;
use orange\cfhelper\CfHelper;
use Proxy\Proxy;
use Proxy\Response\Filter\RemoveEncodingFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VectorFace\Whip\Whip;

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
     * @var Proxy
     */
    private $proxy;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Ui
     */
    private $ui;

    /**
     * @var RoutesDao
     */
    private $routesDao;
    /**
     * @var DatabaseConnector
     */
    private $databaseConnector;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Logger
     */
    private $logger;
    public static $NO_CACHING = [
        "PUT",
        "DELETE",
        "POST"
    ];
    const SENTENCELOG = "User with ip '%s' ask for the page: '%s'. Resquest detail after.";

    public function __construct($configFolder = null)
    {

        $this->proxy = new Proxy(new AdapterProxy());
        $this->proxy->addResponseFilter(new RemoveEncodingFilter());
        $this->request = Request::createFromGlobals();
    }


    private function loadAdminUi()
    {
        if (!$this->config->get('admin-ui', false)) {
            return;
        }
        $routes = $this->getUi()->getRoutes();
        foreach ($routes as $route) {

            $this->router->addRoute('GET', $route['route'], function (Request $req, Response $resp, $args) use ($route) {
                $route['controller']->setProxies($this->config->get('proxyfy'));
                $route['controller']->setArgs($args);
                $resp->setContent($route['controller']->action());
                return $resp;
            });
        }

    }

    private function loadProxyFromCloudFoundryServices()
    {
        $cfHelper = CfHelper::getInstance();
        if (!$cfHelper->isInCloudFoundry()) {
            return;
        }
        $serviceProxy = $cfHelper->getServiceManager()->getService('.*proxy.*');
        if (empty($serviceProxy)) {
            return;
        }
        $proxies = $serviceProxy->getValues();
        $proxiesFinal = [];
        foreach ($proxies as $proxy) {
            $proxy['from'] = 'cfservice';
            $proxiesFinal[] = $proxy;
        }
        $this->config->set('proxyfy',
            array_merge($this->config->get('proxyfy'), $proxiesFinal)
        );
    }

    private function loadProxyFromDatabase()
    {
        $this->config->set('proxyfy',
            array_merge($this->config->get('proxyfy'), $this->routesDao->getRoutes())
        );
    }

    private function registerApiRoute()
    {
        $proxify = $this->config->get('proxyfy');
        $restProxifier = $this;
        foreach ($proxify as $proxyElem) {
            $this->router->addRoute('GET', $proxyElem['route'] . '{pathApi:.*}', function ($req, $resp, $args) use (&$restProxifier, $proxyElem) {
                $request = $restProxifier->getRequest();
                $itemName = $request->getMethod() . '/' . md5($request->getUri());
                $item = null;
                $toUri = $proxyElem['api'] . $args['pathApi'];

                $cachingTime = $restProxifier->getConfig()->get('caching-time', '10 minutes');
                $cachingTimeInSecond = 0;
                if (!empty($cachingTime)) {
                    $cachingTimeInSecond = strtotime('+' . $cachingTime, 0);
                }

                $whip = new Whip();
                $userIp = $whip->getIpAddress();
                $userIp = ($userIp === false) ? $_SERVER['REMOTE_ADDR'] : $userIp;
                if ($restProxifier->getConfig()->get('log-request', true)) {
                    $restProxifier->getLogger()->addNotice(sprintf(RestProxifier::SENTENCELOG, $userIp, $toUri));
                    $restProxifier->getLogger()->addNotice($restProxifier->getRequest()->__toString());
                }

                if (!in_array($request->getMethod(), RestProxifier::$NO_CACHING)) {
                    $item = $restProxifier->getCache()->getPool()->getItem($itemName);
                    $data = $item->get();
                    if (!$item->isMiss()) {
                        $restProxifier->injectCorsByPass($restProxifier->getRequest(), $data, $cachingTimeInSecond);

                        return $data;
                    }
                }

                if (!empty($proxyElem['request-header'])) {
                    $restProxifier->getRequest()->headers->add($proxyElem['request-header']);
                }

                $response = $restProxifier->getProxy()->forward($restProxifier->getRequest())->to($toUri);
                if (!empty($proxyElem['response-content'])) {
                    $response->setContent($proxyElem['response-content']);
                }
                if (!empty($proxyElem['response-header'])) {
                    $response->headers->add($proxyElem['response-header']);
                }
                $restProxifier->injectCorsByPass($restProxifier->getRequest(), $response, $cachingTimeInSecond);

                if (!in_array($request->getMethod(), RestProxifier::$NO_CACHING)) {
                    if (empty($cachingTimeInSecond)) {
                        return $response;
                    }
                    $item->set($response, $cachingTimeInSecond);
                }

                return $response;
            });
        }
    }

    public function injectCorsByPass(Request $request, Response $response, $cachingTimeInSecond = 0)
    {
        $response->headers->add(array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true'
        ));
        $requestMethod = $request->headers->get('Access-Control-Request-Method');
        $requestHeaders = $request->headers->get('Access-Control-Request-Method');
        if (!empty($requestMethod)) {
            $response->headers->add(['Access-Control-Allow-Methods' => $requestMethod]);
        }
        if (!empty($requestHeaders)) {
            $response->headers->add(['Access-Control-Allow-Headers' => $requestHeaders]);
        }
        if (!empty($cachingTimeInSecond)) {
            $response->headers->add(['Access-Control-Max-Age' => $cachingTimeInSecond]);
        }
    }

    public function proxify()
    {
        $this->loadProxyFromCloudFoundryServices();
        $this->loadProxyFromDatabase();
        $this->loadAdminUi();
        $this->registerApiRoute();
        $dispatcher = $this->router->getDispatcher();
        $request = Request::createFromGlobals();

        try {
            $resp = $dispatcher->dispatch('GET', $request->getPathInfo());
        } catch (NotFoundException $e) {
            if (!$this->config->get('admin-ui', false)) {
                throw $e;
            }
            $resp = $dispatcher->dispatch('GET', $this->ui->getBaseRoute());
            $resp->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $resp->send();

    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return RouteCollection
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @Required
     * @param RouteCollection $router
     */
    public function setRouter(RouteCollection $router)
    {
        $this->router = $router;
    }

    /**
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param Proxy $proxy
     */
    public function setProxy(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
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
    public function setUi(Ui $ui)
    {
        $this->ui = $ui;
    }

    /**
     * @param Config $config
     * @Required
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        $proxies = $this->config->get('proxyfy', array());
        $proxiesFinal = [];
        foreach ($proxies as $proxy) {
            $proxy['from'] = 'config';
            $proxiesFinal[] = $proxy;
        }
        $this->config->set('proxyfy', $proxiesFinal);
    }

    /**
     * @return DatabaseConnector
     */
    public function getDatabaseConnector()
    {
        return $this->databaseConnector;
    }

    /**
     * @param DatabaseConnector $databaseConnector
     * @Required
     */
    public function setDatabaseConnector($databaseConnector)
    {
        $this->databaseConnector = $databaseConnector;
    }

    /**
     * @return RoutesDao
     */
    public function getRoutesDao()
    {
        return $this->routesDao;
    }

    /**
     * @param RoutesDao $routesDao
     * @Required
     */
    public function setRoutesDao(RoutesDao $routesDao)
    {
        $this->routesDao = $routesDao;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Cache $cache
     * @Required
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     * @Required
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }


}