<?php

  $tagAbout   = 'Slides in a row that scrolls and snaps sideways, with arrow links and dots - no JavaScript needed.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{carousel} {tab 'caption'} ... {/tab} {tab} ... {/tab} {/carousel}
{carousel label='Name'} ... {/carousel}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'label' => 'The carousel\'s name for a screen reader, <code>Carousel</code> when not given.' ];

  $tagSee     = [ 'tab', 'tabs', 'accordion', 'img' ];

?>
