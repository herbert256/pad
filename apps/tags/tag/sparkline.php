<?php

  $tagAbout   = 'Draws a word-sized line chart as inline SVG - no axes, the last point marked - to stand in a sentence or a table cell.';
  $tagGroup   = 'graphics';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{sparkline data='name', label='field', value='field', title='text', width=120, height=32}
{sparkline sequence='fibonacci', rows=12}
{sparkline type='json'} ... {/sparkline}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'data'     => 'The rows: a <code>{data}</code> store, a sequence store, an array of the page (or a field of the current row holding a list) or a <code>_data</code> file of that name, or a literal (<code>data=\'[3,1,4]\'</code>). Left out on a pair, the content is the data.',
    'type'     => 'For a pair: the format of the content - <code>json</code>, <code>yaml</code>, <code>xml</code> or <code>csv</code> - when it is not to be recognised on sight.',
    'sequence' => 'Plot the first <code>rows</code> terms (default 10) of a sequence type.',
    'value'    => 'The field with the number - else the first numeric field.',
    'label'    => 'The field naming each point, used in the description read by a screen reader.',
    'title'    => 'The accessible name, the SVG\'s one <code>&lt;title&gt;</code>.',
    'width'    => 'The width in pixels, default 120.',
    'height'   => 'The height in pixels, default 32.' ];

  $tagSee     = [ 'chart', 'sequence', 'data' ];

?>
