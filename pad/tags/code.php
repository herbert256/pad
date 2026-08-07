<?php

  // {code}...{/code} runs its own content as a separate PAD pass, and returns what that
  // pass produced. Clearing $padContent keeps the source itself out of the output;
  // start/code.php does the run, reading the sandbox, reset, clean and function options
  // off the tag.
  //
  // The clearing has to come after the run, not before it. $padContent is the level's own
  // working content and the nested pass writes it too, so a clear made first was overwritten
  // by the time level/go.php merged the result in - and the source rendered a second time
  // behind the answer, {code}{echo 'in'}{/code} giving 'inin'.

  $padCodeResult = include PAD . 'start/code.php';

  $padContent = '';

  return $padCodeResult;

?>