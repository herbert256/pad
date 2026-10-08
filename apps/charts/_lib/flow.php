<?php

  // The tag cell: a {chart} or {sparkline} tag the file writes over several lines - an
  // option a line - is shown with its options flowing, side by side as far as the cell is
  // wide, a wrapped line hanging under the first option (.flow in charts.css). Each option
  // is coloured on its own, in a span a line breaks inside only when the option is wider
  // than the whole line; the text around the tags is coloured as it stands.

  function chartsFlow ( $source ) {

    $html = '';
    $at   = 0;

    preg_match_all ( '/^([ \t]*)\{(chart|sparkline)\b((?:\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"|[^\'"{}])*)\}/m',
                     $source, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

    foreach ( $tags as $tag ) {

      list ( $whole, $offset ) = $tag [0];

      if ( ! str_contains ( $whole, "\n" ) )
        continue;

      $lead  = $tag [1] [0];
      $name  = $tag [2] [0];
      $parts = preg_split ( '/,[ \t]*\n\s*/', trim ( $tag [3] [0] ) );
      $last  = count ( $parts ) - 1;
      $spans = [];

      foreach ( $parts as $i => $part )
        $spans [] = '<span>'
                  . padHighlightTokens ( ( $i ? '' : "$lead{" . "$name " ) . trim ( $part ) . ( $i == $last ? '}' : ',' ), 'pad' )
                  . '</span>';

      $html .= padHighlightTokens ( substr ( $source, $at, $offset - $at ), 'pad' )
             . '<span class="flow" style="--hang: ' . ( strlen ( $lead ) + strlen ( $name ) + 2 ) . 'ch">'
             . implode ( ' ', $spans ) . '</span>';

      $at = $offset + strlen ( $whole );

    }

    return $html . padHighlightTokens ( substr ( $source, $at ), 'pad' );

  }

?>
