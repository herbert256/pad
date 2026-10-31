<?php

  // The views are the files of pad/maps/types/, as the engine reads them (padMapViews).

  include_once PAD . 'maps/_lib/map.php';

  $tagAbout   = 'Draws a choropleth map as inline SVG - every country coloured by a value of its row - of the world or one of its ' . ( count ( padMapViews () ) - 1 ) . ' regions.';
  $tagGroup   = 'graphics';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{map 'world', data='name', key='field', value='field', scale='quantile', title='text', width=720, height=400/}
{map 'europe', key='field', value='field'} ... {/map}
PAD;

  $tagParms   = [
    'view' => 'The part of the world drawn, a file of <code>pad/maps/types/</code>: '
                . implode ( ', ', array_map ( fn ( $view ) => "<code>$view</code>", padMapViews () ) )
                . ' - <code>world</code> unless given.' ];

  $tagOptions = [
    'data'   => 'The rows: a <code>{data}</code> store, a page array or a <code>_data</code> file, as for <code>{chart}</code>. As a pair the content is the rows - JSON, YAML, XML or CSV.',
    'key'    => 'The field with the country: its ISO 3166 alpha-2 or alpha-3 code or its English name, in any case - else the first field that is no number.',
    'value'  => 'The field with the number - else the first numeric field.',
    'scale'  => '<code>linear</code> (default: seven equal steps from the lowest value to the highest) or <code>quantile</code> (seven steps by rank).',
    'width'  => 'The width in pixels, default 720.',
    'height' => 'The height in pixels; by default it follows from the view.',
    'title'  => 'The accessible name, default <code>&lt;Value&gt; by country</code>.' ];

  $tagSee     = [ 'chart', 'country', 'data' ];

?>
