<?php

namespace Laraform\Traits;

use Illuminate\Http\Request;
use Laraform\Process\AutoProcess;

trait UpdatesForm
{
  public function update(Request $request) {
    return (new AutoProcess())->process($request, $this->form());
  }
}