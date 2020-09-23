# Daktela API Library
Library for comunication with Daktela API.


Installation and requirements
-----------------------------

The recommended way to is via Composer:

```shell
composer require filipekp/daktela
```

Usage
-----

Activating Daktela Library is easy. Simply add these two lines of code, preferably just after library loading (like `require_once 'vendor/autoload.php'`) and before any output is sent to browser:

```php
  use Tracy\Debugger;
  use filipekp\daktela\DaktelaConnector;
  use filipekp\daktela\models\DefaultModel;
  use filipekp\daktela\models\Contacts;
  use filipekp\daktela\entities\Contact;

  require_once '/vendor/autoload.php';

  Debugger::$maxDepth    = 7;
  Debugger::$maxLength   = 4096;
  Debugger::enable(Debugger::DEVELOPMENT, '/logs/daktela');

  $conn = DaktelaConnector::init('myserver.daktela.com', 'access_token');

  // if haven't access_token use next rows
  $conn = DaktelaConnector::init('myserver.daktela.com');
  // login & password is from some user in daktela system
  $conn->getToken('login', 'password');

  DefaultModel::setConnector($conn);
  
  $contactsModel = new Contacts();
  $customer = new Contact($contactsModel->read($testUserID));
  dump($customer);
  exit();
```
