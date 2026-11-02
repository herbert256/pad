<?php

  // The kinds are the files of pad/subs/charts/types/, as the engine reads them (padChartTypes).

  $tagAbout   = 'Draws a chart as inline SVG on the server - ' . count ( padChartTypes () ) . ' kinds, from bars and lines to sankeys, maps of a hierarchy and org charts.';
  $tagGroup   = 'graphics';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{chart:bar data='name', label='field', value='field, field', stacked, title='text', width=600, height=300/}
{chart:line sequence='fibonacci', rows=12/}
{chart:pie data='name', label='field', value='field'/}
{chart:scatter data='name', x='field', y='field', size='field', color='field', trend/}
{chart:gauge value=72, from=0, to=100, bands='60, 85', target=80, unit='%'/}
{chart:bar label='field', value='field', type='csv'} ... {/chart:bar}
PAD;

  $tagParms   = [
    'kind after colon' => 'The kind of chart, a file of <code>pad/subs/charts/types/</code>: '
                . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padChartTypes () ) )
                . '. The chart: prefix selects it - <code>{chart:pie data=\'budget\'/}</code>.' ];

  $tagOptions = [
    'data'     => 'The rows: a <code>{pad:data}</code> store, a sequence store, the page\'s array or a <code>_data</code> file of that name, or a literal (<code>data=\'[3,1,4]\'</code>). Left out on a pair, the content is the data.',
    'type'     => 'For a pair: the format of the content - <code>json</code>, <code>yaml</code>, <code>xml</code> or <code>csv</code> - when it is not to be recognised on sight.',
    'sequence' => 'Plot the first <code>rows</code> terms (default 10) of a sequence type instead of rows.',
    'value'    => 'The field with the number - else the first numeric field. A comma list draws a series each (eight at most) for bar, hbar, line, area, stream, radar, rose, marimekko, pyramid and parallel. For a gauge it can be the number itself. Likert: the fields of the answers in their order, two to eight, the most negative first.',
    'label'    => 'The field for the category axis or the names - else the first other field, else the row number.',
    'title'    => 'The accessible name in the SVG\'s <code>&lt;title&gt;</code>; by default made from value and label.',
    'width'    => 'The width in pixels, default 600 (round kinds 480, gauge 320, calendar 720).',
    'height'   => 'The height in pixels, default 300 (round kinds 280, the tall kinds 400, gauge 200, calendar 150).',
    'stacked'  => 'Bare option, bar, hbar and area: the series one on the other instead of side by side.',
    'percent'  => 'Bare option, area: every column stacked to 100%.',
    'x'        => 'Scatter, bubble, hexbin, heatmap: the field of the horizontal axis (heatmap: the columns).',
    'y'        => 'Scatter, bubble, hexbin, heatmap: the field of the vertical axis (heatmap: the rows).',
    'size'     => 'Bubble: the field that gives each dot its area.',
    'color'    => 'Scatter, bubble, gantt, parallel: a field whose values colour the marks, with a legend.',
    'trend'    => 'Bare option, scatter and bubble: adds the least-squares line.',
    'source'   => 'Sankey, network, chord, arc: the field of where a link starts.',
    'target'   => 'Sankey, network, chord, arc: the field of where a link goes. Gauge and bullet: the value (or field) marked as the goal.',
    'layout'   => 'Network: <code>force</code> (default) or <code>radial</code>.',
    'arrows'   => 'Bare option, network: draws the direction of the links.',
    'levels'   => 'Treemap, sunburst, pack: the fields above the parts, outermost first.',
    'from'     => 'Gauge: the start of the scale (0). Gantt: the field with the start of a task. Dumbbell: the field of the first end, the first colour.',
    'to'       => 'Gauge: the end of the scale (100). Gantt: the field with the end of a task. Dumbbell: the field of the second end, the second colour. Radar, radialbar, rings, bullet: the end of the scale, a number (or for rings and bullet a field).',
    'names'    => 'Dumbbell: the names of the two ends in the legend (<code>\'2025, 2026\'</code>), else the fields\' own.',
    'ranked'   => 'Bare option, dumbbell: the rows in the order of their change, the largest rise first. Likert: the questions in the order of their share of positive answers, the largest first.',
    'bands'    => 'Gauge and bullet: thresholds that split the track into coloured or grey steps (<code>\'60, 85\'</code>).',
    'unit'     => 'Gauge: written after the number.',
    'total'    => 'Waterfall: the name of the closing bar (<code>Total</code>); <code>total=\'\'</code> leaves it out.',
    'bins'     => 'Histogram: about how many bins (10). Hexbin: the hexagons across the plot (20; 2 to 100).',
    'date'     => 'Calendar: the field with the day.',
    'year'     => 'Calendar: draw the whole year instead of the span of the data.',
    'progress' => 'Gantt: the field with the part done, 0 to 100.',
    'mark'     => 'Gantt: a date marked with a dashed line.',
    'by'       => 'Multiples: the field that splits the rows into small charts.',
    'columns'  => 'Multiples: the number of small charts side by side.',
    'open'     => 'Candlestick: the field of the opening price (also <code>high</code>, <code>low</code> and <code>close</code>, each defaulting to its own name).',
    'volume'   => 'Candlestick: a field of volumes drawn as light bars below.',
    'low'      => 'Errorbar: the field of the lower bound of a row\'s interval (<code>low</code>). Candlestick: the field of the lowest price (<code>low</code>).',
    'high'     => 'Errorbar: the field of the upper bound of a row\'s interval (<code>high</code>). Candlestick: the field of the highest price (<code>high</code>).',
    'interval' => 'Errorbar: what the whisker is - <code>\'95% CI\'</code>, <code>\'min-max\'</code> - in the legend, the title and the description; never made up, without it the legend names the bound fields.',
    'line'     => 'Dual: the field drawn as a line on the right axis, beside the bars of <code>value</code>.',
    'period'   => 'Spiral: rows to one turn (12).',
    'turn'     => 'Spiral: the field that names each turn.',
    'cells'    => 'Waffle: the number of squares (100).',
    'sets'     => 'Venn: the field naming a set (<code>A</code>) or an overlap (<code>A&amp;B</code>).',
    'bandwidth'=> 'Density, violin: the width of the kernel, else Silverman\'s rule. Ridgeline: one width for every ridge, else Silverman\'s rule over all the values.',
    'overlap'  => 'Ridgeline: the bands the tallest ridge rises above its baseline (1.5) - 1 just touches the baseline above.',
    'square'   => 'Bare option, proportional: squares instead of circles.',
    'id'       => 'Orgchart: the field with a row\'s id.',
    'parent'   => 'Orgchart: the field with the id of the row above.',
    'sub'      => 'Orgchart: a field for the second line of a box.' ];

  $tagSee     = [ 'chart:sparkline', 'map', 'timeline', 'diagram:auto', 'data', 'sequence:auto' ];

?>
