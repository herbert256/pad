<?php

  // The fishbone - subs/diagrams/types/fishbone.php: the possible causes of an effect, the
  // cause-and-effect diagram of Kaoru Ishikawa, written as Mermaid's ishikawa writes it - the
  // hierarchy from the indentation.
  //
  //   Blurry photo                       the effect: the first line, the least indented
  //     Equipment                        a category: a bone off the spine
  //       Dirty lens                     a cause: a rib along the bone
  //       Slow shutter
  //         Low light                    a cause of a cause: a rib off its parent's
  //     People
  //       Shaky hands
  //
  // The effect stands in a head on the right, the spine runs into it, and the categories are
  // bones above and below the spine by turns, the first on the left, each leaning toward the
  // head with its category in a box at its outer end. A cause is its text over a rib that
  // ends on the bone; a cause of a cause hangs off its parent's rib by a stem, one step
  // further from the bone. Each bone reads from its category toward the spine - above the
  // spine downward, below it upward, the two halves mirrored - so a stem never meets the
  // bone. The bones of a side stand as close as their texts allow: every band of a bone is
  // kept clear of the bones before it, not the box round the whole bone. The arrows say no
  // more than the text: possible causes, not a proof of them.

  function padDiagramFishbone ( $source, $title ) {

    global $padCheckSyntax;

    $nodes = $stack = $bad = [];
    $first = TRUE;

    foreach ( preg_split ( '/\r?\n/', padUnprotectQuotes ( (string) $source ) ) as $number => $line ) {

      if ( preg_match ( '/^\s*%%/', $line ) or trim ( $line ) === '' )
        continue;

      $indent = strlen ( str_replace ( "\t", '    ', $line ) ) - strlen ( ltrim ( str_replace ( "\t", '    ', $line ) ) );
      $line   = trim ( $line );

      if ( $first and in_array ( $line, [ 'fishbone', 'ishikawa', 'ishikawa-beta' ] ) ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      while ( $stack and end ( $stack ) [0] >= $indent )
        array_pop ( $stack );

      if ( $nodes and ! $stack ) {
        $bad [] = [ $number + 1, $line ];
        continue;
      }

      $parent = $stack ? end ( $stack ) [1] : -1;
      $k      = count ( $nodes );
      $text   = preg_match ( '/^"([^"]*)"$/u', $line, $quoted ) ? $quoted [1] : $line;

      $nodes [$k] = [ 'text' => $text, 'parent' => $parent, 'kids' => [],
                      'depth' => $parent < 0 ? 0 : $nodes [$parent] ['depth'] + 1 ];

      if ( $parent >= 0 )
        $nodes [$parent] ['kids'] [] = $k;

      $stack [] = [ $indent, $k ];

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the fishbone has one effect, and line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 )
               . "' stands beside it - indent the categories and their causes under the first line" );

    if ( ! $nodes )
      return '';

    if ( count ( $nodes ) > 2000 ) {
      padError ( 'the fishbone is too large to lay out: ' . count ( $nodes ) . ' nodes, 2000 at most - draw it in parts' );
      return '';
    }

    $slant = 0.5;

    // Every bone in its own coordinates: its foot on the spine at 0, t the distance from the
    // spine, the bone at -t * slant. Its rows - the causes under the category, in the order
    // of the text - go from the box inward; a row is a band of its lines and room round them.

    $bones = [];

    foreach ( $nodes [0] ['kids'] as $i => $c ) {

      $rows = [];
      $walk = function ( $k ) use ( &$walk, &$rows, $nodes ) {
        foreach ( $nodes [$k] ['kids'] as $kid ) {
          $rows [] = $kid;
          $walk ( $kid );
        }
      };
      $walk ( $c );

      $label  = padDiagramWrap ( $nodes [$c] ['text'], 18 );
      $labelW = 0;
      foreach ( $label as $one )
        $labelW = max ( $labelW, padDiagramWidth ( $one ) );

      $bone = [ 'node' => $c, 'label' => $label, 'boxW' => $labelW + 20, 'boxH' => 16 * count ( $label ) + 12,
                'rows' => [], 'side' => ( $i % 2 ) ? 1 : -1 ];

      $bands = 0;
      foreach ( $rows as $k ) {
        $lines  = padDiagramWrap ( $nodes [$k] ['text'], 26 );
        $textW  = 0;
        foreach ( $lines as $one )
          $textW = max ( $textW, padDiagramWidth ( $one ) );
        $bone ['rows'] [$k] = [ 'lines' => $lines, 'textW' => $textW, 'band' => 16 * count ( $lines ) + 10 ];
        $bands += 16 * count ( $lines ) + 10;
      }

      $bone ['T'] = max ( 40, 10 + $bands + 16 );

      // The rows from the box inward: the band of a row reaches from far (toward the box) to
      // near (toward the spine); the rib lies 3 inside the near edge. A cause ends on the
      // bone, a cause of a cause on its parent's stem, 14 in from where the parent ends.

      // The text of a cause ends 6 short of where the bone is at the far edge of its band: the
      // bone leans out under the text, and ended where the rib meets it, the text ran into it.

      $far = $bone ['T'] - 10;
      $end = [];

      foreach ( $bone ['rows'] as $k => $row ) {
        $near  = $far - $row ['band'];
        $rib   = $near + 3;
        $p     = $nodes [$k] ['parent'];
        $endX  = ( $p == $c ) ? -$slant * $rib : $end [$p] - 14;
        $textX = ( $p == $c ) ? -$slant * $far - 6 : $endX - 6;
        $end [$k] = $endX;
        $bone ['rows'] [$k] += [ 'far' => $far, 'near' => $near, 'rib' => $rib, 'end' => $endX, 'textX' => $textX,
                                 'left' => $textX - $row ['textW'] - 6, 'stem' => ( $p == $c ) ? NULL : $p ];
        $far = $near;
      }

      // The box at the bone's outer end, centred on it.

      $tip = -$slant * $bone ['T'];

      $bone ['tip'] = $tip;

      // What the bone covers: its leftmost reach band by band - the rows, the box and the
      // bone itself between them - and its rightmost, the bone and the box.

      $bone ['lefts'] = [ [ $bone ['T'], $bone ['T'] + $bone ['boxH'], $tip - $bone ['boxW'] / 2 ] ];
      $bone ['lefts'] [] = [ 0, $bone ['T'], NULL ];

      foreach ( $bone ['rows'] as $row )
        $bone ['lefts'] [] = [ $row ['near'], $row ['far'], $row ['left'] ];

      $bone ['rights'] = [ [ 0, $bone ['T'], NULL ], [ $bone ['T'], $bone ['T'] + $bone ['boxH'], $tip + $bone ['boxW'] / 2 ] ];

      $bone ['minX'] = min ( array_merge ( [ $tip - $bone ['boxW'] / 2 ], array_column ( $bone ['rows'], 'left' ) ) );
      $bone ['maxX'] = max ( 0, $tip + $bone ['boxW'] / 2 );

      $bones [] = $bone;

    }

    // The feet along the spine, a side at a time: each bone as far left as the bones before it
    // on its side allow, band by band, 20 clear of them and 48 at least from the foot before.
    // A reach of NULL is the bone's own line, -t * slant, taken at the end of the overlap
    // nearest the other: the left of the line at its far end, the right at its near end.

    $placed = [ -1 => [], 1 => [] ];

    foreach ( $bones as $b => $bone ) {

      $foot = -$bone ['minX'];

      foreach ( $placed [ $bone ['side'] ] as $before ) {

        $foot = max ( $foot, $bones [$before] ['foot'] + 48 );

        foreach ( $bone ['lefts'] as list ( $a0, $a1, $left ) )
          foreach ( $bones [$before] ['rights'] as list ( $b0, $b1, $right ) ) {
            $o0 = max ( $a0, $b0 );
            $o1 = min ( $a1, $b1 );
            if ( $o1 <= $o0 )
              continue;
            $l    = $left  ?? -$slant * $o1;
            $r    = $right ?? -$slant * $o0;
            $foot = max ( $foot, $bones [$before] ['foot'] + $r + 20 - $l );
          }

      }

      $bones [$b] ['foot'] = $foot;
      $placed [ $bone ['side'] ] [] = $b;

    }

    // The head: the effect in a box past the rightmost reach of every bone, the spine from
    // left of the first foot into it.

    $head  = padDiagramWrap ( $nodes [0] ['text'], 16 );
    $headW = 0;
    foreach ( $head as $one )
      $headW = max ( $headW, padDiagramWidth ( $one ) * 1.1 );

    $headW += 28;
    $headH  = 16 * count ( $head ) + 22;

    $right = 0;
    $tail  = 0;
    $up    = $headH / 2;
    $down  = $headH / 2;
    $left  = 0;

    foreach ( $bones as $bone ) {
      $right = max ( $right, $bone ['foot'] + $bone ['maxX'] );
      $left  = min ( $left, $bone ['foot'] + $bone ['minX'] );
      $reach = $bone ['T'] + $bone ['boxH'];
      if ( $bone ['side'] < 0 )
        $up = max ( $up, $reach );
      else
        $down = max ( $down, $reach );
    }

    if ( $bones )
      $tail = min ( array_column ( $bones, 'foot' ) ) - 24;

    $left   = min ( $left, $tail );
    $headX  = max ( $right, $bones ? 0 : 60 ) + 32;
    $spineY = $up;
    $offX   = 12 - $left;
    $offY   = 12;
    $width  = (int) ceil ( $headX + $headW - $left + 24 );
    $height = (int) ceil ( $up + $down + 24 );

    $X = fn ( $x ) => padChartXY ( $x + $offX );
    $Y = fn ( $y ) => padChartXY ( $y + $offY + $spineY );

    // The description: the effect and its categories, then every node with causes and them.

    $say  = fn ( $k ) => padDiagramSay ( padDiagramFlat ( $nodes [$k] ['text'] ) );
    $desc = [ $say ( 0 ) . ( $nodes [0] ['kids'] ? ' - possible causes: ' . implode ( ', ', array_map ( $say, $nodes [0] ['kids'] ) ) : '' ) ];

    foreach ( $nodes as $k => $node )
      if ( $k and $node ['kids'] )
        $desc [] = $say ( $k ) . ': ' . implode ( ', ', array_map ( $say, $node ['kids'] ) );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'fishbone', $nodes ], $desc, $title, $width, $height, FALSE );

    $svg .= padDiagramStyle () . padDiagramFishboneStyle ();

    // The spine, its arrow into the head.

    $svg .= '<path class="pd-edge pd-fb-spine" d="M' . $X ( $tail ) . ',' . $Y ( 0 ) . 'H' . $X ( $headX - 10 ) . '"/>'
          . '<path class="pd-head" d="M' . $X ( $headX ) . ',' . $Y ( 0 ) . 'L' . $X ( $headX - 12 ) . ',' . $Y ( -6 ) . 'L' . $X ( $headX - 12 ) . ',' . $Y ( 6 ) . 'Z"/>';

    foreach ( $bones as $bone ) {

      $s    = $bone ['side'];
      $foot = $bone ['foot'];
      $c    = $bone ['node'];

      $svg .= '<g class="pc-mark"><title>' . padDiagramEsc ( $say ( $c ) . ( $nodes [$c] ['kids'] ? ': ' . implode ( ', ', array_map ( $say, $nodes [$c] ['kids'] ) ) : '' ) ) . '</title>'
            . '<path class="pd-edge pd-fb-bone" d="M' . $X ( $foot + $bone ['tip'] ) . ',' . $Y ( $s * $bone ['T'] ) . 'L' . $X ( $foot ) . ',' . $Y ( 0 ) . '"/>';

      // The stems first, the ribs and their texts over them.

      foreach ( $bone ['rows'] as $k => $row )
        if ( $row ['stem'] !== NULL ) {
          $p    = $bone ['rows'] [ $row ['stem'] ];
          $svg .= '<path class="pd-edge pd-fb-rib" d="M' . $X ( $foot + $p ['end'] - 14 ) . ',' . $Y ( $s * $p ['rib'] ) . 'V' . $Y ( $s * $row ['rib'] ) . '"/>';
        }

      foreach ( $bone ['rows'] as $k => $row ) {

        $deep = $nodes [$k] ['depth'] > 2;
        $svg .= '<path class="pd-edge pd-fb-rib" d="M' . $X ( $foot + $row ['left'] ) . ',' . $Y ( $s * $row ['rib'] ) . 'H' . $X ( $foot + $row ['end'] ) . '"/>';

        // Above the spine the text stands over its rib, below it under the rib.

        $count = count ( $row ['lines'] );
        $first = ( $s < 0 ) ? -$row ['rib'] - 5 - 16 * ( $count - 1 ) : $row ['rib'] + 14;

        $svg .= padDiagramLines ( $row ['lines'], $foot + $row ['textX'] + $offX, $first + 8 * ( $count - 1 ) - 4 + $offY + $spineY, $deep ? 'pd-fb-deep' : '', 'end' );

      }

      // The category's box at the outer end.

      $boxY = ( $s < 0 ) ? -$bone ['T'] - $bone ['boxH'] : $bone ['T'];

      $svg .= '<rect class="pd-node" x="' . $X ( $foot + $bone ['tip'] - $bone ['boxW'] / 2 ) . '" y="' . $Y ( $boxY ) . '" width="' . padChartXY ( $bone ['boxW'] )
            . '" height="' . padChartXY ( $bone ['boxH'] ) . '" rx="4"/>'
            . padDiagramLines ( $bone ['label'], $foot + $bone ['tip'] + $offX, $boxY + $bone ['boxH'] / 2 + $offY + $spineY, 'pd-fb-category' )
            . '</g>';

    }

    // The head over the end of the spine.

    $svg .= '<rect class="pd-node pd-fb-head" x="' . $X ( $headX ) . '" y="' . $Y ( -$headH / 2 ) . '" width="' . padChartXY ( $headW ) . '" height="' . padChartXY ( $headH ) . '" rx="6"/>'
          . padDiagramLines ( $head, $headX + $headW / 2 + $offX, $offY + $spineY, 'pd-fb-effect' );

    return "$svg</svg>";

  }

  // The rules of the fishbone: the spine the thickest line, the bones less, the ribs thin;
  // the head in the colours of a decision, its text and the categories' bold. The head is a
  // node of its own colour by two classes, so the .pd-node of a diagram further down the
  // page, which weighs as much as one, does not paint it over.

  function padDiagramFishboneStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-fb-spine{stroke-width:3}'
         . '.pad-chart-diagram .pd-fb-bone{stroke-width:2}'
         . '.pad-chart-diagram .pd-fb-rib{stroke-width:1}'
         . '.pad-chart-diagram .pd-node.pd-fb-head{fill:var(--pad-diagram-decision);stroke:var(--pad-diagram-decision-border);stroke-width:2}'
         . '.pad-chart-diagram .pd-fb-effect{font-size:13px;font-weight:700}'
         . '.pad-chart-diagram .pd-fb-category{font-weight:700}'
         . '.pad-chart-diagram .pd-fb-deep{font-size:11px}'
         . '</style>';

  }

?>
