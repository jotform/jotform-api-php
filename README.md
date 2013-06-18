jotform-api-php 
===============
JotForm API - PHP Client


## Installation

Install via git clone:

        $ git clone git://github.com/jotform/jotform-api-php.git
        $ cd jotform-api-php
        
or

Install via Composer package manager (http://getcomposer.org/)
        
_composer.json_
```json
    {
        "require": {
            "jotform/jotform-api-php": "dev-master"
        }
    }
```

        $ php composer.phar install

## Documentation

You can find the docs for the API of this client at [http://api.jotform.com/docs/](http://api.jotform.com/docs)

## Authentication

JotForm API requires API key for all user related calls. You can create your API Keys at  [API section](http://www.jotform.com/myaccount/api) of My Account page.

## Examples

Print all forms of the user
    
```php
<?php
    
    include "jotform-api-php/JotForm.php";
    
    $jotformAPI = new JotForm("YOUR API KEY");
    $forms = $jotformAPI->getForms();
    
    foreach ($forms as $form) {
        print $form["title"];
    }

?>
```    

Get latest submissions of the user
    
```php
<?php
    
    try {
        include "jotform-api-php/JotForm.php";
        
        $jotformAPI = new JotForm("YOUR API KEY");
        $latestSubmissions = $jotformAPI->getSubmissions();
    
        foreach ($latestSubmissions as $submission) {
            echo sprintf("%s - %s \n", $submission["created_at"], implode(" ", $submission["fields"]));
        }
    }
    catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
?>
```    

    
First the _JotForm_ class is included from the _jotform-api-php/JotForm.php_ file. This class provides access to JotForm's API. You have to create an API client instance with your API key. 
In any case of exception (wrong authentication etc.), you can catch it or let it fail with fatal error.
