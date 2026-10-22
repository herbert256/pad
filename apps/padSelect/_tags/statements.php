<?php

  // The SQL cell: the statements the example sent, each shape once with how often it ran.
  // It stands after @page@ in the wrapper, so the page has run every select by then.

  $psList = psStatements ( $GLOBALS ['psSql'] ?? [] );
  $psRuns = array_sum ( array_column ( $psList, 'times' ) );
  $psHtml = '';

  foreach ( $psList as [ 'sql' => $psOne, 'times' => $psTimes ] )
    $psHtml .= '<li><code>' . padHighlightTokens ( $psOne, 'sql' ) . '</code>'
             . ( $psTimes > 1 ? "<span class=\"ps-times\">&times; $psTimes</span>" : '' ) . '</li>';

  $psHead = ( $psRuns == 1 ) ? '1 statement' : "$psRuns statements";

  if ( count ( $psList ) < $psRuns )
    $psHead .= ' in ' . count ( $psList ) . ' shapes - each shown by its first, and how many were sent: the same statement for another value';

  return "<p class=\"ps-count\">$psHead</p><ol class=\"ps-sql\">$psHtml</ol>";

?>
