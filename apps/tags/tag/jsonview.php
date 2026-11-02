<?php

  $tagAbout   = 'A JSON value or a PHP array as a tree that folds open and shut, coloured by type.';
  $tagGroup   = 'widgets';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:jsonview $value/}
{pad:jsonview $value, open=1, title='Label'/}
{pad:jsonview data='name'/}
{pad:jsonview} {"id": 42, "paid": true} {/pad:jsonview}
PAD;

  $tagParms   = [
    'value' => 'The value to show: a field holding an array, or a text - a text that is a JSON object or list is decoded.' ];

  $tagOptions = [
    'data'  => 'Names the value instead: a <code>{pad:data}</code> store, a page array or a <code>_data</code> file.',
    'open'  => 'The number of levels shown unfolded, 2 when not given; 0 folds everything.',
    'title' => 'A label for the outermost level.' ];

  $tagSee     = [ 'debug', 'dump', 'data', 'table:standard' ];

?>
