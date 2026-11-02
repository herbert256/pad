<?php

  // Tablature - the tablature notation, {notation:tablature}e|--0--3--|{/notation:tablature}. A tab as players of
  // fretted instruments write it in plain text - a line a string, the highest string on top, the
  // fret to press on the string's line, a column a moment in time - drawn as a tab is printed:
  // the lines of the strings with their names, the frets on them, the bars across, the
  // techniques as arcs, lines and arrows, and every note named in words for a screen reader.
  //
  // The signs are those of the guide of the On-line Guitar Archive, Howard Wright's "The Guide
  // to Tab Notation" (1995, as classtab.org keeps it, section 3.2.2): h hammer-on, p pull-off,
  // b bend up, r release the bend, / slide up, \ slide down, v vibrato (or ~), t tap with the
  // strumming hand, x a muted, struck string; <12> a natural harmonic and (8) a ghost or grace
  // note (its section 2.1); | a bar line. A line of text over or under the lines of the strings
  // - chord names, a count of the beats, the note lengths, d and u for the strokes - is drawn in
  // its columns, as the guide writes rhythm.
  //
  // padTablatureParse     the parts of a text: each the lines of its strings, read into notes,
  //                       links between notes, bars and dots, with the text lines over and
  //                       under them - or what it cannot read
  // padTablatureTuning    the pitches of the strings, from the bottom line up: tuning=, else the
  //                       names written before the lines, else the usual tuning of an
  //                       instrument with that many strings
  // padTablatureDescribe  the tab in words, bar by bar
  // padTablatureSvg       the drawing
  //
  // A string line is its name - a note, e B G D A E - then | or : and its frets, or its name
  // and its frets straight after (E---0--), or | and its frets without a name. Any other line
  // is text.

  include_once PAD . 'subs/notations/_lib/music.php';

  const PAD_TABLATURE_COLOURS = [ 'line' => [ '#6b7280', '#9ca3af' ], 'fret' => [ '#111827', '#f3f4f6' ],
                                  'mark' => [ '#2a78d6', '#6aa8f0' ], 'text' => [ '#4b5563', '#9ca3af' ] ];

  // A tab is read up to this many bytes: a longer one is named under the strict check and cut.

  const PAD_TABLATURE_LONGEST = 20000;

  // The tunings by name, the strings from the bottom line of the tab to the top as MIDI numbers
  // (E2 is 40): the low string at the bottom for a guitar or a bass, the short fifth string of
  // a banjo and the high G of a ukulele on the bottom line, as their tabs write them.

  const PAD_TABLATURE_TUNINGS = [
    'standard' => [ 40, 45, 50, 55, 59, 64 ],          // E2 A2 D3 G3 B3 E4
    'dropd'    => [ 38, 45, 50, 55, 59, 64 ],          // D2 A2 D3 G3 B3 E4
    'dadgad'   => [ 38, 45, 50, 55, 57, 62 ],          // D2 A2 D3 G3 A3 D4
    'openg'    => [ 38, 43, 50, 55, 59, 62 ],          // D2 G2 D3 G3 B3 D4
    'opend'    => [ 38, 45, 50, 54, 57, 62 ],          // D2 A2 D3 F#3 A3 D4
    'seven'    => [ 35, 40, 45, 50, 55, 59, 64 ],      // B1 E2 A2 D3 G3 B3 E4
    'bass'     => [ 28, 33, 38, 43 ],                  // E1 A1 D2 G2
    'bass5'    => [ 23, 28, 33, 38, 43 ],              // B0 E1 A1 D2 G2
    'ukulele'  => [ 67, 60, 64, 69 ],                  // G4 C4 E4 A4, the high G
    'mandolin' => [ 55, 62, 69, 76 ],                  // G3 D4 A4 E5
    'banjo'    => [ 67, 50, 55, 59, 62 ] ];            // g4 D3 G3 B3 D4, open G

  // The tuning a tab of so many strings is read in when nothing names one.

  const PAD_TABLATURE_USUAL = [ 4 => 'bass', 5 => 'bass5', 6 => 'standard', 7 => 'seven' ];

  // The natural harmonics: the fret over a node of the string and how far above the open string
  // the harmonic sounds - the half of the string an octave, the third an octave and a fifth ...

  const PAD_TABLATURE_HARMONICS = [ 12 => 12, 7 => 19, 19 => 19, 5 => 24, 24 => 24, 4 => 28, 9 => 28, 16 => 28, 3 => 31 ];

  const PAD_TABLATURE_SIGNS = [ 'h' => 'hammer-on', 'p' => 'pull-off', 'b' => 'bend', 'r' => 'release', '/' => 'slide up', '\\' => 'slide down' ];

  // The columns are drawn 10 pixels apart, the strings 14.

  const PAD_TABLATURE_COLUMN = 10;
  const PAD_TABLATURE_STRING = 14;

  // The parts of a tab: [ parts, the first problem or '' ]. A part is the string lines that
  // stand together, each [ label, its first column, notes, links, bars, dots ], with the text
  // lines over and under it. A note is [ from, to, fret or NULL for x, flags ] - the columns it
  // takes, its flags ghost, harmonic, muted, tap, vibrato (the last column of its ~), target
  // (the fret a bend or a release goes to, drawn on the arrow, not on the line); a link is [ sign,
  // column, the note before or NULL, the note after or NULL ].

  function padTablatureParse ( $text ) {

    $text   = str_replace ( [ "\r\n", "\r" ], "\n", (string) $text );
    $blocks = [ [] ];

    foreach ( explode ( "\n", $text ) as $number => $line ) {
      $line = rtrim ( padTablatureExpand ( $line ) );
      if ( trim ( $line ) === '' )
        $blocks [] = [];
      else
        $blocks [ count ( $blocks ) - 1 ] [] = [ $number + 1, $line ];
    }

    // In a block of lines between blank lines, text over a part stands above it and text after
    // the last part under it; a block of text alone stands above the part that comes next.

    $parts = [];
    $carry = [];
    $first = '';

    foreach ( $blocks as $block ) {

      $pending = [];
      $part    = NULL;
      $had     = FALSE;
      $was     = '';

      foreach ( $block as list ( $number, $line ) ) {

        $string = padTablatureString ( $line );

        if ( $string === NULL ) {
          $pending [] = padTablatureRuns ( $line );
          $was = 'text';
          continue;
        }

        if ( $part and $was == 'text' ) {
          $parts [] = $part;
          $part     = NULL;
        }

        if ( ! $part ) {
          $part    = [ 'above' => array_merge ( $carry, $pending ), 'strings' => [], 'below' => [] ];
          $carry   = $pending = [];
          $had     = TRUE;
        }

        list ( $label, $start, $chars ) = $string;

        $read = padTablatureRead ( $chars, count ( $part ['strings'] ) + 1, $number );

        if ( $read [4] !== '' and $first === '' )
          $first = $read [4];

        $part ['strings'] [] = [ $label, $start, $read [0], $read [1], $read [2], $read [3], max ( array_keys ( $chars ) ) ];
        $was = 'string';

      }

      if ( $part ) {
        $part ['below'] = $pending;
        $parts [] = $part;
      } elseif ( ! $had )
        $carry = array_merge ( $carry, $pending );

    }

    if ( $carry and $parts )
      $parts [ count ( $parts ) - 1 ] ['below'] = array_merge ( $parts [ count ( $parts ) - 1 ] ['below'], $carry );

    if ( ! $parts )
      return [ [], $carry ? 'no string line - a string line is its name, a bar and its frets, e|--0--3--|' : '' ];

    $count = count ( $parts [0] ['strings'] );

    foreach ( $parts as $one )
      if ( count ( $one ['strings'] ) != $count and $first === '' )
        $first = 'a part of ' . count ( $one ['strings'] ) . ( count ( $one ['strings'] ) == 1 ? ' string' : ' strings' ) . " where the first has $count - the parts of a tab are of one instrument";

    return [ $parts, $first ];

  }

  // A tab character moves to the next column of eight, as a terminal shows it.

  function padTablatureExpand ( $line ) {

    if ( ! str_contains ( $line, "\t" ) )
      return $line;

    $out = '';

    foreach ( mb_str_split ( $line ) as $char )
      $out .= ( $char === "\t" ) ? str_repeat ( ' ', 8 - mb_strlen ( $out ) % 8 ) : $char;

    return $out;

  }

  // A string line as [ its label or '', the column its frets start at, its characters by
  // column ], or NULL for a line of text. A label with | or : after it makes a string line
  // whatever follows - a sign it cannot read is named; a label straight before its dashes, or a
  // line starting with them, is one only when every character is a sign of a tab.

  function padTablatureString ( $line ) {

    preg_match ( '/^\s*([A-Ga-g](?:#|b|♯|♭)?\d?)?\s*(\||:)?/u', $line, $match );

    $label = $match [1] ?? '';
    $bar   = ( $match [2] ?? '' ) !== '';
    $start = mb_strlen ( $match [0] );
    $chars = mb_str_split ( mb_substr ( $line, $start ) );

    // A string line has dashes; a line of bars and names without them - | Am | G | - is text.

    if ( ! $chars or ! in_array ( '-', $chars, TRUE ) )
      return NULL;

    if ( ! $bar ) {
      if ( $chars [0] !== '-' )
        return NULL;
      foreach ( $chars as $char )
        if ( ! str_contains ( "-0123456789hpbrHPBR/\\~vtTxX<>()|: ", $char ) )
          return NULL;
    }

    $columns = [];

    foreach ( $chars as $at => $char )
      $columns [ $start + $at ] = $char;

    return [ str_replace ( [ '♯', '♭' ], [ '#', 'b' ], $label ), $start, $columns ];

  }

  // A text line in runs of characters that are no spaces, each [ column, text ].

  function padTablatureRuns ( $line ) {

    $runs = [];

    preg_match_all ( '/\S+/u', $line, $matches, PREG_OFFSET_CAPTURE );

    foreach ( $matches [0] as list ( $run, $offset ) )
      $runs [] = [ mb_strlen ( substr ( $line, 0, $offset ) ), $run ];

    return $runs;

  }

  // The signs of a string line: [ notes, links, bars, dots, the first problem or '' ].

  function padTablatureRead ( $chars, $string, $line ) {

    $notes   = [];
    $links   = [];
    $bars    = [];
    $dots    = [];
    $problem = '';
    $tap     = NULL;
    $columns = array_keys ( $chars );
    $count   = count ( $columns );

    $fail = function ( $what, $hint = '' ) use ( &$problem, $string, $line ) {
      if ( $problem === '' )
        $problem = "$what on string $string, line $line of the tab" . ( $hint !== '' ? " - $hint" : '' );
    };

    for ( $i = 0; $i < $count; $i++ ) {

      $column = $columns [$i];
      $char   = $chars [$column];
      $lower  = strtolower ( $char );

      if ( $char === '-' or $char === ' ' )
        continue;

      if ( ctype_digit ( $char ) or $char === '(' or $char === '<' ) {

        $close = [ '(' => ')', '<' => '>' ] [$char] ?? '';
        $from  = $column;
        $j     = ( $close !== '' ) ? $i + 1 : $i;
        $digits = '';

        while ( $j < $count and ctype_digit ( $chars [ $columns [$j] ] ) )
          $digits .= $chars [ $columns [ $j++ ] ];

        if ( $close !== '' ) {
          if ( $digits === '' or $j >= $count or $chars [ $columns [$j] ] !== $close ) {
            $fail ( "a '$char' with no fret and '$close' after it" );
            continue;
          }
          $j++;
        }

        // A fret past the 24th is no fret: named, and left out of the drawing.

        if ( strlen ( $digits ) > 2 or (int) $digits > 24 ) {
          $fail ( "a fret '" . padNotationShow ( $digits ) . "'", 'the frets run from 0 to 24' );
          $i = $j - 1;
          continue;
        }

        $flags = [ 'ghost' => $char === '(', 'harmonic' => $char === '<' ];

        if ( $tap !== NULL and $tap == count ( $notes ) ) {
          $flags ['tap'] = TRUE;
          $tap = NULL;
        }

        $notes [] = [ $from, $columns [ $j - 1 ], (int) $digits, $flags ];
        $i = $j - 1;
        continue;

      }

      if ( $lower === 'x' ) {
        $notes [] = [ $column, $column, NULL, [ 'muted' => TRUE ] ];
        continue;
      }

      if ( $char === '|' ) {
        $bars [] = $column;
        continue;
      }

      if ( $char === ':' ) {
        $dots [] = $column;
        continue;
      }

      if ( $char === '~' or $lower === 'v' ) {
        $last = count ( $notes ) - 1;
        if ( $last < 0 or ( $notes [$last] [3] ['vibrato'] ?? $notes [$last] [1] ) < $column - 1 ) {
          $fail ( "a vibrato '$char' with no note before it" );
          continue;
        }
        $notes [$last] [3] ['vibrato'] = $column;
        continue;
      }

      // t taps the note it stands against: the one after it, else the one before.

      if ( $lower === 't' ) {
        $next = $columns [ $i + 1 ] ?? NULL;
        if ( $next !== NULL and ( ctype_digit ( $chars [$next] ) or $chars [$next] === '<' or $chars [$next] === '(' ) )
          $tap = count ( $notes );
        elseif ( $notes and end ( $notes ) [1] == $column - 1 )
          $notes [ count ( $notes ) - 1 ] [3] ['tap'] = TRUE;
        else
          $fail ( "a tap 't' with no fret beside it" );
        continue;
      }

      if ( isset ( PAD_TABLATURE_SIGNS [$lower] ) ) {
        $links [] = [ $lower, $column, NULL, NULL ];
        continue;
      }

      $fail ( "a sign '" . padNotationShow ( $char ) . "' the guide to tab notation has not", 'frets, h p b r / \\ v ~ t x, <12>, (8) and |' );

    }

    // Every link joins the note that ends right before it and the note that starts right after
    // it, as the guide writes them - 5h7, 7b9, 9r7, 7/9 - so /7 is a slide into the 7 and 7\ a
    // slide out of it, whatever stands further away on the string.

    $ends   = [];
    $starts = [];

    foreach ( $notes as $at => $note ) {
      $ends   [ $note [1] ] = $at;
      $starts [ $note [0] ] = $at;
    }

    foreach ( $links as $index => list ( $sign, $column ) ) {

      $before = $ends [ $column - 1 ] ?? NULL;
      $after  = $starts [ $column + 1 ] ?? NULL;

      $links [$index] [2] = $before;
      $links [$index] [3] = $after;

      $name = PAD_TABLATURE_SIGNS [$sign];
      $what = '';

      if ( in_array ( $sign, [ 'h', 'p' ] ) and ( $before === NULL or $after === NULL ) )
        $fail ( $what = "a $name '$sign' without a fret right before and after it", "5h7, 7p5" );
      elseif ( in_array ( $sign, [ 'b', 'r' ] ) and $before === NULL )
        $fail ( $what = "a $name '$sign' with no fret right before it", "7b9, 9r7" );
      elseif ( $before === NULL and $after === NULL )
        $fail ( $what = "a $name '$sign' with no fret right beside it", "5/7, /7, 7\\" );
      elseif ( ( $before !== NULL and $notes [$before] [2] === NULL ) or ( $after !== NULL and $notes [$after] [2] === NULL ) )
        $fail ( $what = "a $name '$sign' beside a muted string" );

      // A link it cannot join is left out of the drawing - the strict check named it.

      if ( $what !== '' ) {
        unset ( $links [$index] );
        continue;
      }

      if ( in_array ( $sign, [ 'b', 'r' ] ) and $after !== NULL )
        $notes [$after] [3] ['target'] = TRUE;

    }

    if ( $tap !== NULL )
      $fail ( "a tap 't' with no fret beside it" );

    return [ $notes, array_values ( $links ), $bars, $dots, $problem ];

  }

  // The pitches of the strings from the bottom line up, MIDI numbers, or NULL when none can be
  // told; or a string: what is wrong with tuning=. Notes without an octave - the names before
  // the lines, D A D G B E - take the octave nearest the usual tuning of so many strings, else
  // climb from the second octave.

  function padTablatureTuning ( $given, $labels, $count ) {

    if ( $given !== '' ) {
      $name = strtolower ( str_replace ( [ ' ', '-', '_' ], '', $given ) );
      if ( isset ( PAD_TABLATURE_TUNINGS [$name] ) )
        $tuning = PAD_TABLATURE_TUNINGS [$name];
      else {
        $tuning = padTablatureNotes ( preg_split ( '/[\s,]+/u', trim ( $given ), -1, PREG_SPLIT_NO_EMPTY ), $count );
        if ( $tuning === NULL )
          return "no tuning '" . padNotationShow ( $given ) . "' - the notes of its strings from the lowest, 'D2 A2 D3 G3 B3 E4' or 'D A D G B E', or "
               . implode ( ', ', array_keys ( PAD_TABLATURE_TUNINGS ) );
      }
      if ( count ( $tuning ) != $count )
        return "a tuning of " . count ( $tuning ) . " strings for a tab of $count";
      return $tuning;
    }

    if ( count ( array_filter ( $labels, fn ( $one ) => $one !== '' ) ) == $count and $count ) {
      $tuning = padTablatureNotes ( array_reverse ( $labels ), $count );
      if ( $tuning !== NULL )
        return $tuning;
    }

    return isset ( PAD_TABLATURE_USUAL [$count] ) ? PAD_TABLATURE_TUNINGS [ PAD_TABLATURE_USUAL [$count] ] : NULL;

  }

  // Note names, the lowest string first, as MIDI numbers: a name with its octave as it says; the
  // names of a tuning in the table without octaves that tuning; else each the octave nearest
  // the usual tuning's string, or - no usual tuning - the first above the string below it.

  function padTablatureNotes ( $names, $count ) {

    $notes = [];

    foreach ( $names as $name ) {
      $note = padMusicNote ( $name );
      if ( ! $note )
        return NULL;
      $notes [] = $note;
    }

    $classes = array_map ( fn ( $note ) => $note [0], $notes );
    $usual   = isset ( PAD_TABLATURE_USUAL [ count ( $notes ) ] ) ? PAD_TABLATURE_TUNINGS [ PAD_TABLATURE_USUAL [ count ( $notes ) ] ] : NULL;

    // Names alone are a tuning of the table when they are its strings - the usual one first -
    // or strings next to each other of one: e B G D A are the top five of a guitar.

    if ( ! array_filter ( $notes, fn ( $note ) => $note [1] !== NULL ) ) {
      $tunings = $usual ? array_merge ( [ $usual ], array_values ( PAD_TABLATURE_TUNINGS ) ) : array_values ( PAD_TABLATURE_TUNINGS );
      foreach ( [ TRUE, FALSE ] as $whole )
        foreach ( $tunings as $tuning )
          for ( $from = 0; $from + count ( $classes ) <= count ( $tuning ); $from++ ) {
            if ( $whole and count ( $tuning ) != count ( $classes ) )
              break;
            $strings = array_slice ( $tuning, $from, count ( $classes ) );
            if ( array_map ( fn ( $midi ) => $midi % 12, $strings ) === $classes )
              return $strings;
          }
    }

    $midis = [];

    foreach ( $notes as $at => list ( $class, $octave ) ) {
      if ( $octave !== NULL )
        $midi = 12 * ( $octave + 1 ) + $class;
      elseif ( $usual ) {
        $midi = $class + 12 * intdiv ( $usual [$at], 12 );
        foreach ( [ $midi - 12, $midi + 12 ] as $other )
          if ( abs ( $other - $usual [$at] ) < abs ( $midi - $usual [$at] ) )
            $midi = $other;
      } else {
        $midi = 36 + $class;
        while ( $at and $midi <= $midis [ $at - 1 ] )
          $midi += 12;
      }
      $midis [] = $midi;
    }

    return $midis;

  }

  // A pitch by its name and octave, C3 - spelt with flats when the tuning was written with them,
  // Eb Ab Db Gb Bb Eb (padTablatureFlats, set per tab), else with sharps.

  function padTablaturePitch ( $midi ) {

    return padTablatureName ( $midi ) . ( intdiv ( $midi, 12 ) - 1 );

  }

  function padTablatureName ( $midi ) {

    return padMusicName ( $midi, padTablatureFlats () ? 'B♭' : 'C' );

  }

  function padTablatureFlats ( $set = NULL ) {

    static $flats = FALSE;

    if ( $set !== NULL )
      $flats = $set;

    return $flats;

  }

  // The tab in words: the strings and their tuning, then bar by bar the notes in the order they
  // are played - notes in one column together - each with its string, its fret, its pitch and
  // how it is played.

  function padTablatureDescribe ( $parts, $tuning ) {

    $count = count ( $parts [0] ['strings'] );
    $said  = [];
    $bar   = 0;

    foreach ( $parts as $part ) {

      foreach ( $part ['above'] as $runs )
        $said [] = 'Above: ' . implode ( ' ', array_column ( $runs, 1 ) ) . '.';

      // The moments of the part: the notes struck, in column order, notes whose columns
      // overlap at one moment; and the bars, every column with a bar on any string.

      $struck = [];
      $bars   = [];

      foreach ( $part ['strings'] as $line => $string ) {
        foreach ( $string [2] as $at => $note )
          if ( empty ( $note [3] ['target'] ) )
            $struck [] = [ $note [0], $note [1], $line, $at ];
        foreach ( $string [4] as $column )
          $bars [$column] = TRUE;
      }

      usort ( $struck, fn ( $a, $b ) => [ $a [0], $a [2] ] <=> [ $b [0], $b [2] ] );
      ksort ( $bars );

      $moments = [];
      $end     = -1;

      foreach ( $struck as $one ) {
        if ( $moments and $one [0] <= $end )
          $moments [ count ( $moments ) - 1 ] [] = $one;
        else
          $moments [] = [ $one ];
        $end = max ( $end, $one [1] );
      }

      // A moment is in the bar that the bar lines between the first moment and it count to:
      // a bar line before the first note opens the first bar, one after the last closes the
      // last; the bars of the next part count on. The moments come in column order and the
      // bar lines are sorted, so the count is a pointer that only moves forward.

      $start  = $moments ? $moments [0] [0] [0] : 0;
      $bars   = array_values ( array_filter ( array_keys ( $bars ), fn ( $column ) => $column > $start ) );
      $passed = 0;
      $number = 0;
      $words  = [];

      foreach ( $moments as $moment ) {

        while ( isset ( $bars [$passed] ) and $bars [$passed] < $moment [0] [0] )
          $passed++;

        $in = 1 + $passed;

        if ( $in != $number and $words ) {
          $said [] = 'Bar ' . ( $bar + $number ) . ': ' . implode ( '; ', $words ) . '.';
          $words   = [];
        }

        $number = $in;
        $notes  = array_map ( fn ( $one ) => padTablatureNoteWords ( $part ['strings'] [ $one [2] ], $one [3], $one [2], $count, $tuning ), $moment );

        $words [] = ( count ( $notes ) > 1 ? 'together ' : '' ) . implode ( ', ', $notes );

      }

      if ( $words )
        $said [] = 'Bar ' . ( $bar + $number ) . ': ' . implode ( '; ', $words ) . '.';

      $bar += $number;

      foreach ( $part ['below'] as $runs )
        $said [] = 'Below: ' . implode ( ' ', array_column ( $runs, 1 ) ) . '.';

    }

    $strings = "$count " . ( $count == 1 ? 'string' : 'strings' );

    if ( $tuning )
      $strings .= ' tuned ' . implode ( ' ', array_map ( 'padTablaturePitch', $tuning ) ) . ' from the bottom line';

    return "$strings. " . implode ( ' ', $said );

  }

  // One note in words: string 5 fret 3 (C3), and how it is played.

  function padTablatureNoteWords ( $string, $at, $line, $count, $tuning ) {

    list ( , , $notes, $links ) = $string;

    $note  = $notes [$at];
    $flags = $note [3];
    $open  = $tuning ? $tuning [ $count - 1 - $line ] : NULL;
    $where = 'string ' . ( $line + 1 );
    $pitch = fn ( $fret ) => ( $open === NULL ) ? '' : ' (' . padTablaturePitch ( $open + $fret ) . ')';

    if ( $note [2] === NULL )
      return "$where muted";

    $fret = $note [2];

    if ( ! empty ( $flags ['harmonic'] ) )
      $text = "$where harmonic at fret $fret" . ( ( $open !== NULL and isset ( PAD_TABLATURE_HARMONICS [$fret] ) ) ? ' (' . padTablaturePitch ( $open + PAD_TABLATURE_HARMONICS [$fret] ) . ')' : '' );
    elseif ( $fret == 0 )
      $text = "$where open" . $pitch ( 0 );
    else
      $text = "$where fret $fret" . $pitch ( $fret );

    if ( ! empty ( $flags ['tap'] ) )
      $text = "tapped $text";

    $before = [];
    $after  = [];

    foreach ( $links as list ( $sign, , $from, $to ) ) {
      if ( $to === $at and $from !== NULL and in_array ( $sign, [ 'h', 'p', '/', '\\' ] ) )
        $before [] = PAD_TABLATURE_SIGNS [$sign] . ' to';
      elseif ( $to === $at and $from === NULL )
        $before [] = PAD_TABLATURE_SIGNS [$sign] . ' into';
      elseif ( $from === $at and $to === NULL and ( $sign == '/' or $sign == '\\' ) )
        $after [] = PAD_TABLATURE_SIGNS [$sign] . ' out of it';
      elseif ( $from === $at and $sign == 'b' )
        $after [] = padTablatureBendWords ( $links, $notes, $at, $open, $pitch );
      elseif ( $from === $at and $sign == 'r' )
        $after [] = 'released' . ( $to !== NULL ? ' to fret ' . $notes [$to] [2] . $pitch ( $notes [$to] [2] )
                                                 . ( isset ( $notes [$to] [3] ['vibrato'] ) ? ' with vibrato' : '' ) : '' );
    }

    if ( ! empty ( $flags ['ghost'] ) )
      $after [] = 'a ghost note';

    if ( isset ( $flags ['vibrato'] ) )
      $after [] = 'with vibrato';

    return ( $before ? implode ( ' ', $before ) . ' ' : '' ) . $text . ( $after ? ', ' . implode ( ', ', $after ) : '' );

  }

  // A bend: how far it goes - a half step, a whole step - to which pitch, and its release when
  // the fret it goes to is let back down.

  function padTablatureBendWords ( $links, $notes, $at, $open, $pitch ) {

    $to = NULL;

    foreach ( $links as list ( $sign, , $from, $target ) )
      if ( $sign == 'b' and $from === $at )
        $to = $target;

    if ( $to === NULL )
      return 'bent';

    $steps = $notes [$to] [2] - $notes [$at] [2];
    $words = 'bent up ' . padTablatureSteps ( $steps ) . ' to fret ' . $notes [$to] [2] . $pitch ( $notes [$to] [2] )
           . ( isset ( $notes [$to] [3] ['vibrato'] ) ? ' with vibrato' : '' );

    foreach ( $links as list ( $sign, , $from, $target ) )
      if ( $sign == 'r' and $from === $to )
        $words .= ' and released' . ( $target !== NULL ? ' to fret ' . $notes [$target] [2] . $pitch ( $notes [$target] [2] )
                                                       . ( isset ( $notes [$target] [3] ['vibrato'] ) ? ' with vibrato' : '' ) : '' );

    return $words;

  }

  function padTablatureSteps ( $semitones ) {

    return [ 1 => 'a half step', 2 => 'a whole step', 3 => 'a step and a half', 4 => 'two whole steps' ] [$semitones]
        ?? ( $semitones <= 0 ? 'no further' : "$semitones half steps" );

  }

  // The amount a bend is printed with over its arrow: ½, full, 1½, 2.

  function padTablatureBendLabel ( $semitones ) {

    return [ 1 => '½', 2 => 'full', 3 => '1½', 4 => '2' ] [$semitones] ?? ( $semitones > 0 ? padNotationNum ( $semitones / 2 ) : '' );

  }

  // The drawing: every part under the one before, its text lines over and under it, the names
  // of the strings to its left; every part starts at the column where the first started, as the
  // text lines them up.

  function padTablatureSvg ( $parts, $tuning, $id, $title, $desc ) {

    $column  = PAD_TABLATURE_COLUMN;
    $gap     = PAD_TABLATURE_STRING;
    $count   = count ( $parts [0] ['strings'] );
    $labels  = array_column ( $parts [0] ['strings'], 0 );
    $named   = count ( array_filter ( $labels, fn ( $one ) => $one !== '' ) ) == $count;

    if ( ! $named and $tuning )
      $labels = array_reverse ( array_map ( 'padTablatureName', $tuning ) );

    $named  = ( $named or $tuning );
    $origin = PHP_INT_MAX;
    $last   = 0;

    foreach ( $parts as $part ) {
      foreach ( $part ['strings'] as $string ) {
        $origin = min ( $origin, $string [1] );
        $last   = max ( $last, $string [6] );
      }
      foreach ( array_merge ( $part ['above'], $part ['below'] ) as $runs )
        foreach ( $runs as list ( $at, $run ) )
          $last = max ( $last, $at + mb_strlen ( $run ) - 1 );
    }

    $left  = $named ? 22 : 8;
    $x     = fn ( $at ) => $left + ( $at - $origin + 0.5 ) * $column;
    $svg   = '';
    $y     = 4;

    foreach ( $parts as $part ) {

      foreach ( $part ['above'] as $runs ) {
        $y   += 14;
        $svg .= padTablatureText ( $runs, $x, $y );
      }

      $top = $y + 22;
      $end = max ( array_column ( $part ['strings'], 6 ) );

      if ( $named )
        foreach ( $labels as $line => $label )
          $svg .= '<text class="ptb-name" x="10" y="' . padNotationNum ( $top + $line * $gap + 3.5 ) . '" text-anchor="middle">' . padNotationAttr ( $label ) . '</text>';

      // The lines of the strings, broken where a fret is written on them.

      foreach ( $part ['strings'] as $line => $string ) {
        $ys    = $top + $line * $gap;
        $from  = $x ( $origin ) - $column / 2;
        $path  = '';
        foreach ( $string [2] as $note ) {
          if ( ! empty ( $note [3] ['target'] ) )
            continue;
          $centre = ( $x ( $note [0] ) + $x ( $note [1] ) ) / 2;
          $half   = ( $note [1] - $note [0] + 1 ) * $column / 2 + 0.5;
          if ( $centre - $half > $from )
            $path .= 'M' . padNotationNum ( $from ) . ' ' . padNotationNum ( $ys ) . 'H' . padNotationNum ( $centre - $half );
          $from = max ( $from, $centre + $half );
        }
        if ( $x ( $end ) + $column / 2 > $from )
          $path .= 'M' . padNotationNum ( $from ) . ' ' . padNotationNum ( $ys ) . 'H' . padNotationNum ( $x ( $end ) + $column / 2 );
        if ( $path !== '' )
          $svg .= '<path class="ptb-string" d="' . $path . '"/>';
      }

      // The bars: a column with a bar on every string a line across; one on some strings
      // only - a tab whose lines do not line up - is drawn over those alone.

      $bars = [];

      foreach ( $part ['strings'] as $line => $string )
        foreach ( $string [4] as $at )
          $bars [$at] [] = $line;

      ksort ( $bars );

      foreach ( $bars as $at => $lines ) {
        $runs = padTablatureRunsOf ( $lines );
        foreach ( $runs as list ( $low, $high ) )
          $svg .= '<path class="ptb-bar" d="M' . padNotationNum ( $x ( $at ) ) . ' ' . padNotationNum ( $top + $low * $gap - ( $low == $high ? 3 : 0 ) )
                . 'V' . padNotationNum ( $top + $high * $gap + ( $low == $high ? 3 : 0 ) ) . '"/>';
      }

      foreach ( $part ['strings'] as $line => $string )
        foreach ( $string [5] as $at )
          if ( $line < $count - 1 )
            $svg .= '<circle class="ptb-dot" cx="' . padNotationNum ( $x ( $at ) ) . '" cy="' . padNotationNum ( $top + ( $line + 0.5 ) * $gap ) . '" r="1.6"/>';

      foreach ( $part ['strings'] as $line => $string )
        $svg .= padTablatureLine ( $string, $top + $line * $gap, $top, $x );

      $y = $top + ( count ( $part ['strings'] ) - 1 ) * $gap + 8;

      foreach ( $part ['below'] as $runs ) {
        $y   += 14;
        $svg .= padTablatureText ( $runs, $x, $y );
      }

      $y += 10;

    }

    $width  = (int) ceil ( $x ( $last ) + $column );
    $height = (int) ceil ( $y );

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-tablature" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

  // Lines of strings next to each other as runs: [ 0, 1, 2, 4 ] is 0 to 2 and 4 to 4.

  function padTablatureRunsOf ( $lines ) {

    $runs = [];

    foreach ( $lines as $line )
      if ( $runs and $runs [ count ( $runs ) - 1 ] [1] == $line - 1 )
        $runs [ count ( $runs ) - 1 ] [1] = $line;
      else
        $runs [] = [ $line, $line ];

    return $runs;

  }

  // A text line in its columns: each run of characters stretched over the columns it takes.

  function padTablatureText ( $runs, $x, $y ) {

    $svg = '';

    foreach ( $runs as list ( $at, $run ) ) {
      $length = mb_strlen ( $run );
      $svg   .= '<text class="ptb-text" x="' . padNotationNum ( $x ( $at ) - PAD_TABLATURE_COLUMN / 2 ) . '" y="' . padNotationNum ( $y ) . '"'
              . ( $length > 1 ? ' textLength="' . ( $length * PAD_TABLATURE_COLUMN ) . '" lengthAdjust="spacingAndGlyphs"' : '' ) . '>'
              . padNotationAttr ( $run ) . '</text>';
    }

    return $svg;

  }

  // The notes of one string and what links them: the frets on the line, an arc with h or p
  // from a note to the next, a slanted line for a slide, an arrow up for a bend with how far
  // it goes and down for its release with the fret it comes back to, a wave for vibrato, a T
  // over a tapped note.

  function padTablatureLine ( $string, $ys, $top, $x ) {

    list ( , , $notes, $links ) = $string;

    $svg    = '';
    $centre = fn ( $note ) => ( $x ( $note [0] ) + $x ( $note [1] ) ) / 2;
    $half   = fn ( $note ) => ( $note [1] - $note [0] + 1 ) * PAD_TABLATURE_COLUMN / 2;
    $num    = 'padNotationNum';

    foreach ( $notes as $note ) {

      $flags = $note [3];

      // Vibrato waves after its note - after the fret a bend went to as well, 7b9~~.

      if ( isset ( $flags ['vibrato'] ) ) {
        $from = $centre ( $note ) + $half ( $note ) + 1;
        $to   = $x ( $flags ['vibrato'] ) + PAD_TABLATURE_COLUMN / 2;
        $path = 'M' . $num ( $from ) . ' ' . $num ( $ys - 1 );
        for ( $at = $from, $up = TRUE; $at + 2 <= $to; $at += 2, $up = ! $up )
          $path .= 'l2 ' . ( $up ? '-2' : '2' );
        $svg .= '<path class="ptb-wave" d="' . $path . '"/>';
      }

      if ( ! empty ( $flags ['target'] ) )
        continue;

      $text  = ( $note [2] === NULL ) ? '×' : (string) $note [2];

      if ( ! empty ( $flags ['ghost'] ) )
        $text = "($text)";
      elseif ( ! empty ( $flags ['harmonic'] ) )
        $text = "<$text>";

      $svg .= '<text class="ptb-fret' . ( ! empty ( $flags ['ghost'] ) ? ' ptb-ghost' : '' ) . '" x="' . $num ( $centre ( $note ) ) . '" y="' . $num ( $ys + 3.5 ) . '" text-anchor="middle">'
            . padNotationAttr ( $text ) . '</text>';

      if ( ! empty ( $flags ['tap'] ) )
        $svg .= '<text class="ptb-sign" x="' . $num ( $centre ( $note ) ) . '" y="' . $num ( $ys - 7 ) . '" text-anchor="middle">T</text>';

    }

    foreach ( $links as list ( $sign, $column, $from, $to ) ) {

      $a = ( $from !== NULL ) ? $notes [$from] : NULL;
      $b = ( $to   !== NULL ) ? $notes [$to]   : NULL;

      if ( $sign == 'h' or $sign == 'p' ) {
        $x1   = $centre ( $a );
        $x2   = $centre ( $b );
        $svg .= '<path class="ptb-arc" d="M' . $num ( $x1 ) . ' ' . $num ( $ys - 6 ) . 'Q' . $num ( ( $x1 + $x2 ) / 2 ) . ' ' . $num ( $ys - 14 ) . ' ' . $num ( $x2 ) . ' ' . $num ( $ys - 6 ) . '"/>'
              . '<text class="ptb-sign" x="' . $num ( ( $x1 + $x2 ) / 2 ) . '" y="' . $num ( $ys - 12 ) . '" text-anchor="middle">' . $sign . '</text>';
        continue;
      }

      if ( $sign == '/' or $sign == '\\' ) {
        $x1   = $a ? $centre ( $a ) + $half ( $a ) + 1 : $x ( $column ) - PAD_TABLATURE_COLUMN / 2;
        $x2   = $b ? $centre ( $b ) - $half ( $b ) - 1 : $x ( $column ) + PAD_TABLATURE_COLUMN / 2;
        $rise = ( $sign == '/' ) ? -1 : 1;
        $svg .= '<path class="ptb-slide" d="M' . $num ( $x1 ) . ' ' . $num ( $ys - 3 * $rise ) . 'L' . $num ( $x2 ) . ' ' . $num ( $ys + 3 * $rise ) . '"/>';
        continue;
      }

      // A bend rises from its note to over the top string at the fret it goes to; a release
      // comes down from there to the column of the fret it goes back to.

      $x1   = $centre ( $a ) + $half ( $a ) + 1;
      $peak = $top - 8;

      if ( $sign == 'b' ) {
        $x2    = $b ? $centre ( $b ) : $x ( $column ) + PAD_TABLATURE_COLUMN / 2;
        $label = $b ? padTablatureBendLabel ( $b [2] - $a [2] ) : '';
        $svg  .= '<path class="ptb-arrow" d="M' . $num ( $x1 ) . ' ' . $num ( $ys ) . 'Q' . $num ( $x2 ) . ' ' . $num ( $ys ) . ' ' . $num ( $x2 ) . ' ' . $num ( $peak + 2 ) . '"/>'
               . '<path class="ptb-head" d="M' . $num ( $x2 - 2.5 ) . ' ' . $num ( $peak + 4 ) . 'L' . $num ( $x2 ) . ' ' . $num ( $peak ) . 'L' . $num ( $x2 + 2.5 ) . ' ' . $num ( $peak + 4 ) . 'Z"/>';
        if ( $label !== '' )
          $svg .= '<text class="ptb-sign" x="' . $num ( $x2 ) . '" y="' . $num ( $peak - 3 ) . '" text-anchor="middle">' . padNotationAttr ( $label ) . '</text>';
        continue;
      }

      $start = empty ( $a [3] ['target'] ) ? $x1 : $centre ( $a );
      $x2    = $b ? $centre ( $b ) : $x ( $column ) + PAD_TABLATURE_COLUMN / 2;
      $svg  .= '<path class="ptb-arrow" d="M' . $num ( $start ) . ' ' . $num ( $peak ) . 'Q' . $num ( $x2 ) . ' ' . $num ( $peak ) . ' ' . $num ( $x2 ) . ' ' . $num ( $ys - 6 ) . '"/>'
             . '<path class="ptb-head" d="M' . $num ( $x2 - 2.5 ) . ' ' . $num ( $ys - 8 ) . 'L' . $num ( $x2 ) . ' ' . $num ( $ys - 4 ) . 'L' . $num ( $x2 + 2.5 ) . ' ' . $num ( $ys - 8 ) . 'Z"/>';
      if ( $b )
        $svg .= '<text class="ptb-sign" x="' . $num ( $x2 + 4 ) . '" y="' . $num ( $peak - 3 ) . '">(' . $b [2] . ')</text>';

    }

    return $svg;

  }

?>
