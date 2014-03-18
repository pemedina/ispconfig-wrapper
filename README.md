# ISPConfig 3 remoting API Wrapper

## Introduction

A simple wrapper for ispconfig3 remote API.

Designed to interoperate with ISPConfig 3, it aims to provide an expresive yet simple interface to perform all actions provided by the API.

## Requirements

* PHP >= 5.0.0 (with [soap](http://se2.php.net/soap) support)

## Getting started

The library acts as a proxy between ISPConfig 3 SOAP server and your app. All functions are renamed to a more expressive (IMHO) camelCase syntax. IT *doesn't* do any validation, just proxies every request to the related SOAP call.
The *only* change is that every response is returned as a json encoded array.

 +  Exceptions are trapped and converted to json, wrapped as `errors`.
 +  Single value responses are converted to json , wrapped as `result`.
 + Array responses are converted to json.

## Composer

```bash
$ composer require pemedina/ispconfig-wrapper 1.*
```

## Usage

The wrapper can be included & used on any PHP application.

## Examples

### Expressive syntax.

``` php
<?php
$webService = new ISPConfigWS(
    new \SoapClient(NULL,
        array('location'   => 'http://192.168.0.55/remote/index.php',
              'uri'        => 'http://192.168.0.55/remote/',
              'exceptions' => 0)
    )
);

// Login
$webService
    ->with(array('loginUser' => 'admin', 'loginPass' => 'password'))
    ->login();


$result = $webService
            ->with(array('client_id' => 5))
            ->getClient()
            ->response();

print_r json_decode( $result ));

// Single call

$result = $webService
            ->with(array('loginUser' => 'admin', 'loginPass' => 'password', 'password' => 'newPass', 'client_id' => 5))
            ->changeClientPassword()
            ->response();

print_r json_decode( $result ));
```

### Standard Usage.

``` php

<?php
$webService = new ISPConfigWS(
    new \SoapClient(NULL,
        array('location'   => 'http://192.168.0.55/remote/index.php',
              'uri'        => 'http://192.168.0.55/remote/',
              'exceptions' => 0)
    )
);

$loginDetail = array('loginUser' => 'admin', 'loginPass' => 'password');
$webService->setParameters( $loginDetails );
$webService->login();
...
...
$parameters = array('client_id' => 5);
$webService->setParameters( $parameters );
$webService->getClient();

print_r json_decode( $webService->getResponse() ));
```

## Feedback and questions

Found a bug or missing a feature? Don't hesitate to create a new issue here on GitHub.




