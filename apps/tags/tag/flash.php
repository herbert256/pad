<?php

  $tagAbout   = 'The flash messages of this request - set with padFlash on the page before - one occurrence each.';
  $tagGroup   = 'forms';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{flash} {$message} {$type} {/flash}
{flash} ... @else@ ... {/flash}
{flash 'error'} {$message} {/flash}
PAD;

  $tagParms   = [
    'type' => 'Optional: only the messages of this type - <code>error</code>, <code>info</code> (padFlash\'s default) or any word the application uses.' ];

  $tagOptions = [];

  $tagSee     = [ 'form', 'redirect' ];

?>
