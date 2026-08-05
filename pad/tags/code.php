<?php

  // {code}...{/code} runs its own content as a separate PAD pass, and returns what that
  // pass produced. Clearing $padContent keeps the source itself out of the output;
  // start/code.php does the run, reading the sandbox, reset, clean and function options
  // off the tag.

  $padContent = '';

  return include PAD . 'start/code.php';

?>