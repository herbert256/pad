<?php

  // Chord boxes - the chord notation, {notation:chord 'Am'/}. A chord of a guitar or a ukulele
  // as the box a chord chart draws: the strings standing up, the frets across, a dot where a
  // finger presses, a bar where one finger presses several strings, o over a string played
  // open and × over one not played, the chord's name above and the note of every string below.
  //
  // padChordShape     the frets of a chord on an instrument: the shape a player learns first
  //                   (the tables below), else one found (padChordSearch)
  // padChordSearch    a playable shape of a chord's notes: every note of the chord - the fifth
  //                   may go when there are four or more - in a hand's span of four frets, no
  //                   string left out between two that are played, on a guitar the root (or
  //                   the bass of a slash chord) the lowest note
  // padChordFrets     the frets written out - 'x32010', or '8 10 10 9 8 8' for frets past 9
  // padChordName      the chord a set of frets plays, named when its notes are one
  // padChordSvg       the box
  //
  // The instruments are tuned as they mostly are: a guitar E A D G B E (E2 to E4), a ukulele
  // G C E A with its high G (G4 C4 E4 A4) - the strings low to high as a chart lists them.

  include_once PAD . 'subs/notations/_lib/music.php';

  const PAD_CHORD_TUNINGS = [
    'guitar'  => [ [ 40, 45, 50, 55, 59, 64 ], [ 'low E', 'A', 'D', 'G', 'B', 'high E' ] ],
    'ukulele' => [ [ 67, 60, 64, 69 ],         [ 'G', 'C', 'E', 'A' ] ] ];

  // The shapes of the common chords, by root (spelt with sharps) and quality, the strings low
  // to high: the open chords and the barre chords of every chart. A chord not here is searched.

  const PAD_CHORD_SHAPES = [
    'guitar' => [
      'C' => 'x32010', 'C#' => 'x46664', 'D' => 'xx0232', 'D#' => 'x68886', 'E' => '022100', 'F' => '133211',
      'F#' => '244322', 'G' => '320003', 'G#' => '466544', 'A' => 'x02220', 'A#' => 'x13331', 'B' => 'x24442',
      'Cm' => 'x35543', 'C#m' => 'x46654', 'Dm' => 'xx0231', 'D#m' => 'x68876', 'Em' => '022000', 'Fm' => '133111',
      'F#m' => '244222', 'Gm' => '355333', 'G#m' => '466444', 'Am' => 'x02210', 'A#m' => 'x13321', 'Bm' => 'x24432',
      'C7' => 'x32310', 'C#7' => 'x46464', 'D7' => 'xx0212', 'D#7' => 'x68686', 'E7' => '020100', 'F7' => '131211',
      'F#7' => '242322', 'G7' => '320001', 'G#7' => '464544', 'A7' => 'x02020', 'A#7' => 'x13131', 'B7' => 'x21202',
      'Cm7' => 'x35343', 'C#m7' => 'x46454', 'Dm7' => 'xx0211', 'D#m7' => 'x68676', 'Em7' => '020000', 'Fm7' => '131111',
      'F#m7' => '242222', 'Gm7' => '353333', 'G#m7' => '464444', 'Am7' => 'x02010', 'A#m7' => 'x13121', 'Bm7' => 'x20202',
      'Cmaj7' => 'x32000', 'C#maj7' => 'x46564', 'Dmaj7' => 'xx0222', 'D#maj7' => 'x68786', 'Emaj7' => '021100',
      'Fmaj7' => 'xx3210', 'F#maj7' => '243322', 'Gmaj7' => '320002', 'G#maj7' => '465544', 'Amaj7' => 'x02120',
      'A#maj7' => 'x13231', 'Bmaj7' => 'x24342',
      'Csus4' => 'x33011', 'Dsus4' => 'xx0233', 'Esus4' => '022200', 'Fsus4' => 'xx3311', 'Gsus4' => '330013', 'Asus4' => 'x02230',
      'Csus2' => 'x30033', 'Dsus2' => 'xx0230', 'Esus2' => '024400', 'Fsus2' => 'xx3011', 'Gsus2' => '300033', 'Asus2' => 'x02200',
      'C6' => 'x32210', 'D6' => 'xx0202', 'E6' => '022120', 'G6' => '320000', 'A6' => 'x02222',
      'Dm6' => 'xx0201', 'Em6' => '022020', 'Am6' => 'x02212',
      'Cadd9' => 'x32030', 'Eadd9' => '024100', 'Gadd9' => '320203',
      'C9' => 'x32333', 'E9' => '020102', 'A9' => 'x02423',
      'Caug' => 'x32110', 'Eaug' => '032110', 'Gaug' => '321003', 'Aaug' => 'x03221',
      'Ddim' => 'xx0131', 'Bdim' => 'x2343x', 'Bm7b5' => 'x2323x',
      'A7sus4' => 'x02030', 'D7sus4' => 'xx0213', 'E7sus4' => '020200', 'G7sus4' => '330011',
      'Ammaj7' => 'x02110', 'Emmaj7' => '021000',
      'C5' => 'x355xx', 'D5' => 'xx023x', 'E5' => '022xxx', 'F5' => '133xxx', 'G5' => '355xxx', 'A5' => 'x022xx', 'B5' => 'x244xx' ],
    'ukulele' => [
      'C' => '0003', 'C#' => '1114', 'D' => '2220', 'D#' => '0331', 'E' => '1402', 'F' => '2010',
      'F#' => '3121', 'G' => '0232', 'G#' => '5343', 'A' => '2100', 'A#' => '3211', 'B' => '4322',
      'Cm' => '0333', 'C#m' => '1104', 'Dm' => '2210', 'D#m' => '3321', 'Em' => '0432', 'Fm' => '1013',
      'F#m' => '2120', 'Gm' => '0231', 'G#m' => '4342', 'Am' => '2000', 'A#m' => '3111', 'Bm' => '4222',
      'C7' => '0001', 'C#7' => '1112', 'D7' => '2223', 'D#7' => '3334', 'E7' => '1202', 'F7' => '2313',
      'F#7' => '3424', 'G7' => '0212', 'G#7' => '1323', 'A7' => '0100', 'A#7' => '1211', 'B7' => '2322',
      'Cm7' => '3333', 'Dm7' => '2213', 'Em7' => '0202', 'Fm7' => '1313', 'F#m7' => '2424', 'Gm7' => '0211',
      'Am7' => '0000', 'A#m7' => '1111', 'Bm7' => '2222',
      'Cmaj7' => '0002', 'Dmaj7' => '2224', 'Emaj7' => '1302', 'Fmaj7' => '2413', 'Gmaj7' => '0222',
      'Amaj7' => '1100', 'A#maj7' => '3210', 'Bmaj7' => '3322',
      'Csus4' => '0013', 'Dsus4' => '0230', 'Gsus4' => '0233', 'Asus4' => '2200', 'Esus4' => '4400',
      'Csus2' => '0233', 'Dsus2' => '2200', 'Gsus2' => '0230', 'Asus2' => '2402',
      'C6' => '0000', 'F6' => '2213', 'G6' => '0202' ] ];

  const PAD_CHORD_COLOURS = [ 'line' => [ '#374151', '#d1d5db' ], 'dot' => [ '#1f2937', '#f3f4f6' ],
                              'text' => [ '#4b5563', '#9ca3af' ], 'inverse' => [ '#ffffff', '#111827' ] ];

  // The frets written out: a character a string - 0 to 9, x for a string not played - or,
  // for frets past 9, the frets apart by spaces, commas or hyphens. NULL when it is neither or
  // the count is not the instrument's.

  function padChordFrets ( $text, $strings ) {

    $text = strtolower ( trim ( (string) $text ) );

    $parts = preg_match ( '/[\s,\-]/', $text ) ? preg_split ( '/[\s,\-]+/', $text ) : str_split ( $text );

    if ( count ( $parts ) != $strings )
      return NULL;

    $frets = [];

    foreach ( $parts as $part )
      if ( $part === 'x' )
        $frets [] = NULL;
      elseif ( ctype_digit ( $part ) and (int) $part <= 24 )
        $frets [] = (int) $part;
      else
        return NULL;

    return array_filter ( $frets, fn ( $fret ) => $fret !== NULL ) ? $frets : NULL;

  }

  // The shape of a chord - [ root, intervals, bass, name, words, root as spelt ] of
  // padMusicChord - on an instrument.

  function padChordShape ( $chord, $instrument ) {

    list ( $root, $intervals, $bass ) = $chord;

    $quality = array_search ( $intervals, array_map ( fn ( $one ) => $one [0], PAD_MUSIC_QUALITIES ), TRUE );
    $key     = [ 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B' ] [$root] . $quality;

    if ( $bass === NULL and isset ( PAD_CHORD_SHAPES [$instrument] [$key] ) )
      return padChordFrets ( PAD_CHORD_SHAPES [$instrument] [$key], count ( PAD_CHORD_TUNINGS [$instrument] [0] ) );

    return padChordSearch ( PAD_CHORD_TUNINGS [$instrument] [0], $root, $intervals, $bass, $instrument == 'guitar' );

  }

  // A shape found: for each position of the hand - its first fret 0 to 9 - every way of
  // playing the strings with notes of the chord within four frets, scored: every string
  // played counts, every note of the chord, a hand low on the neck, few fingers. The best
  // scored is the answer, the first in fret order when two score alike.

  function padChordSearch ( $tuning, $root, $intervals, $bass, $rootLowest ) {

    $tones    = array_map ( fn ( $one ) => ( $root + $one ) % 12, $intervals );
    $required = ( count ( $tones ) >= 4 ) ? array_values ( array_filter ( $tones, fn ( $one ) => $one != ( $root + 7 ) % 12 ) ) : $tones;
    $lowest   = $bass ?? $root;

    if ( $bass !== NULL and ! in_array ( $bass, $tones ) )
      $tones [] = $bass;

    $best  = NULL;
    $score = PHP_INT_MIN;
    $count = count ( $tuning );

    for ( $base = 0; $base <= 9; $base++ ) {

      $window = ( $base == 0 ) ? [ 0, 1, 2, 3 ] : range ( $base, $base + 3 );
      $choice = [];

      foreach ( $tuning as $string => $open ) {
        $choice [$string] = [ NULL ];
        foreach ( $window as $fret )
          if ( in_array ( ( $open + $fret ) % 12, $tones ) )
            $choice [$string] [] = $fret;
      }

      // Every combination, strings low to high: once a string is left out after a played one,
      // the strings above it are left out too.

      $walk = function ( $string, $frets, $closed ) use ( &$walk, &$best, &$score, $choice, $count, $tuning, $tones, $required, $lowest, $rootLowest, $base ) {

        if ( $string == $count ) {

          $played = array_filter ( $frets, fn ( $fret ) => $fret !== NULL );

          if ( count ( $played ) < min ( 3, $count ) )
            return;

          $notes = [];
          foreach ( $played as $at => $fret )
            $notes [] = ( $tuning [$at] + $fret ) % 12;

          foreach ( $required as $tone )
            if ( ! in_array ( $tone, $notes ) )
              return;

          if ( $rootLowest and $notes [0] != $lowest )
            return;

          $pressed = array_filter ( $played, fn ( $fret ) => $fret > 0 );

          if ( $pressed and max ( $pressed ) - min ( $pressed ) > 3 )
            return;

          $fingers = count ( $pressed );

          if ( $pressed ) {
            $low   = min ( $pressed );
            $under = array_keys ( $played, $low, TRUE );
            if ( count ( $under ) >= 2 and ! array_filter ( range ( min ( $under ), max ( $under ) ), fn ( $at ) => ( $frets [$at] ?? 0 ) < $low ) )
              $fingers -= count ( $under ) - 1;
          }

          if ( $fingers > 4 )
            return;

          $value = count ( $played ) * 10 + count ( array_unique ( $notes ) ) * 4 - $fingers - $base * 3;

          if ( $value > $score or ( $value == $score and padChordOrder ( $frets ) < padChordOrder ( $best ) ) ) {
            $score = $value;
            $best  = $frets;
          }

          return;

        }

        foreach ( $choice [$string] as $fret ) {
          if ( $closed and $fret !== NULL )
            continue;
          $next   = $frets;
          $next [] = $fret;
          $walk ( $string + 1, $next, $closed or ( $fret === NULL and array_filter ( $frets, fn ( $one ) => $one !== NULL ) ) );
        }

      };

      $walk ( 0, [], FALSE );

    }

    return $best;

  }

  // The frets as a chart writes them: x32010, or 8 10 10 9 8 8 when one is past the ninth.

  function padChordWrite ( $frets ) {

    $high = max ( array_map ( 'intval', $frets ) );

    return implode ( $high > 9 ? ' ' : '', array_map ( fn ( $fret ) => $fret === NULL ? 'x' : (string) $fret, $frets ) );

  }

  function padChordOrder ( $frets ) {

    return implode ( ',', array_map ( fn ( $fret ) => $fret === NULL ? 'x' : sprintf ( '%02d', $fret ), (array) $frets ) );

  }

  // The chord a shape plays: its notes as a set, matched with each quality from each note -
  // the lowest first, so x32010 is C and not Am7 without its fifth. NULL when none fits.

  function padChordName ( $frets, $tuning ) {

    $notes = [];

    foreach ( $frets as $at => $fret )
      if ( $fret !== NULL )
        $notes [] = ( $tuning [$at] + $fret ) % 12;

    if ( ! $notes )
      return NULL;

    $set = array_values ( array_unique ( $notes ) );
    sort ( $set );

    $candidates = array_values ( array_unique ( $notes ) );

    foreach ( $candidates as $root )
      foreach ( PAD_MUSIC_QUALITIES as $quality => list ( $intervals ) ) {
        $tones = array_values ( array_unique ( array_map ( fn ( $one ) => ( $root + $one ) % 12, $intervals ) ) );
        sort ( $tones );
        if ( $tones === $set ) {
          $name = padMusicName ( $root ) . $quality;
          $bass = ( $notes [0] != $root ) ? '/' . padMusicName ( $notes [0], padMusicName ( $root ) ) : '';
          return padMusicChord ( str_replace ( [ '♯', '♭' ], [ '#', 'b' ], $name . $bass ) );
        }
      }

    return NULL;

  }

  // The box. Strings 18 apart, frets 20; four frets shown, or as many as the shape spans up to
  // five. A shape that stays in the first four frets starts at the nut, drawn thick; one higher
  // up starts at its lowest fret, its number beside the first.

  function padChordSvg ( $frets, $instrument, $name, $words, $spelling, $size, $id, $title ) {

    list ( $tuning, $stringNames ) = PAD_CHORD_TUNINGS [$instrument];

    $count   = count ( $frets );
    $pressed = array_filter ( $frets, fn ( $fret ) => $fret !== NULL and $fret > 0 );
    $high    = $pressed ? max ( $pressed ) : 0;
    $low     = $pressed ? min ( $pressed ) : 0;
    $start   = ( $high <= 4 ) ? 1 : $low;
    $rows    = max ( 4, min ( 5, $high - $start + 1 ) );

    $gap   = 18;
    $step  = 20;
    $left  = 26;
    $top   = 44;
    $width = $left + ( $count - 1 ) * $gap + 26;
    $height = $top + $rows * $step + 26;

    $svg = '<text class="pch-name" x="' . padNotationNum ( $left + ( $count - 1 ) * $gap / 2 ) . '" y="18" text-anchor="middle">' . padNotationAttr ( $name ) . '</text>';

    // The frets across, the nut thick when the box starts at it; the strings standing.

    for ( $row = 0; $row <= $rows; $row++ ) {
      $y    = $top + $row * $step;
      $svg .= '<path class="' . ( ( $row == 0 and $start == 1 ) ? 'pch-nut' : 'pch-fret' ) . '" d="M' . $left . ' ' . $y . 'h' . ( ( $count - 1 ) * $gap ) . '"/>';
    }

    for ( $string = 0; $string < $count; $string++ )
      $svg .= '<path class="pch-string" d="M' . ( $left + $string * $gap ) . ' ' . $top . 'v' . ( $rows * $step ) . '"/>';

    if ( $start > 1 )
      $svg .= '<text class="pch-fretno" x="' . ( $left - 7 ) . '" y="' . ( $top + $step / 2 + 4 ) . '" text-anchor="end">' . $start . 'fr</text>';

    // A bar where one finger presses the lowest fret across the strings to the highest: on
    // three strings or more, or from the lowest string played - the way F and Bm are drawn,
    // where the D chord's two dots at the second fret stay two dots.

    $barre = NULL;

    if ( $pressed ) {
      $under  = array_keys ( $frets, $low, TRUE );
      $played = array_keys ( array_filter ( $frets, fn ( $fret ) => $fret !== NULL ) );
      if ( count ( $under ) >= 2 and max ( $under ) == $count - 1
           and ! array_filter ( range ( min ( $under ), $count - 1 ), fn ( $at ) => $frets [$at] === NULL or $frets [$at] < $low )
           and ( count ( $under ) >= 3 or min ( $under ) == min ( $played ) ) )
        $barre = [ min ( $under ), $count - 1 ];
    }

    if ( $barre ) {
      $y    = $top + ( $low - $start ) * $step + $step / 2;
      $svg .= '<rect class="pch-barre" x="' . ( $left + $barre [0] * $gap - 7 ) . '" y="' . ( $y - 7 ) . '" width="' . ( ( $barre [1] - $barre [0] ) * $gap + 14 ) . '" height="14" rx="7"/>';
    }

    $said = [];

    foreach ( $frets as $string => $fret ) {

      $x = $left + $string * $gap;

      if ( $fret === NULL ) {
        $svg   .= '<path class="pch-mute" d="M' . ( $x - 4 ) . ' ' . ( $top - 15 ) . 'l8 8m0 -8l-8 8"/>';
        $said [] = $stringNames [$string] . ' string not played';
        continue;
      }

      $note = padMusicName ( $tuning [$string] + $fret, $spelling );

      if ( $fret == 0 )
        $svg .= '<circle class="pch-open" cx="' . $x . '" cy="' . ( $top - 11 ) . '" r="4"/>';
      elseif ( ! ( $barre and $fret == $low and $string >= $barre [0] ) )
        $svg .= '<circle class="pch-dot" cx="' . $x . '" cy="' . ( $top + ( $fret - $start ) * $step + $step / 2 ) . '" r="7"/>';

      $svg .= '<text class="pch-note" x="' . $x . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padNotationAttr ( $note ) . '</text>';

      $said [] = $stringNames [$string] . ' string ' . ( $fret == 0 ? 'open' : padChordOrdinal ( $fret ) . ' fret' ) . ', ' . $note;

    }

    // size= is the width of a guitar's box, six strings: a ukulele's box is drawn to the same
    // scale, narrower, so the two stand side by side alike.

    $scale = $size / ( $left + 5 * $gap + 26 );
    $desc  = ( $words !== '' ? "$words. " : '' ) . ucfirst ( implode ( '; ', $said ) ) . '.';

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-chord" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . padNotationNum ( $width * $scale ) . '" height="' . padNotationNum ( $height * $scale ) . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

  function padChordOrdinal ( $number ) {

    $last = $number % 10;

    return $number . ( ( $number % 100 >= 11 and $number % 100 <= 13 ) ? 'th' : ( [ 1 => 'st', 2 => 'nd', 3 => 'rd' ] [$last] ?? 'th' ) );

  }

?>
