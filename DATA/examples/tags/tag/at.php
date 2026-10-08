<?php

  $tagAbout   = 'Looks a value up in a data set by a path of names and conditions - the @ notation as a tag.';
  $tagGroup   = 'values';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{at 'field@store'}
{at "path.key='value'.field@store"}
PAD;

  $tagParms   = [
    'path' => 'An @ expression: before the <code>@</code> a dotted path - field names, and <code>key=\'value\'</code> steps that pick the row whose field has that value - and after it where to look: a <code>{data}</code> store, a page array, a <code>_data/</code> file or a level.' ];

  $tagOptions = [];

  $tagSee     = [ 'data', 'set', 'tree' ];

?>
