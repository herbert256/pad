<?php

  // Differences between two texts - the {diff} tag. What was taken out is a <del>, what came
  // in an <ins>, both escaped; word by word, or line by line with numbers, in one column or
  // side by side.
  //
  //   {diff $old, $new}                      the words, in the flow of the text
  //   {diff $old, $new, lines}               the lines, one column with both line numbers
  //   {diff $old, $new, side, from='v1', to='v2'}   the lines side by side
  //
  // padDiffOps     the edit script of two lists: [ '=', '-' or '+', item ] in order - Myers'
  //                O(ND) algorithm, the shortest script, as git diff finds it
  // padDiffWords   the word diff as HTML
  // padDiffLines   the line diff as a table, one column or side by side
  //
  // In a run of changes the deletions come before the insertions, so a changed phrase
  // reads as the old one struck and the new one after it; the space between two changed
  // words joins the change instead of splitting it in two. A changed line that has a
  // counterpart on the other side marks the words that changed within it. A screen reader
  // hears where a deletion and an insertion start and end - the words are in the markup,
  // hidden from the eye.

  function padDiffOps ( $a, $b ) {

    $n     = count ( $a );
    $m     = count ( $b );
    $max   = $n + $m;
    $v     = [ 1 => 0 ];
    $trace = [];
    $found = FALSE;

    for ( $d = 0; $d <= $max and ! $found; $d++ ) {

      $trace [] = $v;

      for ( $k = -$d; $k <= $d; $k += 2 ) {

        $x = ( $k == -$d or ( $k != $d and $v [$k - 1] < $v [$k + 1] ) ) ? $v [$k + 1] : $v [$k - 1] + 1;
        $y = $x - $k;

        while ( $x < $n and $y < $m and $a [$x] === $b [$y] ) {
          $x++;
          $y++;
        }

        $v [$k] = $x;

        if ( $x >= $n and $y >= $m ) {
          $found = TRUE;
          break;
        }

      }

    }

    // Back from the end through the snapshots: each step one deletion or insertion, the
    // equal items of its snake after it.

    $ops = [];
    $x   = $n;
    $y   = $m;

    for ( $d = count ( $trace ) - 1; $d >= 0; $d-- ) {

      $v     = $trace [$d];
      $k     = $x - $y;
      $prevK = ( $k == -$d or ( $k != $d and $v [$k - 1] < $v [$k + 1] ) ) ? $k + 1 : $k - 1;
      $prevX = $v [$prevK];
      $prevY = $prevX - $prevK;

      while ( $x > $prevX and $y > $prevY )
        $ops [] = [ '=', $a [--$x], $b [--$y] ];

      if ( $d > 0 ) {
        if ( $x == $prevX )
          $ops [] = [ '+', $b [--$y] ];
        else
          $ops [] = [ '-', $a [--$x] ];
      }

    }

    return padDiffGroup ( array_reverse ( $ops ) );

  }

  // The deletions of a run of changes before its insertions.

  function padDiffGroup ( $ops ) {

    $out = $del = $ins = [];

    foreach ( $ops as $op ) {
      if ( $op [0] == '-' )
        $del [] = $op;
      elseif ( $op [0] == '+' )
        $ins [] = $op;
      else {
        array_push ( $out, ...$del, ...$ins );
        $out [] = $op;
        $del    = $ins = [];
      }
    }

    return array_merge ( $out, $del, $ins );

  }

  function padDiffEscape ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The words of a text: a word, a run of white space or one other character each.

  function padDiffTokens ( $text ) {

    preg_match_all ( '/\s+|[\p{L}\p{N}_]+|./su', (string) $text, $m );

    return $m [0];

  }

  // The word diff of two texts as inline HTML: equal text as it is, a run of changes as
  // one <del> and one <ins>. White space between two changes belongs to both.

  function padDiffMarkup ( $old, $new ) {

    $ops  = padDiffOps ( padDiffTokens ( $old ), padDiffTokens ( $new ) );
    $html = $del = $ins = '';
    $hold = '';

    $flush = function () use ( &$html, &$del, &$ins ) {
      if ( $del !== '' ) $html .= '<del>' . padDiffEscape ( $del ) . '</del>';
      if ( $ins !== '' ) $html .= '<ins>' . padDiffEscape ( $ins ) . '</ins>';
      $del = $ins = '';
    };

    foreach ( $ops as $i => $op ) {

      if ( $op [0] == '=' ) {

        $next = $ops [$i + 1] [0] ?? '=';

        if ( $del !== '' and $ins !== '' and $next != '=' and trim ( $op [1] ) === '' ) {
          $del .= $op [1];
          $ins .= $op [2];
          continue;
        }

        $flush ();
        $html .= padDiffEscape ( $op [1] );

      } elseif ( $op [0] == '-' )
        $del .= $op [1];
      else
        $ins .= $op [1];

    }

    $flush ();

    return $html;

  }

  function padDiffWords ( $old, $new ) {

    return padDiffStyle () . '<div class="pad-diff pad-diff-words">' . padDiffMarkup ( $old, $new ) . '</div>';

  }

  // The line diff. Each run of changes pairs its deleted and inserted lines one to one;
  // a pair marks its changed words, a line without a counterpart is struck or added
  // whole. One column: old number, new number, sign, text. Side by side: old number and
  // text, new number and text, a pair on one row.

  function padDiffLines ( $old, $new, $side, $from, $to ) {

    $split = fn ( $t ) => $t === '' ? [] : preg_split ( '/\R/', rtrim ( $t, "\r\n" ) );
    $ops   = padDiffOps ( $split ( (string) $old ), $split ( (string) $new ) );
    $rows  = [];
    $del   = $ins = [];
    $a     = $b   = 0;

    $ops [] = [ 'end' ];

    foreach ( $ops as $op ) {

      if ( $op [0] == '-' ) { $del [] = [ ++$a, $op [1] ]; continue; }
      if ( $op [0] == '+' ) { $ins [] = [ ++$b, $op [1] ]; continue; }

      for ( $i = 0; $i < max ( count ( $del ), count ( $ins ) ); $i++ )
        $rows [] = [ 'change', $del [$i] ?? NULL, $ins [$i] ?? NULL ];

      $del = $ins = [];

      if ( $op [0] == '=' )
        $rows [] = [ 'same', [ ++$a, $op [1] ], [ ++$b, $op [2] ] ];

    }

    $caption = ( $from !== '' or $to !== '' );
    $html    = padDiffStyle () . '<table class="pad-diff pad-diff-lines' . ( $side ? ' pad-diff-side' : '' ) . '">';

    if ( $caption and $side )
      $html .= '<thead><tr><th scope="col" colspan="2">' . padDiffEscape ( $from ) . '</th><th scope="col" colspan="2">' . padDiffEscape ( $to ) . '</th></tr></thead>';
    elseif ( $caption )
      $html .= '<caption>' . padDiffEscape ( $from ) . ( $from !== '' && $to !== '' ? ' → ' : '' ) . padDiffEscape ( $to ) . '</caption>';

    $html .= '<tbody>';

    foreach ( $rows as list ( $kind, $left, $right ) ) {

      if ( $kind == 'same' ) {
        $text  = padDiffEscape ( $left [1] );
        $html .= $side ? "<tr><td class=\"pdf-num\">{$left [0]}</td><td class=\"pdf-text\">$text</td><td class=\"pdf-num\">{$right [0]}</td><td class=\"pdf-text\">$text</td></tr>"
                       : "<tr><td class=\"pdf-num\">{$left [0]}</td><td class=\"pdf-num\">{$right [0]}</td><td class=\"pdf-sign\"> </td><td class=\"pdf-text\">$text</td></tr>";
        continue;
      }

      // A pair: the words of each side, the old one without its insertions, the new one
      // without its deletions.

      if ( $left and $right ) {
        $both     = padDiffMarkup ( $left [1], $right [1] );
        $oldText  = preg_replace ( '#<ins>.*?</ins>#s', '', $both );
        $newText  = preg_replace ( '#<del>.*?</del>#s', '', $both );
      } else {
        $oldText = $left  ? '<del class="pdf-whole">' . padDiffEscape ( $left  [1] ) . '</del>' : '';
        $newText = $right ? '<ins class="pdf-whole">' . padDiffEscape ( $right [1] ) . '</ins>' : '';
      }

      if ( $side )
        $html .= '<tr>'
               . ( $left  ? "<td class=\"pdf-num pdf-del\">{$left [0]}</td><td class=\"pdf-text pdf-del\">$oldText</td>"  : '<td class="pdf-num pdf-none"></td><td class="pdf-text pdf-none"></td>' )
               . ( $right ? "<td class=\"pdf-num pdf-ins\">{$right [0]}</td><td class=\"pdf-text pdf-ins\">$newText</td>" : '<td class="pdf-num pdf-none"></td><td class="pdf-text pdf-none"></td>' )
               . '</tr>';
      else {
        if ( $left )
          $html .= "<tr class=\"pdf-del\"><td class=\"pdf-num\">{$left [0]}</td><td class=\"pdf-num\"></td><td class=\"pdf-sign\">-</td><td class=\"pdf-text\">$oldText</td></tr>";
        if ( $right )
          $html .= "<tr class=\"pdf-ins\"><td class=\"pdf-num\"></td><td class=\"pdf-num\">{$right [0]}</td><td class=\"pdf-sign\">+</td><td class=\"pdf-text\">$newText</td></tr>";
      }

    }

    return "$html</tbody></table>";

  }

  // The look, once per page: the struck and the added colours as custom properties with
  // light-dark() defaults, the words a screen reader hears at the edges of a change.

  function padDiffStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $roles = [ 'del'      => [ '#fbe1e1', '#4a1f22' ], 'del-mark' => [ '#f3b4b4', '#7a2e33' ],
               'ins'      => [ '#dcf5e6', '#163b27' ], 'ins-mark' => [ '#a8e2bf', '#1f6340' ],
               'text'     => [ '#1a1a19', '#e8e7e1' ], 'muted'    => [ '#898781', '#898781' ],
               'line'     => [ '#e4e3df', '#3a3a37' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-diff-$role:$day;";
      $both  .= "--pad-diff-$role:light-dark($day,$night);";
    }

    $hidden = 'position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%);white-space:nowrap';

    return '<style>'
         . ":where(.pad-diff){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-diff){{$both}}}"
         . ':where(.pad-diff){color:var(--pad-diff-text)}'
         . ':where(.pad-diff-words){white-space:pre-wrap;line-height:1.7}'
         . ':where(.pad-diff del){background:var(--pad-diff-del-mark);text-decoration:line-through;text-decoration-color:rgba(160,30,30,.6);border-radius:3px;padding:1px 2px}'
         . ':where(.pad-diff ins){background:var(--pad-diff-ins-mark);text-decoration:none;border-radius:3px;padding:1px 2px}'
         . '.pad-diff del::before,.pad-diff del::after,.pad-diff ins::before,.pad-diff ins::after{' . $hidden . '}'
         . '.pad-diff del::before{content:" [deletion start] "}.pad-diff del::after{content:" [deletion end] "}'
         . '.pad-diff ins::before{content:" [insertion start] "}.pad-diff ins::after{content:" [insertion end] "}'
         . ':where(.pad-diff-lines){border-collapse:collapse;width:100%;font:13px/1.55 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;table-layout:auto}'
         . ':where(.pad-diff-lines caption,.pad-diff-lines th){padding:4px 8px;text-align:left;font:600 13px system-ui,sans-serif;border-bottom:1px solid var(--pad-diff-line)}'
         . ':where(.pad-diff-lines td){padding:1px 8px;vertical-align:top}'
         . ':where(.pad-diff-lines .pdf-num){width:1%;text-align:right;color:var(--pad-diff-muted);user-select:none;white-space:nowrap}'
         . ':where(.pad-diff-lines .pdf-sign){width:1%;color:var(--pad-diff-muted);user-select:none;padding:1px 2px}'
         . ':where(.pad-diff-lines .pdf-text){white-space:pre-wrap;word-break:break-word}'
         . ':where(.pad-diff-lines tr.pdf-del,.pad-diff-lines td.pdf-del){background:var(--pad-diff-del)}'
         . ':where(.pad-diff-lines tr.pdf-ins,.pad-diff-lines td.pdf-ins){background:var(--pad-diff-ins)}'
         . ':where(.pad-diff-lines td.pdf-none){background:repeating-linear-gradient(-45deg,transparent 0 4px,var(--pad-diff-line) 4px 5px)}'
         . ':where(.pad-diff-side .pdf-text){width:49%}'
         . ':where(.pad-diff-side td:nth-child(3)){border-left:1px solid var(--pad-diff-line)}'
         . ':where(.pad-diff .pdf-whole){background:none;text-decoration:none}'
         . '</style>';

  }

?>
