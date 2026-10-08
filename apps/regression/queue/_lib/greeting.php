<?php

  // A function of the application's _lib, which a job handler calls - the proof that a job
  // runs in the application's context.

  function queueGreeting ( $name ) {

    return "hello $name";

  }

?>
