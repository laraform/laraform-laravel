<?php

namespace Laraform\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laraform\Process\AutoProcess;

class FormController extends Controller
{
    public function process(Request $request)
    {
        return (new AutoProcess())->process($request);
    }
}