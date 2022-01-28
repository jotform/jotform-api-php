jotform-api-php 
===============
[Jotform API](http://api.jotform.com/docs/) - PHP Client


## Installation

1- Install via [Composer](http://getcomposer.org/)
        
```
$ composer require jotform/jotform-api-php 
```
and add following line to your php file:
```php 
require 'vendor/autoload.php';
```

2- Install and use manually:
```
$ git clone https://github.com/jotform/jotform-api-php.git
```
and add following line to your php file:
```php 
require 'jotform-api-php/Jotform.php';
```
If you install the package into another directory, you should update path for `require` command above.


## Documentation

You can find the docs for the API of this client at [Jotform API Documentation](http://api.jotform.com/docs).

## Authentication

Jotform API requires API key for all user related calls. You can create your API Keys at [API section](http://www.jotform.com/myaccount/api) of My Account page.

## Examples

1- Print all forms of the user:
```php
<?php
    
require 'vendor/autoload.php';

use Jotform\Jotform;
use Jotform\JotformClient;

$client = new JotformClient('<YOUR_API_KEY>');
$jotform = new Jotform($client);

$forms = $jotform->user()->forms();
foreach ($forms as $form) {
    echo $form['title'] . PHP_EOL;
}
``` 

2- Get submissions of the latest form: 
```php
<?php

require 'vendor/autoload.php';

use Jotform\Jotform;
use Jotform\JotformClient;

try {
    $client = new JotformClient('<YOUR_API_KEY>');
    $jotform = new Jotform($client);

    $forms = $jotform->user()->forms();
    $latestForm = $forms[0];
    $latestFormId = $latestForm['id'];

    $submissions = $jotform->form($latestFormId)->submissions();
    var_dump($submissions);
}
catch (Exception $e) {
    var_dump($e->getMessage());
}
```

3- Get latest 100 submissions ordered by creation date:
```php

require 'vendor/autoload.php';

use Jotform\Jotform;
use Jotform\JotformClient;

try {    
    $client = new JotformClient('<YOUR_API_KEY>');
    $jotform = new Jotform($client);

    $submissions = $jotform->user()->limit(100)->orderBy('created_at')->submissions();
    var_dump($submissions);
} catch (Exception $e) {
    var_dump($e->getMessage());
}
```   

4- Submission and form filter examples:
```php

require 'vendor/autoload.php';

use Jotform\Jotform;
use Jotform\JotformClient;

try {
    $client = new JotformClient('<YOUR_API_KEY>');
    $jotform = new Jotform($client);
    
    $filter = [
        'id:gt' => '239252191641336722',
        'created_at:gt' => '2013-07-09 07:48:34',
    ];

    $submissions = $jotform->user()->filter($filter)->submissions();
    var_dump($submissions); 
    
    $filter = [
        'id:gt' => '239176717911737253',
    ];
    
    $forms = $jotform->user()->filter($filter)->forms();
    var_dump($forms);
} catch (Exception $e) {
    var_dump($e->getMessage());
}
```    

5- Delete last 50 submissions:
```php

require 'vendor/autoload.php';

use Jotform\Jotform;
use Jotform\JotformClient;

try {
    $client = new JotformClient('<YOUR_API_KEY>');
    $jotform = new Jotform($client);

    $submissions = $jotform->user()->limit(50)->orderBy('created_at')->submissions();
    foreach ($submissions as $submission) {
        $result = $jotform->submission($submission['id'])->delete();
        echo $result . PHP_EOL;
    }
}
catch (Exception $e) {
    var_dump($e->getMessage());
}
```

## Notes
- Condition methods: `filter`, `limit`, `offset`, `orderBy`
- Query methods: `action`, `sortBy`, `date`, `startDate`, `endDate`
- Limitation: *(for now)*
    - **Condition methods** can be used with `Form` and `User` services.
    - **Query methods** can be used with `User` service.
---
Jotform
