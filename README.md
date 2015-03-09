rest-proxifier
==============

The goal of this project is to proxy your rest api.

Why do this?
-----------
First, it was initialized when I found that some rest api doesn't handle [CORS](http://en.wikipedia.org/wiki/Cross-origin_resource_sharing).
This project will reinject CORS headers for angularjs project for example.
This project will also cache response for read request.

**NOTE:** Rest-proxifier use `pathinfo` so set your web server to use it.
 **NOTE2:** This project is [Cloud Foundry](http://www.cloudfoundry.org/about/index.html) Ready, so you can also use it inside Cloud Foundry to handle uaa rest api for example.
Installation
=======

Through Composer, obviously:

```shell
$ composer create-project arthurh/rest-proxifier path/
```

How to use
==========
You can use rest-proxifier in multiple ways:
 - Proxy from cnnfig file
 - Proxy from CloudFoundry Services if you use CloudFoundry
 - Proxy from database entries
Theses 3 ways can be used in same time, there is no limit.

For simple use, look at `config/default.yml`:
```yaml
proxyfy:
  - api: http://example.com
    route: /api
    #this is optionnal
    #request-header:
    #  header-key: header-value
    #response-header:
    #  header-key: header-value
    #response-content: ~
admin-ui: true #set to false to remove admin interface
admin-ui-root: /admin #path to admin ui
caching-time: 20 minutes #set to false to remove caching
database: false #or uri to database, e.g: mysql://root:password@localhost/mydb or special uri for sqlite: sqlite:/path, this is optionnal
```
This file can be rewrite in `json` or in `xml` too, it uses this dependency [noodlehaus/config](https://github.com/noodlehaus/config) so follow schemas from this dependency if you don't want use yaml.

For this default config there is an example, a route is already set.
What you should do to try is to go to `http://<my hostname>/index.php/api` and rest-proxifier will respond the page `http://example.com` with CORS headers.
If you do `http://<my hostname>/index.php/api/other/verb/for/api` rest-proxifier will respond `http://example.com/other/verb/for/api`.

Admin interface
---------------
By default you have an interface located on `http://<my hostname>/index.php/admin`.
You can use it to add more proxy only on a database.
Proxies from config file can't be modify with this interface with the goal make accessible all the time theses proxies.

For Cloud Foundry user
---------------------
This part is only for people who use the Cloud Foundry PaaS.

You can create a service which contain `proxy` (use regex to find service) in his name with this json value for example:
```json
{
    [
        {
          "api": "http://example.com",
          "route": "/api"
        }
    ]
}
```
And rest-proxifier will auto-bind to this service

You can also create a database service which should contains at least one of theses values in its name (use regex to find service):
  - `my` (for mysql)
  - `db`
  - `database`
  - `oracle`
  - `oci`
  - `postgres`
  - `pgsql`
  - `maria`
And rest-proxifier will auto-bind database to this service