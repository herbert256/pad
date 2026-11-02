<?php

  $tagAbout   = 'A named region of a layout or wrapper - and a page\'s override of it.';
  $tagGroup   = 'layout';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:block 'name'} default content {/pad:block}
{pad:block 'name'} {pad:parent/} more content {/pad:block}
PAD;

  $tagParms   = [
    'name' => 'The block\'s name, always quoted. A <code>{pad:block}</code> without a name is not a layout block.' ];

  $tagOptions = [];

  $tagSee     = [ 'extends', 'parent', 'slot', 'meta' ];

?>
