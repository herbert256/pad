<?php

  $tagAbout   = 'A named region of a layout or wrapper - and a page\'s override of it.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{block 'name'} default content {/block}
{block 'name'} {parent} more content {/block}
PAD;

  $tagParms   = [
    'name' => 'The block\'s name, always quoted. A <code>{block}</code> without a name is not a layout block.' ];

  $tagOptions = [];

  $tagSee     = [ 'extends', 'parent', 'slot', 'meta' ];

?>
