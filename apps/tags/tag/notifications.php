<?php

  $tagAbout   = 'Lists the notifications padNotify kept on the site for the user logged in, unread, each with a link that marks it read.';
  $tagGroup   = 'access';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{widget:notifications/}
{widget:notifications for=$id, all} ... {$title} {$text} {$created} {$link} ... {/widget:notifications}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'for' => 'The recipient - an id or an e-mail address - instead of the user logged in.',
    'all' => 'The read notifications too.' ];

  $tagSee     = [ 'auth', 'mail', 'flash' ];

  $subsystemKindLinks = appSubsystemLinks ( 'channel' );

?>
