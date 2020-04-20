<?php

namespace Laraform\Traits;

use Illuminate\Http\Request;
use Laraform\Process\AutoProcess;

trait ProcessesForm
{
  public function process(Request $request) {
    return (new AutoProcess())->process($request);
  }
}