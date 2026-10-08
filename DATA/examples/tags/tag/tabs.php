<?php

  $tagAbout   = 'Panels behind a row of tabs, switched by radio buttons and CSS - no JavaScript.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{tabs} {tab 'label'} ... {/tab} {tab 'label'} ... {/tab} {/tabs}
{tabs active='label'} ... {/tabs}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'active' => 'The tab shown first: its number counted from 1, or its label. The first tab when not given.' ];

  $tagSee     = [ 'tab', 'accordion', 'carousel', 'modal' ];

?>
