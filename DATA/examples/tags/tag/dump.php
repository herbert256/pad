<?php

  $tagAbout   = 'Stops the request and shows the debug dump of the engine\'s state at that point.';
  $tagGroup   = 'debug';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pad:dump/}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'debug', 'trace', 'error', 'exit' ];

?>
