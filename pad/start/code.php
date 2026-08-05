<?php

  // Body of the {code} and {sandbox} tags: takes the tag's own content as PAD source and
  // runs it through a nested engine pass, returning whatever that pass produced.
  //
  // $padStrBld = 'code' tells start/pad/pad.php to dispatch the nested run to
  // start/pad/code.php, and $padStrCod carries the source. start/pad/parms.php then reads
  // the sandbox, reset, clean and function options off the tag to decide how much of the
  // engine state has to be saved and cleared around the run.

  $padStrBld  = 'code';
  $padStrCod  = $padBase [$pad];

  return include PAD . 'start/pad/parms.php';

?>