<?php

  // The user journey - diagrams/types/journey.php: the steps a person takes, how each one
  // felt and who took it, written as Mermaid's journey writes them.
  //
  //   title A day at the office          a heading over the picture
  //   section Morning                    a section: a band over its steps
  //     Make tea: 5: Me                  a step: its text, a score from 1 (bad) to 5 (good)
  //     Read the mail: 2: Me, Cat        and the people who take it, comma by comma
  //
  // The steps stand in a row of cards under the bands of their sections; under each card a
  // face at the height of its score - a smile for 4 and 5, a straight mouth for 3, a frown
  // below - the faces joined by a line, and a dot for every person who takes the step, in
  // the colour the legend under them gives that person.

  function padDiagramJourney ( $source, $title ) {

    global $padCheckSyntax;

    $sections = $bad = $people = [];
    $heading  = '';
    $first    = TRUE;

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $number => $line ) {

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : trim ( $line );

      if ( $line === '' )
        continue;

      if ( $first and $line == 'journey' ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      if ( preg_match ( '/^title\s+(.+)$/i', $line, $m ) )
        $heading = padDiagramFlat ( $m [1] );
      elseif ( preg_match ( '/^section\s+(.+)$/i', $line, $m ) )
        $sections [] = [ padDiagramFlat ( $m [1] ), [] ];
      elseif ( preg_match ( '/^(.+?)\s*:\s*(\d+)\s*(?::\s*(.*))?$/u', $line, $m ) ) {
        $score = (int) $m [2];
        if ( $score < 1 or $score > 5 ) {
          $bad [] = [ $number + 1, $line, "its score is $score, from 1 to 5" ];
          $score  = max ( 1, min ( 5, $score ) );
        }
        $who = array_values ( array_filter ( array_map ( 'trim', explode ( ',', $m [3] ?? '' ) ), 'strlen' ) );
        foreach ( $who as $one )
          $people [$one] ??= count ( $people ) % 8 + 1;
        if ( ! $sections )
          $sections [] = [ '', [] ];
        $sections [ count ( $sections ) - 1 ] [1] [] = [ padDiagramFlat ( $m [1] ), $score, $who ];
      } else
        $bad [] = [ $number + 1, $line, 'write title text, section text or a step as text: 5: who' ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the journey cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - " . $bad [0] [2] );

    $sections = array_values ( array_filter ( $sections, fn ( $section ) => $section [1] ) );

    if ( ! $sections )
      return '';

    // The cards as wide as the widest text asks, between 90 and 130 - a longer text wraps - so
    // a day of six steps fits a page's column.

    $widest = 0;
    foreach ( $sections as list ( , $steps ) )
      foreach ( $steps as list ( $text ) )
        $widest = max ( $widest, padDiagramWidth ( $text ) );

    $cardW = max ( 90, min ( 130, $widest + 20 ) );
    $wrap  = max ( 8, (int) floor ( ( $cardW - 16 ) / 7 ) );
    $lines = 1;

    foreach ( $sections as $s => list ( , $steps ) )
      foreach ( $steps as $t => $step ) {
        $sections [$s] [1] [$t] [3] = padDiagramWrap ( $step [0], $wrap );
        $lines = max ( $lines, count ( $sections [$s] [1] [$t] [3] ) );
      }

    // The legend is a row under the dots, a dot and a name for every person: beside the
    // cards it took the room of the longest name from every one of them.

    $legend = 12;
    foreach ( $people as $name => $slot )
      $legend += padDiagramWidth ( $name ) + 30;

    $left   = 12;
    $top    = 12 + ( $heading !== '' ? 30 : 0 );
    $cardY  = $top + 28 + 8;
    $cardH  = 16 * $lines + 16;
    $faceY  = $cardY + $cardH + 22;
    $dotY   = $faceY + 4 * 24 + 32;
    $height = (int) ceil ( $dotY + ( $people ? 14 + 28 : -16 ) + 12 );

    // The places across: the cards side by side, a wider gap between two sections.

    $x    = $left;
    $at   = [];
    $span = [];

    foreach ( $sections as $s => list ( , $steps ) ) {
      $span [$s] = [ $x ];
      foreach ( $steps as $t => $step ) {
        $at [$s] [$t] = $x + $cardW / 2;
        $x           += $cardW + 8;
      }
      $span [$s] [] = $x - 8;
      $x += 10;
    }

    $width = (int) ceil ( max ( $x - 18 + 12, $people ? $legend : 0, $heading !== '' ? padDiagramWidth ( $heading ) * 1.2 + 24 : 0 ) );
    $X     = fn ( $v ) => padChartXY ( $v );

    $desc = [];
    foreach ( $sections as list ( $name, $steps ) )
      foreach ( $steps as list ( $text, $score, $who ) )
        $desc [] = ( $name !== '' ? "$name: " : '' ) . "$text, $score of 5" . ( $who ? ' (' . implode ( ', ', $who ) . ')' : '' );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'journey', $heading, $sections ], $desc, $title, $width, $height, TRUE );

    $svg .= padDiagramStyle () . padDiagramJourneyStyle ();

    if ( $heading !== '' )
      $svg .= '<text class="pd-heading" x="12" y="' . $X ( 12 + 18 ) . '">' . padChartAttr ( $heading ) . '</text>';

    // The legend, under the dots.

    $x = 12;
    foreach ( $people as $name => $slot ) {
      $svg .= '<circle class="pd-who' . $slot . ' pd-dot" cx="' . $X ( $x + 6 ) . '" cy="' . $X ( $dotY + 28 ) . '" r="5"/>'
            . '<text x="' . $X ( $x + 16 ) . '" y="' . $X ( $dotY + 32 ) . '">' . padChartAttr ( $name ) . '</text>';
      $x   += padDiagramWidth ( $name ) + 30;
    }

    // The bands of the sections, by turns in the colour of a node and of a round node.

    foreach ( $sections as $s => list ( $name ) )
      if ( $name !== '' )
        $svg .= '<rect class="pd-band' . ( $s % 2 ) . '" x="' . $X ( $span [$s] [0] ) . '" y="' . $X ( $top ) . '" width="' . $X ( $span [$s] [1] - $span [$s] [0] ) . '" height="28" rx="4"/>'
              . '<text class="pd-name" x="' . $X ( ( $span [$s] [0] + $span [$s] [1] ) / 2 ) . '" y="' . $X ( $top + 18 ) . '" text-anchor="middle">' . padChartAttr ( $name ) . '</text>';

    // The line through the faces, under them.

    $d = '';
    foreach ( $sections as $s => list ( , $steps ) )
      foreach ( $steps as $t => $step )
        $d .= ( $d === '' ? 'M' : 'L' ) . $X ( $at [$s] [$t] ) . ',' . $X ( $faceY + ( 5 - $step [1] ) * 24 );

    $svg .= '<path class="pd-trail" d="' . $d . '"/>';

    foreach ( $sections as $s => list ( $name, $steps ) )
      foreach ( $steps as $t => list ( $text, $score, $who, $wrapped ) ) {

        $cx   = $at [$s] [$t];
        $cy   = $faceY + ( 5 - $score ) * 24;
        $tip  = ( $name !== '' ? "$name: " : '' ) . "$text, $score of 5" . ( $who ? ' (' . implode ( ', ', $who ) . ')' : '' );

        $svg .= '<g class="pc-mark"><title>' . padChartAttr ( $tip ) . '</title>'
              . '<rect class="pd-node pd-card" x="' . $X ( $cx - $cardW / 2 ) . '" y="' . $X ( $cardY ) . '" width="' . $X ( $cardW ) . '" height="' . $X ( $cardH ) . '" rx="4"/>'
              . padDiagramText ( $wrapped, $cx, $cardY + $cardH / 2 )
              . '<line class="pd-life" x1="' . $X ( $cx ) . '" x2="' . $X ( $cx ) . '" y1="' . $X ( $cardY + $cardH ) . '" y2="' . $X ( $cy - 13 ) . '"/>'
              . padDiagramJourneyFace ( $cx, $cy, $score, $X );

        foreach ( $who as $k => $one )
          $svg .= '<circle class="pd-who' . $people [$one] . ' pd-dot" cx="' . $X ( $cx + ( $k - ( count ( $who ) - 1 ) / 2 ) * 13 ) . '" cy="' . $X ( $dotY ) . '" r="5"/>';

        $svg .= '</g>';

      }

    return "$svg</svg>";

  }

  // The accessible name a journey has without title=: its title line, else User journey.

  function padDiagramJourneyTitle ( $source ) {

    return preg_match ( '/^\s*title\s+(.+?)\s*$/im', (string) $source, $m ) ? padDiagramFlat ( $m [1] ) : 'User journey';

  }

  // A face of 13 round at x, y for a score: two eyes and a mouth that smiles at 4 and 5, is
  // straight at 3 and frowns below, in the colour of its score.

  function padDiagramJourneyFace ( $x, $y, $score, $X ) {

    $mouth = ( $score >= 4 ) ? 'M' . $X ( $x - 5 ) . ',' . $X ( $y + 3 ) . 'Q' . $X ( $x ) . ',' . $X ( $y + 8 ) . ' ' . $X ( $x + 5 ) . ',' . $X ( $y + 3 )
           : ( ( $score == 3 ) ? 'M' . $X ( $x - 5 ) . ',' . $X ( $y + 5 ) . 'H' . $X ( $x + 5 )
           :   'M' . $X ( $x - 5 ) . ',' . $X ( $y + 7 ) . 'Q' . $X ( $x ) . ',' . $X ( $y + 2 ) . ' ' . $X ( $x + 5 ) . ',' . $X ( $y + 7 ) );

    return '<circle class="pd-s' . $score . '" cx="' . $X ( $x ) . '" cy="' . $X ( $y ) . '" r="13"/>'
         . '<circle class="pd-eye" cx="' . $X ( $x - 4.5 ) . '" cy="' . $X ( $y - 3 ) . '" r="1.6"/>'
         . '<circle class="pd-eye" cx="' . $X ( $x + 4.5 ) . '" cy="' . $X ( $y - 3 ) . '" r="1.6"/>'
         . '<path class="pd-mouth" d="' . $mouth . '"/>';

  }

  // The rules of the journey: the scores from red to green and the face drawn on them, as
  // custom properties with light-dark() defaults; the people in the colours of the chart
  // palette; the bands, the cards and the line through the faces. A card is a node of its
  // own colour by two classes, so the .pd-node of a diagram further down the page, which
  // weighs as much as one, does not paint it over.

  function padDiagramJourneyStyle () {

    $roles = [ 'score-1' => [ '#e34948', '#e66767' ], 'score-2' => [ '#eb6834', '#d95926' ], 'score-3' => [ '#eda100', '#c98500' ],
               'score-4' => [ '#8ab82e', '#7aa526' ], 'score-5' => [ '#1baf7a', '#199e70' ], 'face'    => [ '#2b2a27', '#1a1a19' ] ];

    $light = $both = $css = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-diagram-$role:$day;";
      $both  .= "--pad-diagram-$role:light-dark($day,$night);";
    }

    for ( $slot = 1; $slot <= 8; $slot++ )
      $css .= ".pad-chart-diagram .pd-who$slot{fill:var(--pad-chart-$slot)}";

    for ( $score = 1; $score <= 5; $score++ )
      $css .= ".pad-chart-diagram .pd-s$score{fill:var(--pad-diagram-score-$score)}";

    return '<style' . padStyleNonce () . '>'
         . ":where(.pad-chart-diagram){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chart-diagram){{$both}}}"
         . $css
         . '.pad-chart-diagram .pd-heading{font-size:15px;font-weight:700}'
         . '.pad-chart-diagram .pd-name{font-weight:700}'
         . '.pad-chart-diagram .pd-band0{fill:var(--pad-diagram-node)}'
         . '.pad-chart-diagram .pd-band1{fill:var(--pad-diagram-round)}'
         . '.pad-chart-diagram .pd-node.pd-card{fill:var(--pad-chart-surface)}'
         . '.pad-chart-diagram .pd-trail{fill:none;stroke:var(--pad-diagram-edge);stroke-width:1.5;stroke-opacity:.5}'
         . '.pad-chart-diagram .pd-eye{fill:var(--pad-diagram-face)}'
         . '.pad-chart-diagram .pd-mouth{fill:none;stroke:var(--pad-diagram-face);stroke-width:1.6;stroke-linecap:round}'
         . '.pad-chart-diagram .pd-dot{stroke:var(--pad-chart-surface);stroke-width:1.5}'
         . '</style>';

  }

?>
