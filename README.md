Folders Structure:
==================

- config: 
     Drop here your config files, credentials should live in files inside this folder

- constants: 
     Files defining constants across the system, usually with one file should be enough, however can create several files

- controllers: 
     The Fwk follows and MVC pattern, classes acting as controllers should live inside this folder. 
     This folder can also have internal subfolders to provide some site structure.
      Finally controllers are classes that should inherit from the fwk controller class and define public methods which are actions to be called

- dataaccess:
      Files defining classes to access data (Not necesarilly a DB, could be caching repositories, external services, etc)

- fwk:
      Core files from the fwk, if need to change something in here, be sure that unit tests still pass and be extra carefull as they impact the whole platform

- css:
      Stylesheet files (css) should live inside this folder

- js:
      Drop in here javascript files

- images:
      Images used by the site should live inside this folder

- lib:
     This folder holds libraries used by the system, either custom built or vendor ones

- locales:
     Files defining localization resources as translations (Using gettext)

- models:
     Models which define entities of the system, most of the cases should map a data entity, should follow active record pattern in most of the cases

- templates:
      View files. A page in the fwk has been thought as a conjunction as many modules, files in this folder are php files which holds html with php markup to display data gotten from controllers or to include submodules.
      For the sake of organization, this folder allows to create subfolders

- factories:
      Factories classes which allows to create models or library objects bases on different set of parameters

- helpers:
      Classes implementing helper utilities should go inside this folder, an example would be a class with a method to calculate remaining time to a certain date (Date would not be a model for the site, but we may need certain functionalities for display matters).

Special File
============
main.php: Lives on the root of the site, every request is passed to this file which then makes validations, initialize the fwk and route the request to the propper controller.
 
Urls and Routing
================
By default there is a matching between urls and which is the controller which will respond to the request in the following form:

www.gopr.it/user/register => This would route to the method register on the controller named User inside controllers folder

www.gopr.it/user/ => When the last char of the url is a /, then the action to be executed will be the one named index, so this url will execute the method index on the controller user located on controllers folder

www.gopr.it/journalists/search/partial => As the structure of urls works, the last part of the url will be the action to be called, the previous one will be the name of the controller which holds the action, and all the previous ones matches a folder structure inside controllers. In this case it will execute the mehod partial for the search controller located inside /controllers/journalists

Custom routing: In case you need a specific url which doesn't match the default url structure for the framework, custom routing rules can be defined inside /config/urlmanagerconf.php file, there you define an array entry with the following fields:
    - pattern: Regular expression matching your url
    - handler file: path to the controller which should manage the url
    - hanlderClass: Name of the controller class
    - hanlderMethod: Name of the method which manages the url
    - extraParams: (Optional) parámeters to be passed to the method, could be either hardcoded or comming from subpatterns of the url, using $n references to the subpatterns on the pattern defined for the url.

Autoloading
===========
The fwk works using namespaces and an autoloading function
Given the structure followed by the fwk the Autoloading function consider files inside a folder as in the same namespace, so the autoloader function will search for the class requested on a folder of the same name as the namespace and will include the file with the same name as the class.

As an example a class named

Controllers\Journalists\Twitter => Will try to open the file twitter.php inside the folder /controllers/journalists and this file should contain a class named twitter

Important to note that is case sensitive, folders and files should be named in lowercase

Special cases can be defined on the autoloader class, but there is no config file. (Check _checkSpecialCases method)

The response object
===================
The fwk receives a request and always renders a response object as a result of the request
Controllers will have for default an attribute holding a response object, during the execution of the controller, it should set the different atributes of the response object which will be rendered at the end as the response of the object.

The Response object provides a set of attributes for setting important attributes:
- setResponseCode: Http response code for the request (200 OK)
- setHeaders: Different headers as ContentType (But not limited to)
- setBody: Could be a json, html, etc. In most of the cases will be the result of rendering a view
- setResponseCookie: to set a cookie

Rendering a view
================
As an MVC fwk controllers will gain control of the execution, so to render a view, from the controller you can instantiate a view class from there.
From the controller you will be able to set different attributes to the view with any name (Ex: $view->myvalue = 1), this attributes are then available at rendering view time.
Finally calling the method render() for the view will return a string with the results of rendering the view with the values set on the controller.
Finally set this result string as the body for the response object.

Frontend Special Classes
========================
The fwk includes a few classes as utilities to be used when rendering views:
JSEnqueuer: Allows to include javascript files or code snippets on the views that are needed but ends up rendering just before the body closing tag (See examples included on the initial import)

StaticFilesRenderer: Defines a default controller which allows rendering of views without the need of definining a controller (Usefull for static pages).

Decorators and Validators: The fwk includes a set of js classes to validate and decorate text inputs client side (see views examples included for usage), some of them are actually not needed as html5 includes some of this fields as email.

PHP Validators
==============
The fwk includes a set of classes to be used as validators for different conditions, check the example model included for the usage.
The models defines a method _setValidatonRules which allows to set validation rules for the different attributes.

Apache setup
=============
For this framework to work all urls but static files should be pointed to the main.php file, so you need to have mod_rewrite module installed and setup some rewrite rules.
An example of setup can be found below:

"<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName <Your site url>
        DocumentRoot <Path to where the site is located>

        <Directory "<Path to where the site is located>">
                Options FollowSymLinks
                AllowOverride all
                RewriteEngine on
                Require all granted

                RewriteCond %{REQUEST_URI} !.*\.(ico|png|gif|jpg|css|js|ttf|svg|crx|eot|woff)$ [NC]
                RewriteRule ^.* main.php [QSA,L]
        </Directory>
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>"
