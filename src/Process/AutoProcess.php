<?php

namespace Laraform\Process;

use Illuminate\Http\Request;
use Laraform\Laraform;

class AutoProcess
{
  public function process(Request $request, Laraform $form = null) {
    if ($form === null) {
      $form = app(config('laraform.path') . '\\' . decrypt($request->key));
    }

    $form->setData($request->data);
    $form->setKeyFromData($request->data);

    if($result = $form->fire('before')) {
      return $result;
    }

    $form->validate();

    if ($form->isInvalid()) {
      return response([
        'status' => 'fail',
        'messages' => $form->getErrors(),
        'payload' => []
      ]);
    }

    if ($form->hasModel()) {
      $form->save();
    }

    if($result = $form->fire('after')) {
      return $result;
    }

    $updates = $form->getUpdates();

    return response([
      'status' => 'success',
      'messages' => [],
      'payload' => count($updates) > 0 ? [
        'updates' => $updates
      ] : []
    ]);
  }
}