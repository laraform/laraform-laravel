<?php

namespace Laraform\Traits;

use Illuminate\Http\Request;
use Laraform\Process\AutoProcess;

trait StoresForm
{
  public function store(Request $request) {
    return (new AutoProcess())->process($request, $this->form());
  }
}