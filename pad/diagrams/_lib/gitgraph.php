<?php

  // The commit graph - diagrams/types/gitgraph.php: the commits of a repository on the
  // branches they were made on, the branches leaving and the merges joining, written as
  // Mermaid's gitGraph writes them and drawn left to right.
  //
  //   commit                             a commit on the branch checked out - main at first
  //   commit id: "Fix" tag: "v1.0"       its name under it, a tag over it; type: HIGHLIGHT
  //                                      draws it square, type: REVERSE crossed out
  //   branch develop                     a branch from here, checked out at once
  //   checkout main                      (or switch main) on to another branch
  //   merge develop                      a merge of a branch into the one checked out
  //   cherry-pick id: "Fix"              a copy of a commit of another branch
  //
  // Each branch is a lane of its own colour, in the order the branches are made - order: n
  // after the name moves one - and each commit a column of its own, in the order written.

  function padDiagramGitgraph ( $source, $title ) {

    global $padCheckSyntax;

    $commits  = $bad = $ids = $events = [];
    $branches = [ 'main' => [ 'lane' => 0, 'head' => NULL, 'order' => 0, 'made' => 0 ] ];
    $current  = 'main';
    $first    = TRUE;
    $name     = '"[^"]+"|[\p{L}\p{M}\p{N}_.\/-]+';

    // The options of a commit, a merge or a cherry-pick: id: "x" tag: "y" type: Z.

    $options = function ( $text, &$wrong ) {
      $found = [];
      $rest  = preg_replace_callback ( '/\b(id|tag|type|msg|parent|order)\s*:\s*("[^"]*"|\S+)/i', function ( $m ) use ( &$found ) {
        $found [ strtolower ( $m [1] ) ] = trim ( $m [2], '"' );
        return '';
      }, $text );
      $wrong = trim ( $rest ) !== '';
      return $found;
    };

    $fail = function ( $number, $line, $why ) use ( &$bad ) {
      $bad [] = [ $number + 1, $line, $why ];
    };

    foreach ( preg_split ( '/\r?\n/', (string) $source ) as $number => $line ) {

      $line = preg_match ( '/^\s*%%/', $line ) ? '' : trim ( $line );

      if ( $line === '' )
        continue;

      if ( $first and preg_match ( '/^gitGraph(\s+(LR|TB|BT):?)?$/', $line ) ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      if ( preg_match ( '/^commit\b(.*)$/i', $line, $m ) ) {

        $opt = $options ( $m [1], $wrong );
        if ( $wrong ) { $fail ( $number, $line, 'write commit id: "name" tag: "name" type: HIGHLIGHT' ); continue; }
        $id = $opt ['id'] ?? ( $opt ['msg'] ?? '' );
        if ( $id !== '' and isset ( $ids [$id] ) ) { $fail ( $number, $line, "there is a commit $id already" ); continue; }

        $mine       = $branches [$current] ['head'] ?? ( $branches [$current] ['from'] ?? NULL );
        $parents    = ( $mine !== NULL ) ? [ $mine ] : [];
        $commits [] = [ 'id' => $id, 'branch' => $current, 'parents' => $parents, 'type' => strtoupper ( $opt ['type'] ?? 'NORMAL' ), 'tag' => $opt ['tag'] ?? '', 'kind' => 'commit' ];

      } elseif ( preg_match ( "/^branch\s+($name)(.*)$/iu", $line, $m ) ) {

        $branch = trim ( $m [1], '"' );
        $opt    = $options ( $m [2], $wrong );
        if ( $wrong ) { $fail ( $number, $line, 'write branch name, or branch name order: 2' ); continue; }
        if ( isset ( $branches [$branch] ) ) { $fail ( $number, $line, "there is a branch $branch already" ); continue; }

        $from = $branches [$current] ['head'] ?? ( $branches [$current] ['from'] ?? NULL );
        $branches [$branch] = [ 'lane' => count ( $branches ), 'head' => NULL, 'from' => $from,
                                'order' => (int) ( $opt ['order'] ?? count ( $branches ) ), 'made' => count ( $branches ) ];
        $events []  = [ count ( $commits ), "$branch branches off $current" ];
        $current    = $branch;
        continue;

      } elseif ( preg_match ( "/^(?:checkout|switch)\s+($name)$/iu", $line, $m ) ) {

        $branch = trim ( $m [1], '"' );
        if ( ! isset ( $branches [$branch] ) ) { $fail ( $number, $line, "there is no branch $branch" ); continue; }
        $current = $branch;
        continue;

      } elseif ( preg_match ( "/^merge\s+($name)(.*)$/iu", $line, $m ) ) {

        $branch = trim ( $m [1], '"' );
        $opt    = $options ( $m [2], $wrong );
        if ( $wrong ) { $fail ( $number, $line, 'write merge branch id: "name" tag: "name"' ); continue; }
        if ( ! isset ( $branches [$branch] ) ) { $fail ( $number, $line, "there is no branch $branch" ); continue; }
        if ( $branch == $current ) { $fail ( $number, $line, "a branch cannot be merged into itself" ); continue; }
        if ( $branches [$branch] ['head'] === NULL ) { $fail ( $number, $line, "the branch $branch has no commit of its own to merge" ); continue; }
        $id = $opt ['id'] ?? '';
        if ( $id !== '' and isset ( $ids [$id] ) ) { $fail ( $number, $line, "there is a commit $id already" ); continue; }

        $mine = $branches [$current] ['head'] ?? ( $branches [$current] ['from'] ?? NULL );
        $commits [] = [ 'id' => $id, 'branch' => $current, 'parents' => array_values ( array_filter ( [ $mine, $branches [$branch] ['head'] ], fn ( $p ) => $p !== NULL ) ),
                        'type' => strtoupper ( $opt ['type'] ?? 'NORMAL' ), 'tag' => $opt ['tag'] ?? '', 'kind' => 'merge', 'merged' => $branch ];

      } elseif ( preg_match ( '/^cherry-pick\b(.*)$/i', $line, $m ) ) {

        $opt = $options ( $m [1], $wrong );
        $id  = $opt ['id'] ?? '';
        if ( $wrong or $id === '' ) { $fail ( $number, $line, 'write cherry-pick id: "name"' ); continue; }
        if ( ! isset ( $ids [$id] ) ) { $fail ( $number, $line, "there is no commit $id" ); continue; }
        if ( $commits [ $ids [$id] ] ['branch'] == $current ) { $fail ( $number, $line, "the commit $id is on $current already" ); continue; }

        $mine = $branches [$current] ['head'] ?? ( $branches [$current] ['from'] ?? NULL );
        $commits [] = [ 'id' => "$id (picked)", 'branch' => $current, 'parents' => $mine !== NULL ? [ $mine ] : [],
                        'type' => 'NORMAL', 'tag' => $opt ['tag'] ?? '', 'kind' => 'cherry', 'picked' => $id ];

      } else {
        $fail ( $number, $line, 'write commit, branch name, checkout name, merge name or cherry-pick id: "name"' );
        continue;
      }

      $k = count ( $commits ) - 1;

      if ( ! in_array ( $commits [$k] ['type'], [ 'NORMAL', 'REVERSE', 'HIGHLIGHT' ] ) ) {
        $fail ( $number, $line, 'type is NORMAL, REVERSE or HIGHLIGHT' );
        $commits [$k] ['type'] = 'NORMAL';
      }

      if ( $commits [$k] ['id'] !== '' and $commits [$k] ['kind'] != 'cherry' )
        $ids [ $commits [$k] ['id'] ] = $k;

      $branches [$current] ['head'] = $k;

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the commit graph cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - " . $bad [0] [2] );

    if ( ! $commits )
      return '';

    // As large as a mind map or a tree at most: 2000 commits, 2000 branches. 8000 branches
    // were three megabytes of SVG, a lane each, with no word that it was too much.

    foreach ( [ 'commits' => count ( $commits ), 'branches' => count ( $branches ) ] as $what => $count )
      if ( $count > 2000 ) {
        padError ( "the commit graph is too large to lay out: $count $what, 2000 at most - draw it in parts" );
        return '';
      }

    // The lanes: main first, the others by their order: and then as they were made.

    $names = array_keys ( $branches );
    usort ( $names, fn ( $a, $b ) => [ $a == 'main' ? 0 : 1, $branches [$a] ['order'], $branches [$a] ['made'] ] <=> [ $b == 'main' ? 0 : 1, $branches [$b] ['order'], $branches [$b] ['made'] ] );

    $tags  = (bool) array_filter ( array_column ( $commits, 'tag' ), 'strlen' );
    $space = $tags ? 56 : 44;
    $labelW = 0;

    foreach ( $names as $i => $branch ) {
      $branches [$branch] ['lane'] = $i;
      $branches [$branch] ['slot'] = $i % 8 + 1;
      $labelW = max ( $labelW, padDiagramWidth ( $branch ) + 20 );
    }

    // The columns: as wide as the name under the commit or the tag over it asks, 40 at least.

    $x = 12 + $labelW + 30;

    foreach ( $commits as $k => $commit ) {
      $wide = max ( 40, padDiagramWidth ( $commit ['id'] ) * 0.85 + 12, padDiagramWidth ( $commit ['tag'] ) * 0.85 + 16 );
      $commits [$k] ['wide'] = $wide;
      $commits [$k] ['x']    = $x + $wide / 2;
      $commits [$k] ['y'] = ( $tags ? 40 : 24 ) + $branches [ $commit ['branch'] ] ['lane'] * $space;
      $x += $wide;
    }

    $width  = (int) ceil ( $x + 18 );
    $height = (int) ceil ( ( $tags ? 40 : 24 ) + ( count ( $names ) - 1 ) * $space + 36 );
    $X      = fn ( $v ) => padChartXY ( $v );

    // Said in order: the commits, the branches leaving, the merges and the copies.

    // Where each commit's line stands in it is kept for the commit's tooltip: counted again
    // per commit from every branch event, 8000 branches took 6 seconds.

    $desc = $descAt = [];
    $next = 0;

    foreach ( $commits as $k => $commit ) {
      while ( $next < count ( $events ) and $events [$next] [0] <= $k )
        $desc [] = $events [$next++] [1];
      $descAt [$k] = count ( $desc );
      $what    = [ 'commit' => 'commit', 'merge' => 'merge of ' . ( $commit ['merged'] ?? '' ), 'cherry' => 'cherry-pick of ' . ( $commit ['picked'] ?? '' ) ] [ $commit ['kind'] ];
      $desc [] = $commit ['branch'] . ': ' . $what . ( ( $commit ['id'] !== '' && $commit ['kind'] != 'cherry' ) ? ' ' . $commit ['id'] : '' )
               . ( $commit ['tag'] !== '' ? ' (tag ' . $commit ['tag'] . ')' : '' );
    }

    while ( $next < count ( $events ) )
      $desc [] = $events [$next++] [1];

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'gitgraph', $commits, $names ], $desc, $title, $width, $height, TRUE );

    $svg .= padDiagramStyle () . padDiagramGitStyle ();

    // The names of the branches at the start of their lanes.

    foreach ( $names as $branch ) {
      $y    = ( $tags ? 40 : 24 ) + $branches [$branch] ['lane'] * $space;
      $slot = $branches [$branch] ['slot'];
      $svg .= '<rect class="pd-g' . $slot . '" x="12" y="' . $X ( $y - 10 ) . '" width="' . $X ( $labelW ) . '" height="20" rx="10"/>'
            . '<text class="pd-lane-name" x="' . $X ( 12 + $labelW / 2 ) . '" y="' . $X ( $y + 4 ) . '" text-anchor="middle">' . padChartAttr ( $branch ) . '</text>';
    }

    // The lines between a commit and its parents. One on its own lane runs along it; one to
    // a new branch's first commit leaves its parent's lane past the parent's column and
    // turns onto the new lane, and one from the head of a merged branch runs along that
    // lane and turns up or down to the merge before the merge's column: the turns stand
    // between the columns, clear of the names under the commits. The lines that change lanes
    // go first, so a lane's own line lies over the part of them that runs along it.

    $along = $across = '';

    foreach ( $commits as $commit )
      foreach ( $commit ['parents'] as $p ) {

        $from = $commits [$p];

        if ( $from ['y'] == $commit ['y'] ) {
          $along .= '<path class="pd-git-line pd-gl' . $branches [ $commit ['branch'] ] ['slot'] . '" d="M' . $X ( $from ['x'] ) . ',' . $X ( $from ['y'] ) . 'H' . $X ( $commit ['x'] ) . '"/>';
          continue;
        }

        $merge = ( $commit ['kind'] == 'merge' and $p === end ( $commit ['parents'] ) );
        $turn  = $merge ? $commit ['x'] - $commit ['wide'] / 2 - 4 : $from ['x'] + $from ['wide'] / 2 + 4;
        $r     = min ( 10, abs ( $commit ['y'] - $from ['y'] ) / 2 );
        $sign  = ( $commit ['y'] > $from ['y'] ) ? 1 : -1;
        $lane  = $merge ? $from ['branch'] : $commit ['branch'];

        $across .= '<path class="pd-git-line pd-gl' . $branches [$lane] ['slot'] . '" d="M' . $X ( $from ['x'] ) . ',' . $X ( $from ['y'] )
                 . 'H' . $X ( $turn - $r ) . 'Q' . $X ( $turn ) . ',' . $X ( $from ['y'] ) . ' ' . $X ( $turn ) . ',' . $X ( $from ['y'] + $sign * $r )
                 . 'V' . $X ( $commit ['y'] - $sign * $r ) . 'Q' . $X ( $turn ) . ',' . $X ( $commit ['y'] ) . ' ' . $X ( $turn + $r ) . ',' . $X ( $commit ['y'] )
                 . 'H' . $X ( $commit ['x'] ) . '"/>';

      }

    $svg .= $across . $along;

    // The commits: a dot in the colour of their branch, a merge with a ring in it, a
    // highlight a square, a reverse crossed out, a cherry-pick with a dot of the background;
    // the name under, the tag over.

    foreach ( $commits as $k => $commit ) {

      $x    = $commit ['x'];
      $y    = $commit ['y'];
      $slot = $branches [ $commit ['branch'] ] ['slot'];

      $svg .= '<g class="pc-mark"><title>' . padChartAttr ( $desc [ $descAt [$k] ] ) . '</title>';

      if ( $commit ['type'] == 'HIGHLIGHT' )
        $svg .= '<rect class="pd-g' . $slot . ' pd-commit" x="' . $X ( $x - 7 ) . '" y="' . $X ( $y - 7 ) . '" width="14" height="14" rx="2"/>';
      else
        $svg .= '<circle class="pd-g' . $slot . ' pd-commit" cx="' . $X ( $x ) . '" cy="' . $X ( $y ) . '" r="7"/>';

      if ( $commit ['kind'] == 'merge' or $commit ['kind'] == 'cherry' )
        $svg .= '<circle class="pd-hole" cx="' . $X ( $x ) . '" cy="' . $X ( $y ) . '" r="' . ( $commit ['kind'] == 'merge' ? 3.5 : 2 ) . '"/>';

      if ( $commit ['type'] == 'REVERSE' )
        $svg .= '<path class="pd-cross" d="M' . $X ( $x - 3.5 ) . ',' . $X ( $y - 3.5 ) . 'l7,7m0,-7l-7,7"/>';

      if ( $commit ['id'] !== '' )
        $svg .= '<text class="pd-commit-name" x="' . $X ( $x ) . '" y="' . $X ( $y + 21 ) . '" text-anchor="middle">' . padChartAttr ( $commit ['id'] ) . '</text>';

      if ( $commit ['tag'] !== '' ) {
        $w    = padDiagramWidth ( $commit ['tag'] ) * 0.85 + 12;
        $svg .= '<path class="pd-tag" d="M' . $X ( $x - $w / 2 ) . ',' . $X ( $y - 30 ) . 'h' . $X ( $w ) . 'v16h' . $X ( - $w / 2 + 4 ) . 'l-4,4l-4,-4H' . $X ( $x - $w / 2 ) . 'Z"/>'
              . '<text class="pd-commit-name" x="' . $X ( $x ) . '" y="' . $X ( $y - 18 ) . '" text-anchor="middle">' . padChartAttr ( $commit ['tag'] ) . '</text>';
      }

      $svg .= '</g>';

    }

    return "$svg</svg>";

  }

  // The rules of the commit graph: a lane, its dots and its name in a colour of the chart
  // palette, the names of commits small, a tag on a plate.

  function padDiagramGitStyle () {

    $css = '';

    for ( $slot = 1; $slot <= 8; $slot++ )
      $css .= ".pad-chart-diagram .pd-g$slot{fill:var(--pad-chart-$slot)}.pad-chart-diagram .pd-gl$slot{stroke:var(--pad-chart-$slot)}";

    return '<style' . padStyleNonce () . '>' . $css
         . '.pad-chart-diagram .pd-git-line{fill:none;stroke-width:3;stroke-linecap:round}'
         . '.pad-chart-diagram .pd-commit{stroke:var(--pad-chart-surface);stroke-width:2}'
         . '.pad-chart-diagram .pd-hole{fill:var(--pad-chart-surface)}'
         . '.pad-chart-diagram .pd-cross{stroke:var(--pad-chart-surface);stroke-width:2;stroke-linecap:round}'
         . '.pad-chart-diagram .pd-lane-name{fill:var(--pad-chart-surface);font-size:11px;font-weight:700}'
         . '.pad-chart-diagram .pd-commit-name{font-size:10px}'
         . '.pad-chart-diagram .pd-tag{fill:var(--pad-diagram-note);stroke:var(--pad-diagram-decision-border);stroke-width:1}'
         . '</style>';

  }

?>
