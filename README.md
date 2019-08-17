# Laraform Community Edition (Laravel)

This repository contains the Laravel package of Laraform's Community Edition.

Check out [laraform/laraform](https://github.com/laraform/laraform) repo or [Documentation](https://laraform.io/docs) for more details.

## Installation


``` bash
composer require laraform/laraform-laravel
```

## Usage

``` php
// app/Forms/MyFirstForm.php

<?php

namespace App\Forms;

use Laraform;

class MyFirstForm extends Laraform
{
  public $schema = [
    'hello_world' => [
      'type' => 'text',
      'label' => 'Hello',
      'default' => 'World'
    ]
  ];
}
```

Pass the form to the view:
``` php
// routes/web.php

Route::get('/', function () {
  return view('welcome', [
    'form' => app('App\Forms\MyFirstForm')
  ]);
});
```

Render:
``` html
<!-- resources/views/welcome.blade.php --->

<html>
  <head>
    <!-- ... --->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="/css/app.css">
  </head>
  <body>
    <div id="app">
      {!! $form->render() !!}
    </div>

    <script src="/js/app.js"></script>
  </body>
</html>
```

Please note that you need the [Larafrom Vue package](https://github.com/laraform/laraform) in order to make this work.