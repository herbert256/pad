<?php

  // Piano keyboards - the piano notation, {notation:piano 'C4 E4 G4'/}. Notes - or a chord, voiced from
  // its root - shown on a keyboard, the keys pressed marked and named.
  //
  // padPianoNotes   the notes of a text as MIDI numbers (C4 is 60) with their names: the notes
  //                 written out - C4 E4 G4, Bb3, F#5 - or one chord name, Cmaj7, voiced upwards
  //                 from its root in the fourth octave (its slash bass an octave lower)
  // padPianoSvg     the keyboard from a key to a key, white keys 24 wide, black keys 14

  include_once PAD . 'subs/notations/_lib/music.php';

  const PAD_PIANO_COLOURS = [ 'white' => [ '#ffffff', '#e8e8e4' ], 'black' => [ '#1f2326', '#0b0c0d' ],
                              'edge'  => [ '#5b6168', '#2a2e33' ], 'mark'  => [ '#2a78d6', '#4c95ea' ],
                              'text'  => [ '#8a9097', '#6b7177' ], 'label' => [ '#ffffff', '#ffffff' ] ];

  // The white keys of an octave, by pitch class, and where each black key stands: its middle,
  // in white-key widths from the octave's C - a little off the line between its two white keys,
  // as on an instrument.

  const PAD_PIANO_WHITE = [ 0 => 0, 2 => 1, 4 => 2, 5 => 3, 7 => 4, 9 => 5, 11 => 6 ];
  const PAD_PIANO_BLACK = [ 1 => 0.88, 3 => 2.12, 6 => 3.84, 8 => 5.0, 10 => 6.16 ];

  // The notes, or the reason there are none: [ [ midi => name ], words for the title ] or a
  // string. Notes without an octave climb from the fourth: C E G is C4 E4 G4, G B D is G4 B4 D5.
  //
  // A text of one word is a chord when it is a chord name, else a note: E7 is the dominant
  // seventh on E, and the note E7 alone - a note in the fifth, sixth, seventh or ninth octave
  // is a chord name as well - is written as a list, with a comma: 'E7,'.

  function padPianoNotes ( $text ) {

    $text   = trim ( (string) $text );
    $tokens = preg_split ( '/[\s,]+/u', $text, -1, PREG_SPLIT_NO_EMPTY );

    if ( ! $tokens )
      return [ [], '' ];

    if ( count ( $tokens ) == 1 and ! preg_match ( '/[\s,]/u', $text ) and ( padMusicChord ( $text ) or ! padMusicNote ( $text ) ) ) {

      $chord = padMusicChord ( $tokens [0] );

      if ( ! $chord )
        return "no note or chord '" . padNotationShow ( $tokens [0] ) . "' - notes are written C4 E4 G4, a chord C, Am7, F#dim";

      list ( $root, $intervals, $bass, $name, $words, $spelling ) = $chord;

      $notes = [];

      if ( $bass !== NULL )
        $notes [ 48 + $bass ] = padMusicName ( $bass, $spelling ) . ( 3 + intdiv ( $bass, 12 ) );

      foreach ( $intervals as $interval ) {
        $midi = 60 + $root + $interval;
        $notes [$midi] = padMusicName ( $midi, $spelling ) . ( intdiv ( $midi, 12 ) - 1 );
      }

      ksort ( $notes );

      return [ $notes, $words ];

    }

    $notes = [];
    $last  = NULL;

    foreach ( $tokens as $token ) {

      $note = padMusicNote ( $token );

      if ( ! $note )
        return "no note '" . padNotationShow ( $token ) . "' - a note is a letter, a sharp or flat and its octave: C4, F#3, Bb5";

      list ( $class, $octave, $name, $shift ) = $note;

      if ( $octave === NULL ) {
        $midi = 60 + $class;
        while ( $last !== NULL and $midi <= $last )
          $midi += 12;
      } else
        $midi = ( $octave + 1 ) * 12 + $class;

      if ( $midi < 21 or $midi > 108 )
        return "a note '" . padNotationShow ( $token ) . "' off the keyboard - a piano runs from A0 to C8";

      // The note is named with the octave of its letter, as it was written: Cb4 is the key of
      // B3 and says C♭4, B#3 the key of C4 and says B♯3 - the octave of the key put C♭3 and B♯4.

      $notes [$midi] = $name . ( intdiv ( $midi - $shift, 12 ) - 1 );
      $last          = $midi;

    }

    ksort ( $notes );

    return [ $notes, '' ];

  }

  // A MIDI number of a key name - C4, A0, C8 - or NULL.

  function padPianoKey ( $name ) {

    $note = padMusicNote ( $name );

    if ( ! $note or $note [1] === NULL )
      return NULL;

    $midi = ( $note [1] + 1 ) * 12 + $note [0];

    return ( $midi >= 21 and $midi <= 108 ) ? $midi : NULL;

  }

  // The keyboard from $from to $to - both moved out to white keys - its pressed keys marked
  // and named, every C named under its key for the reader to find the octave.

  function padPianoSvg ( $notes, $from, $to, $height, $id, $title, $desc ) {

    while ( ! isset ( PAD_PIANO_WHITE [ $from % 12 ] ) )
      $from--;

    while ( ! isset ( PAD_PIANO_WHITE [ $to % 12 ] ) )
      $to++;

    $whiteW = 24;
    $whiteH = 120;
    $blackW = 14;
    $blackH = 76;

    $column = fn ( $midi ) => ( intdiv ( $midi, 12 ) * 7 + PAD_PIANO_WHITE [ $midi % 12 ] );
    $first  = $column ( $from );
    $keys   = $column ( $to ) - $first + 1;

    $whites = $blacks = '';

    for ( $midi = $from; $midi <= $to; $midi++ ) {

      $class  = $midi % 12;
      $marked = isset ( $notes [$midi] );

      if ( isset ( PAD_PIANO_WHITE [$class] ) ) {

        $x = 1 + ( $column ( $midi ) - $first ) * $whiteW;

        $whites .= '<rect class="ppi-white' . ( $marked ? ' ppi-on' : '' ) . '" x="' . $x . '" y="1" width="' . $whiteW . '" height="' . $whiteH . '" rx="2"/>';

        if ( $marked )
          $whites .= '<text class="ppi-label" x="' . ( $x + $whiteW / 2 ) . '" y="' . ( $whiteH - 9 ) . '" text-anchor="middle">' . padNotationAttr ( $notes [$midi] ) . '</text>';
        elseif ( $class == 0 )
          $whites .= '<text class="ppi-c" x="' . ( $x + $whiteW / 2 ) . '" y="' . ( $whiteH - 9 ) . '" text-anchor="middle">C' . ( intdiv ( $midi, 12 ) - 1 ) . '</text>';

      } else {

        $octave = intdiv ( $midi, 12 ) * 7 - $first;
        $middle = 1 + ( $octave + PAD_PIANO_BLACK [$class] ) * $whiteW;
        $x      = padNotationNum ( $middle - $blackW / 2 );

        $blacks .= '<rect class="ppi-black' . ( $marked ? ' ppi-on' : '' ) . '" x="' . $x . '" y="1" width="' . $blackW . '" height="' . $blackH . '" rx="2"/>';

        if ( $marked )
          $blacks .= '<text class="ppi-label ppi-small" x="' . padNotationNum ( $middle ) . '" y="' . ( $blackH - 8 ) . '" text-anchor="middle">' . padNotationAttr ( padPianoShort ( $notes [$midi] ) ) . '</text>';

      }

    }

    $width = $keys * $whiteW + 2;
    $scale = $height / ( $whiteH + 2 );

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-piano" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . padNotationNum ( $width * $scale ) . '" height="' . padNotationNum ( ( $whiteH + 2 ) * $scale ) . '" viewBox="0 0 ' . $width . ' ' . ( $whiteH + 2 ) . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $whites . $blacks . '</svg>';

  }

  // The name on a key: without its octave on a black key, which has room for two characters.

  function padPianoShort ( $name ) {

    return preg_replace ( '/-?\d+$/', '', $name );

  }

?>
