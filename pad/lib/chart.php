<?php

  // The pieces of the chart code needed where no chart is drawn: padChartTypes and
  // padChartType, the kinds a chart has - the files of charts/types/ - which the tag types
  // ask of every name nothing else claims (lib/type.php); padChartAttr, the text of an
  // attribute or an SVG element escaped - {emoji}, {country}, {countdown}, {timeago},
  // {jsonview}, {toc} - and padChartDedent, the content of a pair with its shared indent
  // taken off - {copy}, {diagram}, {highlight}. The rest of the charts is the subsystem in
  // charts/, its library charts/_lib/ loaded when a page draws one.

  // The kinds of chart: the names of the files of charts/types/, sorted - a kind is added by
  // adding its file. Read from the listing kept between requests (padDirList, lib/dirs.php),
  // as written on disk: asked of a file system that ignores case, {Bar} is no kind, as a tag
  // in another case is none (padTagBuiltIn).

  function padChartTypes () {

    static $types = NULL;

    if ( $types === NULL ) {
      $types = [];
      foreach ( padDirList ( PAD . 'charts/types' ) ?: [] as $file )
        if ( $file [0] != '.' and str_ends_with ( $file, '.php' ) )
          $types [] = substr ( $file, 0, -4 );
      sort ( $types );
    }

    return $types;

  }

  function padChartType ( $name ) {

    static $types = NULL;

    $types ??= array_flip ( padChartTypes () );

    return is_string ( $name ) and isset ( $types [$name] );

  }

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
