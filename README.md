jotform-api-php 
===============
[JotForm API](http://api.jotform.com/docs/) - PHP Client


### Installation

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

### Documentation

You can find the docs for the API of this client at [http://api.jotform.com/docs/](http://api.jotform.com/docs)

### Authentication

JotForm API requires API key for all user related calls. You can create your API Keys at  [API section](http://www.jotform.com/myaccount/api) of My Account page.

### Examples

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
Get submissions of the latest form
    
```php
<?php
    
    try {
        include "jotform-api-php/JotForm.php";
        
        $jotformAPI = new JotForm("YOUR API KEY");

        $forms = $jotformAPI->getForms(0, 1, null, null);

        $latestForm = $forms[0];

        $latestFormID = $latestForm["id"];

        $submissions = $jotformAPI->getFormSubmissions($latestFormID);

        var_dump($submissions);

    }
    catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
?>
```

Get latest 100 submissions ordered by creation date
    
```php
<?php
    
    try {
        include "jotform-api-php/JotForm.php";
        
        $jotformAPI = new JotForm("YOUR API KEY");

        $submissions = $jotformAPI->getSubmissions(0, 100, null, "created_at");

        var_dump($submissions);
    }
    catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
?>
```   

Submission and form filter examples
    
```php
<?php

    try {
        include "jotform-api-php/JotForm.php";
        
        $jotformAPI = new JotForm("YOUR API KEY");
        
        $filter = array(
                "id:gt" => "239252191641336722",
                "created_at:gt" => "2013-07-09 07:48:34",
        );
        
        $subs = $jotformAPI->getSubmissions(0, 0, $filter, "");
        var_dump($subs); 
        
        $filter = array(
                "id:gt" => "239176717911737253",
        );
        
        $formSubs = $jotformAPI->getForms(0, 0, 2, $filter);
        var_dump($formSubs);
    } catch (Exception $e) {
            var_dump($e->getMessage());
    }
    
?>
```    

Delete last 50 submissions

```php
<?php
    
    try {
        include "jotform-api-php/JotForm.php";
        
        $jotformAPI = new JotForm("YOUR API KEY");

        $submissions = $jotformAPI->getSubmissions(0, 50, null, null);

        foreach ($submissions as $submission) {
            $result = $jotformAPI->deleteSubmission($submission["id"]);
            print $result;
        }
    }
    catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
?>
```    
    
First the _JotForm_ class is included from the _jotform-api-php/JotForm.php_ file. This class provides access to JotForm's API. You have to create an API client instance with your API key. 
In case of an exception (wrong authentication etc.), you can catch it or let it fail with a fatal error.
