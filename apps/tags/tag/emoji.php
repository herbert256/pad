<?php

  $tagAbout   = 'An emoji by its GitHub or Slack shortcode, as a labelled image a screen reader names.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{emoji 'rocket'}
{emoji ':tada:', label='Party!'}
PAD;

  $tagParms   = [
    'shortcode' => 'The shortcode, with or without colons, in any case - <code>+1</code>, <code>heart</code>, <code>:white_check_mark:</code>.' ];

  $tagOptions = [
    'label' => 'The name a screen reader says, default the emoji\'s own name.' ];

  $tagSee     = [ 'icon', 'country', 'avatar' ];

?>
