<?php

  $tagAbout   = 'Renders a tree of rows: its body for every row, and {pad:recurse} renders it again for the row\'s children.';
  $tagGroup   = 'loops';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:tree 'rows'} ... {pad:recurse/} ... {/pad:tree}
{pad:tree data='rows', children='items'} ... {pad:branch} ... {pad:recurse/} ... {/pad:branch} ... {/pad:tree}
{pad:tree 'rows'} ... @else@ ... {/pad:tree}
PAD;

  $tagParms   = [
    'rows' => 'The name of the rows: a <code>{format:auto}</code> block, a stored sequence, an array of the page or of an enclosing row, or a <code>_data/</code> file. <code>data=</code> names them as well.' ];

  $tagOptions = [
    'children' => 'The field of a row that holds its children, at every depth. Default <code>children</code>.' ];

  $tagSee     = [ 'branch', 'recurse', 'format:auto' ];

?>
