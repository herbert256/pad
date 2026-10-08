<?php

  $tagAbout   = 'Ends the request at once, with an empty body - the PAD way to stop instead of PHP\'s exit.';
  $tagGroup   = 'debug';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{exit}
PAD;

  $tagParms   = [];

  $tagOptions = [];

  $tagSee     = [ 'error', 'dump', 'redirect', 'restart' ];

?>
