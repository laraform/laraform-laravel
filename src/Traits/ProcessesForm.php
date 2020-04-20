<?php

namespace Laraform\Traits;

use Illuminate\Http\Request;
use Laraform\Process\AutoProcess;

trait ProcessesForm
{
  public function process(Request $request) {
    $form = app(config('laraform.path') . '\\' . decrypt($request->key));

    return (new AutoProcess())->process($request, $form);
  }
}