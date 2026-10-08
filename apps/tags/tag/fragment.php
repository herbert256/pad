<?php

  $tagAbout   = 'A named part of the page that a request can ask for alone - for htmx and ajax swaps.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{fragment 'name'} ... {/fragment}
PAD;

  $tagParms   = [
    'name' => 'The fragment\'s name, required. A request with <code>&amp;padFragment=name</code> gets this part alone.' ];

  $tagOptions = [];

  $tagSee     = [ 'ajax', 'live', 'page' ];

?>
