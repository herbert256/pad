<?php

  $tagAbout   = 'Items that open and close on a click, each a details element - no JavaScript.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{accordion} {tab 'label'} ... {/tab} {tab 'label'} ... {/tab} {/accordion}
{accordion single, open=1} ... {/accordion}
{accordion open} ... {/accordion}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'single' => 'Bare option: one item open at a time - the items share a <code>name</code>, the exclusive accordion of HTML.',
    'open'   => 'The item open at first: its number from 1 or its label. Bare, <code>open</code> opens every item.' ];

  $tagSee     = [ 'tab', 'tabs', 'carousel', 'modal' ];

?>
