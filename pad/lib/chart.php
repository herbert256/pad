<?php

  // The two pieces of the {chart} code that tags drawing no chart use too: padChartAttr, the
  // text of an attribute or an SVG element escaped - {emoji}, {country}, {countdown},
  // {timeago}, {jsonview}, {toc} - and padChartDedent, the content of a pair with its shared
  // indent taken off - {copy}, {diagram}, {highlight}. The rest of the charts is
  // tags/_lib/chart.php, loaded when a page uses a tag that draws one.

  // The content of a pair is written indented under its tag: the indent all its lines share
  // is taken off, else the trim of padData would leave YAML's first line out of step with
  // the rest.

  function padChartDedent ( $source ) {

    $lines  = explode ( "\n", str_replace ( "\r\n", "\n", trim ( $source, "\n\r" ) ) );
    $indent = PHP_INT_MAX;

    foreach ( $lines as $line )
      if ( trim ( $line ) !== '' )
        $indent = min ( $indent, strlen ( $line ) - strlen ( ltrim ( $line, " \t" ) ) );

    // Without an indent to take off the text is the same lines all the same - the blank line
    // after the tag left out - where it came back as it was: a diagram's "cannot read line
    // N" named another line for content not indented than for content that was.

    if ( $indent == PHP_INT_MAX or $indent == 0 )
      return implode ( "\n", $lines );

    foreach ( $lines as $key => $line )
      $lines [$key] = substr ( $line, min ( $indent, strlen ( $line ) - strlen ( ltrim ( $line, " \t" ) ) ) );

    return implode ( "\n", $lines );

  }

  function padChartAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
