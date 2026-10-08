<?php

  $tagAbout   = 'Rows as a complete HTML table - formatted numbers, a totals row, sorting and paging on the server.';
  $tagGroup   = 'widgets';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{datatable data='name', columns='a, b, c', labels='A, B, C', totals='c'}
{datatable data='name', format="c:currency('EUR')", sortable, striped, caption='Caption'}
{datatable data='name', rows=10, query='prefix_'}
{datatable totals='amount'} month,amount ... {/datatable}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'data'     => 'The rows: a <code>{data}</code> store, a page array or a <code>_data</code> file. As a pair the content is the rows instead - JSON, YAML, XML or CSV, told apart on sight.',
    'columns'  => 'The fields shown, in their order. Every field of the first row when not given.',
    'labels'   => 'The header of each column, in the order of <code>columns</code>. The field name as a headline when not given (<code>order_date</code> is Order Date).',
    'format'   => 'A pipe per column, <code>column:pipe</code> separated by commas - <code>total:money, date:date(\'j M\')</code>.',
    'totals'   => 'Columns summed in a totals row - over every row, not only the page shown.',
    'sortable' => 'Bare option: every header is a link that sorts on the server, <code>?sort=total&amp;dir=desc</code>.',
    'rows'     => 'Rows per page, with page links below (<code>?page=2</code>).',
    'query'    => 'A prefix for the request names <code>sort</code>, <code>dir</code> and <code>page</code> - for two tables on one page.',
    'striped'  => 'Bare option: every other row shaded.',
    'caption'  => 'The table\'s <code>&lt;caption&gt;</code>.' ];

  $tagSee     = [ 'pager', 'chart', 'data' ];

?>
