<?php

  // Tunes in ABC - the abc notation, {notation:abc}. ABC is the plain-text notation folk musicians have
  // written tunes in since 1991: a few header lines - T: the title, M: the meter, L: the unit
  // note length, K: the key - and then the notes as letters, C middle C and c the octave above,
  // a number behind a note a multiple of the unit. This file reads a useful part of ABC 2.1 and
  // engraves it as inline SVG: a five-line staff for every line of music with a treble clef,
  // the key and time signature, the notes with their stems, beams, flags, dots, ledger lines
  // and accidentals, rests, ties, slurs, tuplets, bar lines, repeats with first and second
  // endings, chord names, and the title, composer and tempo above the first staff.
  //
  //   X:1
  //   T:Speed the Plough
  //   M:4/4
  //   L:1/8
  //   K:G
  //   |:GABc dedB|dedB dedB|c2ec B2dB|c2A2 A2BA|
  //
  // padAbcSvg   the SVG of a tune: one <svg> drawn in currentColor, its accessible name and a
  //             description of the tune bar by bar for a screen reader, and what it could not
  //             read in the problems it hands back
  // padAbcRead  the tune read: the header and the lines of music as events - notes, rests,
  //             bar lines - and the problems
  //
  // What is read: the header fields X: (ignored), T: (a second one the subtitle), C:, M: (4/4,
  // 6/8, 2+3/8, C, C|, none), L:, Q: (1/4=120, "Allegro" 3/8=60) and K: (every key and mode,
  // K:none, clef=treble); in the music notes with accidentals and octave marks, lengths,
  // broken rhythm (> < >> <<), rests z and x, bar rests Z and Z4, chords [CEG], ties, slurs,
  // tuplets (3abc and (p:q:r, every bar line and repeat sign, endings [1 |2 :|2, chord names
  // "Am", annotations "^text" "_text" "<text" ">text", the decorations staccato (.), accent
  // (L, !>!), fermata (H) and trill (T) - drawn - and the others read and left out, a \ at the
  // end of a line, the inline fields [K:] [M:] [L:] and the fields K: M: L: on a line of their
  // own between the lines of music; an empty line ends the tune. Left out: grace notes {..}
  // (read and skipped), lyrics (w: lines, skipped), more than one voice (V: fields are skipped,
  // so the lines of the voices follow each other; the overlay & is a problem), other clefs than
  // the treble clef (a problem, drawn treble), the accidentals of a K: field written out (K:D
  // ^c, a problem), and lengths no single note has - 5/8, a 64th, a breve - which are a
  // problem and drawn as the longest note value that is not longer.

  // The staff: a space is 10 units, the staff 40 high with its top line at y 0. A note's
  // place is its diatonic step - the octave times 7 plus C D E F G A B as 0 to 6, so middle C
  // is 28 - and the top line is F5, step 38: every step lower is 5 units further down.

  const PAD_ABC_TOP      = 38;    // F5, the top line
  const PAD_ABC_MIDDLE   = 34;    // B4, the middle line, which decides where stems go
  const PAD_ABC_LEFT     = 4;     // where the staff begins
  const PAD_ABC_RIGHT    = 4;     // the room right of it
  const PAD_ABC_HEAD     = 6.5;   // half the width of a note head
  const PAD_ABC_STEM_X   = 5.7;   // a stem's distance from the middle of its head
  const PAD_ABC_STEM     = 35;    // the length of a stem: three and a half spaces
  const PAD_ABC_BEAM     = 5;     // the thickness of a beam
  const PAD_ABC_BEAM_GAP = 7.5;   // the distance from one beam to the next

  // How many slurs may be open at once. A slur is drawn over the notes it binds, lifted to
  // clear each of them, so slurs nested a thousand deep took time with the square of their
  // number; music nests two or three. A slur opened past this is a problem and left out.

  const PAD_ABC_SLURS = 8;

  const PAD_ABC_LETTERS = 'CDEFGAB';

  // The keys: the fifths of a tonic in a major key (F has one flat, G one sharp), what a mode
  // shifts that by with its name, and the order of the sharps and flats of a key signature
  // with the step each is drawn on in the treble clef.

  const PAD_ABC_FIFTHS = [ 'F' => -1, 'C' => 0, 'G' => 1, 'D' => 2, 'A' => 3, 'E' => 4, 'B' => 5 ];

  const PAD_ABC_MODES = [ 'maj' => [ 0, 'major' ], 'ion' => [ 0, 'ionian' ], 'min' => [ -3, 'minor' ],
                          'aeo' => [ -3, 'aeolian' ], 'mix' => [ -1, 'mixolydian' ], 'dor' => [ -2, 'dorian' ],
                          'phr' => [ -4, 'phrygian' ], 'lyd' => [ 1, 'lydian' ], 'loc' => [ -5, 'locrian' ] ];

  const PAD_ABC_SHARPS = [ [ 'F', 38 ], [ 'C', 35 ], [ 'G', 39 ], [ 'D', 36 ], [ 'A', 33 ], [ 'E', 37 ], [ 'B', 34 ] ];
  const PAD_ABC_FLATS  = [ [ 'B', 34 ], [ 'E', 37 ], [ 'A', 33 ], [ 'D', 36 ], [ 'G', 32 ], [ 'C', 35 ], [ 'F', 31 ] ];

  // The accidentals as written, what each does to the pitch, the glyph it is drawn with and
  // the room it takes left of the head.

  const PAD_ABC_ALTER = [ '^^' => 2, '^' => 1, '=' => 0, '_' => -1, '__' => -2 ];
  const PAD_ABC_ACC   = [ '^^' => [ 'dsharp', 8.4 ], '^' => [ 'sharp', 8.6 ], '=' => [ 'natural', 6 ],
                          '_' => [ 'flat', 7.6 ], '__' => [ 'flat', 14 ] ];

  const PAD_ABC_VALUES = [ 1 => 'whole', 2 => 'half', 4 => 'quarter', 8 => 'eighth', 16 => 'sixteenth', 32 => 'thirty-second' ];

  const PAD_ABC_DECORATIONS = [ '.' => 'staccato', '~' => 'roll', 'H' => 'fermata', 'L' => 'accent', 'T' => 'trill',
                                'M' => 'lowermordent', 'P' => 'uppermordent', 'S' => 'segno', 'O' => 'coda',
                                'u' => 'upbow', 'v' => 'downbow' ];

  // How far each rest reaches up and down the staff, for the room above and below a line.

  const PAD_ABC_RESTS = [ 1 => [ 10, 15 ], 2 => [ 15, 20 ], 4 => [ 4, 33 ], 8 => [ 12, 30 ], 16 => [ 12, 40 ], 32 => [ 2, 40 ] ];

  // The glyphs, drawn once per drawing in <defs> and placed with <use>: the treble clef (its
  // outline made by sweeping a pen of changing width along a curve through the points of a
  // clef, staff top at y 0, the spiral round the G line at y 30), the note heads (tilted
  // ellipses, the open ones with a hole), the accidentals, the rests (in staff coordinates),
  // the flags (from the end of the stem), the C of common time and the fermata. The open
  // heads and the flat have holes and are filled even-odd.

  const PAD_ABC_GLYPHS = [
    'clef'    => 'M8.8 48.7l0.1 0.7 0.1 0.9 0.2 1.2 0.2 1.1 0.5 1 0.6 0.9 0.7 0.7 0.8 0.6 0.9 0.4 0.9 0.3 1 0.1 1.1-0.1 1-0.3 1-0.5 0.9-0.7 0.8-0.9 0.6-1.1 0.5-1.2 0.3-1.3 0.3-1.3 0.2-1.4 0.1-1.4 0-1.4-0.1-1.5-0.2-1.6-0.1-1.7-0.3-1.8-0.3-1.8-0.3-1.9-0.4-1.9-0.3-1.9-0.3-2-0.3-2-0.3-2-0.4-2.1-0.3-2-0.4-2-0.3-2-0.4-2-0.4-2-0.5-2-0.4-2.1-0.5-2-0.4-2-0.3-1.8-0.2-1.7-0.1-1.6-0.1-1.6 0.1-1.5 0-1.4 0.2-1.5 0.3-1.4 0.3-1.3 0.4-1.2 0.4-1 0.4-0.7 0.3-0.4 0.4-0.3 0.4-0.2 0.3-0.1 0.4 0.1 0.5 0.2 0.5 0.2 0.4 0.4 0.4 0.4 0.3 0.6 0.4 0.7 0.3 0.8 0.3 0.9 0.1 0.8 0.1 1 0.1 1-0.1 1.1-0.1 1.1-0.2 1-0.4 1.1-0.5 1.1-0.6 1.1-0.7 1.1-0.8 1.1-1 1.2-1.2 1.2-1.4 1.3-1.4 1.2-1.4 1.3-1.4 1.2-1.5 1.2-1.5 1.3-1.5 1.3-1.4 1.4-1.2 1.5-1.1 1.5-1 1.6-0.8 1.6-0.6 1.6-0.4 1.7-0.2 1.7 0 1.7 0.1 1.6 0.3 1.5 0.6 1.5 0.7 1.5 0.8 1.4 1.1 1.3 1.1 1.1 1.4 0.9 1.4 0.8 1.5 0.6 1.6 0.4 1.6 0.3 1.5 0.1 1.7 0 1.7-0.1 1.6-0.3 1.6-0.4 1.5-0.5 1.5-0.7 1.4-0.9 1.4-1 1.1-1.2 0.9-1.4 0.7-1.6 0.4-1.6 0.3-1.7 0-1.5-0.2-1.6-0.4-1.5-0.6-1.4-0.8-1.3-0.8-1.1-1-0.9-1.1-0.7-1.2-0.5-1.2-0.4-1.1-0.2-1.2-0.1-1.3 0-1.2 0.2-1.3 0.2-1.1 0.4-1.1 0.4-1 0.6-1 0.6-0.9 0.8-0.8 0.9-0.6 1-0.6 1-0.3 1.2-0.3 1.1-0.1 1.1 0.1 1.1 0.2 1.1 0.3 1.1 0.5 1 0.5 0.9 0.8 0.7 0.9 0.6 0.9 0.4 0.6 0.2 0.4 0.2 1.2-3-0.5-0.2-0.6-0.2-0.6-0.2-0.5-0.2-0.2-0.3-0.3-0.4-0.3-0.6-0.3-0.7-0.2-0.8-0.1-0.7 0-0.8 0.1-0.9 0.2-0.9 0.2-0.8 0.4-0.8 0.5-0.7 0.6-0.7 0.8-0.6 0.8-0.6 0.9-0.5 0.9-0.3 1.1-0.2 1.1-0.2 1.1 0.1 0.9 0.1 0.9 0.4 0.9 0.5 0.7 0.6 0.6 0.7 0.5 0.7 0.4 0.9 0.4 1 0.2 1.1 0.2 1.1 0 1-0.1 1.2-0.1 1.2-0.3 1.2-0.3 1.2-0.5 1-0.6 1-0.8 1-1 0.9-1.1 0.9-1.2 0.7-1.3 0.5-1.4 0.3-1.5 0.2-1.5 0.1-1.4-0.2-1.3-0.4-1.3-0.6-1.2-0.7-1.1-0.8-0.8-0.9-0.7-1-0.5-1.1-0.5-1.2-0.3-1.2-0.1-1.1-0.1-1.2 0-1.1 0.1-1.2 0.2-1.2 0.4-1.1 0.5-1.2 0.6-1.2 0.8-1.3 0.9-1.3 0.9-1.2 1.1-1.2 1.3-1.2 1.4-1.3 1.5-1.3 1.4-1.4 1.4-1.4 1.3-1.4 1.3-1.4 1.3-1.4 1.1-1.4 1-1.4 0.8-1.4 0.6-1.4 0.6-1.3 0.4-1.4 0.3-1.4 0.2-1.5 0-1.4-0.1-1.3-0.2-1.2-0.4-1.2-0.5-1.2-0.7-1-0.6-0.9-0.8-0.7-0.8-0.6-0.9-0.4-0.9-0.2-0.8 0-0.8 0.1-0.7 0.2-0.7 0.4-0.6 0.5-0.5 0.7-0.5 0.9-0.4 1.1-0.4 1.3-0.4 1.4-0.2 1.5-0.2 1.5-0.2 1.5 0 1.6 0 1.6 0.1 1.7 0.2 1.8 0.3 1.9 0.4 2 0.4 2.1 0.4 2.1 0.4 2 0.4 2 0.3 2 0.4 2 0.3 2 0.4 2 0.3 2 0.3 2 0.3 2 0.4 2 0.3 2 0.3 1.9 0.3 1.8 0.3 1.8 0.3 1.8 0.2 1.6 0.1 1.5 0.1 1.5 0 1.3-0.1 1.3-0.1 1.1-0.3 1.2-0.3 1.2-0.4 1-0.5 0.9-0.4 0.7-0.6 0.4-0.7 0.3-0.7 0.2-0.8 0.1-0.6-0.1-0.6-0.1-0.7-0.4-0.6-0.4-0.5-0.5-0.5-0.5-0.3-0.7-0.2-1-0.2-1-0.1-0.9-0.1-0.7zM5.5 48.6a4.1 4.1 0 1 0 8.2 0a4.1 4.1 0 1 0-8.2 0zM11.3 33.6a1.9 1.9 0 1 0 3.8 0a1.9 1.9 0 1 0-3.8 0z',
    'black'   => 'M-6.11 2.22A6.5 4.5-20 1 1 6.11-2.22A6.5 4.5-20 1 1-6.11 2.22z',
    'half'    => 'M-6.11 2.22A6.5 4.5-20 1 1 6.11-2.22A6.5 4.5-20 1 1-6.11 2.22zM-3.9 2.44A4.6 2.2-32 1 0 3.9-2.44A4.6 2.2-32 1 0-3.9 2.44z',
    'whole'   => 'M-8.5 0A8.5 5.2 0 1 1 8.5 0A8.5 5.2 0 1 1-8.5 0zM-2.41-3.44A4.2 2.6 55 1 0 2.41 3.44A4.2 2.6 55 1 0-2.41-3.44z',
    'sharp'   => 'M-2.4-10h1.1v22.5h-1.1zM1.3-12.5h1.1V10h-1.1zM-4.2-4.4L4.2-6.6v3L-4.2-1.4zM-4.2 3.6L4.2 1.4v3L-4.2 6.6z',
    'flat'    => 'M-3.6-17h1.1v15.2C-0.4-4.4 2.3-5.4 3.8-4.1C5.3-2.8 4.6 0.2 1.6 2.6L-2.5 5.6h-1.1zM-2.5 3.6C0 1.9 2.1 0.2 2.3-1.6C2.5-3.2 0.8-3.8-2.5-1.1z',
    'natural' => 'M-2.7-12h1.1v8.3L2.7-5.4V12H1.6V3.7L-2.7 5.4zM-1.6-0.6v3.5L1.6 1.2v-3.5z',
    'dsharp'  => 'M-4.4-4.4h3.2l1.2 1.4 1.2-1.4h3.2v3.2l-1.4 1.2 1.4 1.2v3.2h-3.2l-1.2-1.4-1.2 1.4h-3.2v-3.2l1.4-1.2-1.4-1.2z',
    'r1'      => 'M-6 10h12v5h-12z',
    'r2'      => 'M-6 15h12v5h-12z',
    'r4'      => 'M-1.8 4.6L3.8 11.4C2.2 13.2 0.8 15 0.4 17L3.6 23C1.8 22.6 0 23.2-0.6 25C-1.2 27.2 0 30.6 2 33.4L1.4 34C-1.6 32.4-3.8 29.8-3.4 27.2C-3 24.6-1.2 22.6 1 22.6L-3.4 16.2C-1.6 14.2-0.4 12 0.2 9.8C0 7.8-0.6 6.2-1.8 4.6z',
    'r8'      => 'M3.7 12.6L4.9 12.8L0.38 30L-0.82 30zM-4.7 15a2.3 2.3 0 1 0 4.6 0a2.3 2.3 0 1 0-4.6 0zM-2.8 17.1C-0.4 18.1 2.2 16.2 4 12.7L4.8 13.4C2.8 17.6 -0.2 19.6 -3.6 18.2z',
    'r16'     => 'M4.7 12.6L5.9 12.8L-0.68 40L-1.88 40zM-3.7 15a2.3 2.3 0 1 0 4.6 0a2.3 2.3 0 1 0-4.6 0zM-1.8 17.1C0.6 18.1 3.2 16.2 5 12.7L5.8 13.4C3.8 17.6 0.8 19.6 -2.6 18.2zM-6.1 25a2.3 2.3 0 1 0 4.6 0a2.3 2.3 0 1 0-4.6 0zM-4.2 27.1C-1.8 28.1 0.8 26.2 2.6 22.7L3.4 23.4C1.4 27.6 -1.6 29.6 -5 28.2z',
    'r32'     => 'M5.7 2.6L6.9 2.8L-0.97 40L-2.17 40zM-2.7 5a2.3 2.3 0 1 0 4.6 0a2.3 2.3 0 1 0-4.6 0zM-0.8 7.1C1.6 8.1 4.2 6.2 6 2.7L6.8 3.4C4.8 7.6 1.8 9.6 -1.6 8.2zM-4.8 15a2.3 2.3 0 1 0 4.6 0a2.3 2.3 0 1 0-4.6 0zM-2.9 17.1C-0.5 18.1 2.1 16.2 3.9 12.7L4.7 13.4C2.7 17.6 -0.3 19.6 -3.7 18.2zM-6.9 25a2.3 2.3 0 1 0 4.6 0a2.3 2.3 0 1 0-4.6 0zM-5 27.1C-2.6 28.1 0 26.2 1.8 22.7L2.6 23.4C0.6 27.6 -2.4 29.6 -5.8 28.2z',
    'flagU'   => 'M-0.6 0H0.6C1 3.6 3.8 5.6 6.4 8.6C8.8 11.4 10.2 14.8 9.6 19.4C9.2 21.6 8.6 23.2 7.8 24.6C8.2 22.6 8.4 20.6 7.8 18.2C6.8 14.2 3.8 11 0.6 7.2H-0.6z',
    'flagD'   => 'M-0.6 0H0.6C1-3.6 3.8-5.6 6.4-8.6C8.8-11.4 10.2-14.8 9.6-19.4C9.2-21.6 8.6-23.2 7.8-24.6C8.2-22.6 8.4-20.6 7.8-18.2C6.8-14.2 3.8-11 0.6-7.2H-0.6z',
    'common'  => 'M5.4-4.6C5.4-7.6 3-9.6-0.2-9.6C-5-9.6-8.2-5.4-8.2 0C-8.2 5.6-4.8 9.6 0.4 9.6C3.6 9.6 5.8 7.8 6.8 4.8L6 4.4C5 6.6 3.4 7.8 1.2 7.8C-2.2 7.8-3.8 4.6-3.8 0C-3.8-4.8-2.2-7.8 0.6-7.8C2.2-7.8 3.2-7.2 3.4-6.4C2.4-6.4 1.6-5.4 1.6-4.4C1.6-3.2 2.6-2.4 3.6-2.4C4.8-2.4 5.4-3.4 5.4-4.6z',
    'fermata' => 'M-9 0C-9-11.5 9-11.5 9 0H7.6C7.6-8.8-7.6-8.8-7.6 0zM-1.7-2a1.7 1.7 0 1 0 3.4 0a1.7 1.7 0 1 0-3.4 0z' ];

  const PAD_ABC_EVENODD = [ 'half' => TRUE, 'whole' => TRUE, 'flat' => TRUE ];

  // The SVG of a tune. $id prefixes the ids of the drawing's title, description and glyphs;
  // $width spreads every line over that many pixels - the last line only when it is not much
  // shorter - and 0 leaves each line at its natural width; $title is the accessible name,
  // else the tune's T:, else "Music". A tune with no notes and no title answers ''.

  function padAbcSvg ( string $text, string $id, int $width = 0, string $title = '', array &$problems = [] ) : string {

    $tune = padAbcRead ( $text, $problems );

    if ( ! $tune ['notes'] and ! $tune ['titles'] )
      return '';

    $d = [ 'id' => $id, 'used' => [] ];

    // Every line made ready and measured at its natural width: the widest sets the width of
    // the drawing, and a given width when that is wider.

    $preps   = [];
    $natural = [];

    foreach ( $tune ['lines'] as $index => $line ) {
      $preps   [$index] = padAbcPrepare ( $line );
      $natural [$index] = padAbcPlace ( $preps [$index], 1 ) [1];
    }

    $full = max ( 100, padAbcHeaderWidth ( $tune ), ...array_values ( $natural ?: [ 0 ] ) );

    if ( $width > 0 )
      $full = max ( $full, $width );

    // The header, then the lines one under the other, each as high as what it draws.

    $y     = 2;
    $body  = padAbcHeader ( $d, $tune, $full, $y );
    $carry = [ 'ties' => [], 'slurs' => [], 'ending' => NULL ];
    $last  = count ( $preps ) - 1;

    foreach ( $preps as $index => $prep ) {

      $target = $natural [$index];

      if ( $width > 0 and ! ( $index == $last and $natural [$index] < 0.6 * $full ) )
        $target = $full;

      list ( $xs, $end ) = padAbcFit ( $prep, $target );

      $drawn  = padAbcDraw ( $d, $prep, $xs, $end, $carry );
      $staffY = $y - $drawn ['top'] + ( $index ? 6 : 2 );
      $body  .= '<g transform="translate(0 ' . padAbcNum ( $staffY ) . ')">' . $drawn ['svg'] . '</g>';
      $y      = $staffY + $drawn ['bottom'];

    }

    $height = $y + 4;
    $name   = ( $title !== '' ) ? $title : ( $tune ['titles'] [0] ?? 'Music' );

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-abc" role="img"'
         . ' aria-labelledby="' . padAbcText ( "$id-title $id-desc" ) . '"'
         . ' width="' . padAbcNum ( $width > 0 ? $width : $full ) . '"'
         . ' height="' . padAbcNum ( $width > 0 ? $height * $width / $full : $height ) . '"'
         . ' viewBox="0 0 ' . padAbcNum ( $full ) . ' ' . padAbcNum ( $height ) . '">'
         . '<title id="' . padAbcText ( "$id-title" ) . '">' . padAbcText ( $name ) . '</title>'
         . '<desc id="' . padAbcText ( "$id-desc" ) . '">' . padAbcText ( padAbcDescribe ( $tune ) ) . '</desc>';

    // The glyphs used, in a fixed order so the same tune gives the same bytes.

    $defs = '';

    foreach ( PAD_ABC_GLYPHS as $glyph => $path )
      if ( isset ( $d ['used'] [$glyph] ) )
        $defs .= '<path id="' . padAbcText ( "$id-$glyph" ) . '" d="' . $path . '"' . ( isset ( PAD_ABC_EVENODD [$glyph] ) ? ' fill-rule="evenodd"' : '' ) . '/>';

    if ( $defs !== '' )
      $svg .= "<defs>$defs</defs>";

    return $svg . '<g fill="currentColor" font-family="system-ui, sans-serif">' . $body . '</g></svg>';

  }

  // ------------------------------------------------------------------------------------------
  // Reading
  // ------------------------------------------------------------------------------------------

  // The tune read: [ titles, composer, meter, tempo, key, lines, notes ], a line being
  // [ items, key, meter ] - the events of a line of music, the key it starts in and the meter
  // drawn at its start (NULL when none is). An event is an array with its kind: note (heads,
  // len, base, dots, time, ...), rest, mrest (a bar rest of bars), bar, ending, key, meter.

  function padAbcRead ( $text, &$problems ) {

    $tune = [ 'titles' => [], 'composer' => [], 'meter' => [ 'kind' => 'none' ], 'tempo' => NULL,
              'key' => padAbcKey ( 'C', $problems ), 'lines' => [], 'notes' => 0 ];

    $unit   = NULL;
    $state  = NULL;
    $music  = FALSE;
    $ended  = FALSE;
    $join   = '';
    $meter  = TRUE;
    $lines  = preg_split ( '/\r\n|\r|\n/', (string) $text );
    $lines [] = '';

    foreach ( $lines as $raw ) {

      // A % starts a comment - \% is a percent sign - and a line that is only a comment or a
      // %% directive is no line at all: it neither breaks the staff nor ends the tune.

      if ( str_starts_with ( ltrim ( $raw ), '%' ) )
        continue;

      $line = rtrim ( preg_replace ( '/(?<!\\\\)%.*$/s', '', $raw ) );
      $line = str_replace ( '\\%', '%', $line );

      // A line ending in \ waits for the next, which goes on where it stopped.

      if ( $music and $join !== '' and trim ( $line ) !== '' and ! str_ends_with ( $line, '\\' ) and ! preg_match ( '/^[A-Za-z]:/', $line ) ) {
        $line = $join . $line;
        $join = '';
      }

      if ( trim ( $line ) === '' ) {
        if ( $join !== '' ) {
          $line = $join;
          $join = '';
        } else {
          if ( $music and $tune ['lines'] )
            $ended = TRUE;
          continue;
        }
      }

      if ( $ended ) {
        $problems [] = 'has more after the empty line that ends the tune - only the first tune is drawn';
        break;
      }

      // The header: fields up to K:, which starts the music - or the first line that is no
      // field, for a tune written without one.

      if ( ! $music and preg_match ( '/^([A-Za-z]):(.*)$/', $line, $m ) ) {

        $value = trim ( $m [2] );

        switch ( $m [1] ) {
          case 'T': if ( $value !== '' ) $tune ['titles'] [] = $value; break;
          case 'C': if ( $value !== '' ) $tune ['composer'] [] = $value; break;
          case 'M': $tune ['meter'] = padAbcMeter ( $value, $problems ); break;
          case 'L': $unit = padAbcUnit ( $value, $problems ); break;
          case 'Q': $tune ['tempo'] = padAbcTempo ( $value, $problems ); break;
          case 'K': $tune ['key'] = padAbcKey ( $value, $problems ); $music = TRUE; break;
        }

        continue;

      }

      if ( ! $music )
        $music = TRUE;

      $state ??= [ 'unit' => $unit ?? padAbcDefaultUnit ( $tune ['meter'] ), 'key' => $tune ['key'], 'meter' => $tune ['meter'],
                   'bar' => [], 'tied' => [], 'tuplet' => NULL, 'broken' => NULL, 'tuplets' => 0, 'slurs' => 0, 'skipped' => 0 ];

      // A field between the lines of music: K:, M: and L: change what follows, the others
      // - lyrics, parts, voices, notes - are left out.

      if ( preg_match ( '/^([A-Za-z]):(.*)$/', $line, $m ) ) {

        $value = trim ( $m [2] );

        if ( $m [1] == 'K' )
          $state ['key'] = padAbcKey ( $value, $problems );
        elseif ( $m [1] == 'M' ) {
          $state ['meter'] = padAbcMeter ( $value, $problems );
          $meter = TRUE;
        } elseif ( $m [1] == 'L' )
          $state ['unit'] = padAbcUnit ( $value, $problems ) ?? $state ['unit'];

        continue;

      }

      if ( str_ends_with ( $line, '\\' ) ) {
        $join .= substr ( $line, 0, -1 );
        continue;
      }

      // The key and meter the line starts in, before an inline field in it changes them.

      $key   = $state ['key'];
      $start = $state ['meter'];
      $items = padAbcMusic ( $line, $state, $problems );

      if ( $items ) {
        $tune ['lines'] [] = [ 'items' => $items, 'key' => $key, 'meter' => $meter ? $start : NULL ];
        $meter = FALSE;
      }

      foreach ( $items as $item )
        if ( in_array ( $item ['kind'], [ 'note', 'rest', 'mrest' ] ) )
          $tune ['notes']++;

    }

    $problems = array_values ( array_unique ( $problems ) );

    return $tune;

  }

  // The lengths of a line read as note values - whole to thirty-second, dotted once or twice
  // - once a broken rhythm can change them no more, and the time each takes: a tuplet's notes
  // less than they are written. A length that is no note value is a problem, put in the list
  // where the note was read.

  function padAbcValues ( &$items, &$problems ) {

    $added = 0;

    foreach ( $items as $k => $item ) {

      if ( $item ['kind'] != 'note' and $item ['kind'] != 'rest' )
        continue;

      $value = padAbcValue ( $item ['len'] );

      if ( $value === NULL ) {
        array_splice ( $problems, $item ['mark'] + $added++, 0, [ 'has a length that is no single note: ' . $item ['len'] [0] . '/' . $item ['len'] [1] . ' - tie two notes instead' ] );
        $value = padAbcNearest ( $item ['len'] );
      }

      $time = $item ['len'] [0] / $item ['len'] [1];

      if ( $item ['tuplet'] )
        $time = $time * $item ['tuplet'] ['q'] / $item ['tuplet'] ['p'];

      $items [$k] ['base'] = $value [0];
      $items [$k] ['dots'] = $value [1];
      $items [$k] ['time'] = $time;

    }

  }

  // One line of music read into its events. $st is what carries over from line to line: the
  // unit length, the key, the meter, the accidentals of the bar so far, the pitches tied into
  // the next note, a tuplet still taking notes and a broken rhythm waiting for its second note.

  function padAbcMusic ( $text, &$st, &$problems ) {

    $items = [];
    $n     = strlen ( $text );
    $i     = 0;
    $gap   = TRUE;
    $last  = NULL;
    $wait  = padAbcWait ();

    while ( $i < $n ) {

      $c = $text [$i];

      // A space breaks the beams: notes written together are beamed together.

      if ( $c == ' ' or $c == "\t" ) {
        $gap = TRUE;
        $i++;
        continue;
      }

      // A quoted text belongs to the next note: a chord name, or with ^ _ < > @ first an
      // annotation above, below, left or right of it.

      if ( $c == '"' ) {

        $end = strpos ( $text, '"', $i + 1 );

        if ( $end === FALSE ) {
          $problems [] = 'has a quoted text that never closes';
          break;
        }

        $quote = substr ( $text, $i + 1, $end - $i - 1 );

        if ( $quote !== '' and str_contains ( '^_<>@', $quote [0] ) )
          $wait ['texts'] [] = [ $quote [0], trim ( substr ( $quote, 1 ) ) ];
        elseif ( trim ( $quote ) !== '' )
          $wait ['chord'] = trim ( $wait ['chord'] . ' ' . trim ( $quote ) );

        $i = $end + 1;
        continue;

      }

      // A decoration, !name! or the older +name+.

      if ( $c == '!' or $c == '+' ) {

        $end = strpos ( $text, $c, $i + 1 );

        if ( $end === FALSE ) {
          $problems [] = "has a decoration '" . padAbcShow ( substr ( $text, $i, 12 ) ) . "' that never closes";
          break;
        }

        $name = strtolower ( substr ( $text, $i + 1, $end - $i - 1 ) );
        $wait ['deco'] [] = [ '>' => 'accent', 'emphasis' => 'accent', 'tr' => 'trill' ] [$name] ?? $name;
        $i = $end + 1;
        continue;

      }

      // Grace notes are read past and not drawn.

      if ( $c == '{' ) {

        $end = strpos ( $text, '}', $i );

        if ( $end === FALSE ) {
          $problems [] = 'has grace notes that never close';
          break;
        }

        $i = $end + 1;
        continue;

      }

      // A tuplet - (3 is three notes in the time of two, (p:q:r p notes in the time of q for
      // the next r - or else a slur starting at the next note.

      if ( $c == '(' ) {

        if ( preg_match ( '/\G\((\d+)(?::(\d*))?(?::(\d*))?/', $text, $m, 0, $i ) ) {

          $p = (int) $m [1];
          $q = ( ( $m [2] ?? '' ) !== '' ) ? (int) $m [2] : padAbcTupletTime ( $p, $st ['meter'] );
          $r = ( ( $m [3] ?? '' ) !== '' ) ? (int) $m [3] : $p;

          if ( $p < 2 or $p > 9 or $q < 1 or $q > 9 or $r < 1 or $r > 9 )
            $problems [] = "has a tuplet '" . padAbcShow ( $m [0] ) . "' it cannot read - (2 to (9, or (p:q:r";
          else
            $st ['tuplet'] = [ 'id' => ++$st ['tuplets'], 'p' => $p, 'q' => $q, 'left' => $r, 'count' => $r ];

          $i += strlen ( $m [0] );
          continue;

        }

        // A slur inside PAD_ABC_SLURS open ones is left out, and the ) that closes it too.

        if ( $st ['slurs'] >= PAD_ABC_SLURS ) {
          $problems [] = 'has slurs nested deeper than ' . PAD_ABC_SLURS;
          $st ['skipped']++;
        } else {
          $st ['slurs']++;
          $wait ['slur']++;
        }

        $i++;
        continue;

      }

      if ( $c == ')' ) {
        if ( $st ['skipped'] > 0 )
          $st ['skipped']--;
        elseif ( $last !== NULL ) {
          $items [$last] ['slurEnd']++;
          $st ['slurs'] = max ( 0, $st ['slurs'] - 1 );
        }
        $i++;
        continue;
      }

      // A tie: the note before it is held into the next.

      if ( $c == '-' ) {

        $k = array_key_last ( $items );

        if ( $k !== NULL and $k === $last and $items [$k] ['kind'] == 'note' ) {
          foreach ( $items [$k] ['heads'] as $h => $head ) {
            $items [$k] ['heads'] [$h] ['tie'] = TRUE;
            $st ['tied'] [ $head ['step'] ] = $head ['alter'];
          }
        } else
          $problems [] = "has a tie '-' with no note before it";

        $i++;
        continue;

      }

      // Broken rhythm: A>B is A dotted and B halved, A>>B A double dotted and B a quarter.

      if ( $c == '>' or $c == '<' ) {

        $count = strspn ( $text, $c, $i );

        if ( $last === NULL or $count > 3 )
          $problems [] = "has a broken rhythm '" . str_repeat ( $c, $count ) . "' with no note before it";
        else {
          $long  = [ 2 ** ( $count + 1 ) - 1, 2 ** $count ];
          $short = [ 1, 2 ** $count ];
          $items [$last] ['len'] = padAbcMul ( $items [$last] ['len'], $c == '>' ? $long : $short );
          $st ['broken'] = ( $c == '>' ) ? $short : $long;
        }

        $i += $count;
        continue;

      }

      // A bar line: | || |] [| and the repeats |: :| :: - and right behind it the number of
      // an ending, |1 :|2 or |[1.

      if ( $c == '|' or $c == ':' or ( $c == '[' and ( $text [$i + 1] ?? '' ) == '|' ) ) {

        $left = strspn ( $text, ':', $i );
        $i   += $left;
        $core = '';

        foreach ( [ '[|', '|]', '||', '|' ] as $sign )
          if ( substr ( $text, $i, strlen ( $sign ) ) === $sign ) {
            $core = $sign;
            $i   += strlen ( $sign );
            break;
          }

        $right = strspn ( $text, ':', $i );
        $i    += $right;

        if ( $core === '' and $left >= 2 ) {
          $core  = '|';
          $right = 1;
        } elseif ( $core === '' ) {
          $problems [] = "has a ':' it cannot read";
          continue;
        }

        $ending = '';

        if ( preg_match ( '/\G\[?([1-9]\d*(?:[,\-][1-9]\d*)*)/', $text, $m, 0, $i ) ) {
          $ending = $m [1];
          $i     += strlen ( $m [0] );
        }

        $items [] = [ 'kind' => 'bar', 'core' => $core, 'end' => $left > 0, 'start' => $right > 0, 'ending' => $ending ];

        $st ['bar']    = [];
        $st ['broken'] = NULL;
        $gap  = TRUE;
        $last = NULL;
        continue;

      }

      if ( $c == '[' ) {

        // An inline field: [K:D], [M:3/4] and [L:1/16] change what follows.

        if ( preg_match ( '/\G\[([A-Za-z]):([^\]]*)\]/', $text, $m, 0, $i ) ) {

          $value = trim ( $m [2] );

          if ( $m [1] == 'K' ) {
            $from = $st ['key'];
            $st ['key'] = padAbcKey ( $value, $problems );
            $items [] = [ 'kind' => 'key', 'key' => $st ['key'], 'from' => $from ];
          } elseif ( $m [1] == 'M' ) {
            $st ['meter'] = padAbcMeter ( $value, $problems );
            if ( $st ['meter'] ['kind'] != 'none' )
              $items [] = [ 'kind' => 'meter', 'meter' => $st ['meter'] ];
          } elseif ( $m [1] == 'L' )
            $st ['unit'] = padAbcUnit ( $value, $problems ) ?? $st ['unit'];

          $gap = TRUE;
          $i  += strlen ( $m [0] );
          continue;

        }

        // An ending after a space: [1 - it belongs to the bar line just before.

        if ( preg_match ( '/\G\[([1-9]\d*(?:[,\-][1-9]\d*)*)/', $text, $m, 0, $i ) ) {

          $k = array_key_last ( $items );

          if ( $k !== NULL and $items [$k] ['kind'] == 'bar' and $items [$k] ['ending'] === '' )
            $items [$k] ['ending'] = $m [1];
          else
            $items [] = [ 'kind' => 'ending', 'ending' => $m [1] ];

          $i += strlen ( $m [0] );
          continue;

        }

        // A chord: notes on one stem, its length behind the bracket or on its first note.

        $close = strpos ( $text, ']', $i );

        if ( $close === FALSE ) {
          $problems [] = "has a chord '" . padAbcShow ( substr ( $text, $i, 12 ) ) . "' that never closes";
          break;
        }

        $inner = substr ( $text, $i + 1, $close - $i - 1 );
        $heads  = [];
        $first  = NULL;
        $j      = 0;
        $unread = FALSE;

        while ( $j < strlen ( $inner ) ) {

          if ( preg_match ( '/\G(\^\^|\^|__|_|=)?([A-Ga-g])([,\']*)([0-9\/]*)(-?)/', $inner, $m, 0, $j ) ) {

            $head = padAbcHead ( $m [1], $m [2], $m [3], $st );
            $len  = padAbcLength ( $m [4] );

            if ( $len === NULL )
              $problems [] = "has a note length '" . padAbcShow ( $m [2] . $m [3] . $m [4] ) . "' it cannot read";

            $first ??= $len ?? [ 1, 1 ];
            $head ['tie'] = ( $m [5] === '-' );
            $heads [] = $head;
            $j += strlen ( $m [0] );

          } else {

            // A chord is named once, whatever it holds that is no note: named for every such
            // character, it was cut short as often - time with the square of its length.

            if ( ! ctype_space ( $inner [$j] ) and ! $unread ) {
              $problems [] = "has a chord '[" . padAbcShow ( $inner ) . "]' it cannot read";
              $unread = TRUE;
            }

            $j++;

          }

        }

        $i = $close + 1;
        preg_match ( '/\G[0-9\/]*/', $text, $m, 0, $i );
        $outer = padAbcLength ( $m [0] );
        $i    += strlen ( $m [0] );

        if ( $outer === NULL ) {
          $problems [] = "has a chord length '" . padAbcShow ( $m [0] ) . "' it cannot read";
          $outer = [ 1, 1 ];
        }

        if ( ! $heads ) {
          $problems [] = 'has a chord with no notes';
          continue;
        }

        $items [] = padAbcEvent ( 'note', padAbcStack ( $heads ), padAbcMul ( $first, $outer ), $gap, $wait, $st, count ( $problems ) );

        foreach ( $heads as $head )
          if ( $head ['tie'] )
            $st ['tied'] [ $head ['step'] ] = $head ['alter'];

        $last = array_key_last ( $items );
        $gap  = FALSE;
        $wait = padAbcWait ();
        continue;

      }

      // A note: accidental, letter, octave marks, length.

      if ( preg_match ( '/\G(\^\^|\^|__|_|=)?([A-Ga-g])([,\']*)([0-9\/]*)/', $text, $m, 0, $i ) ) {

        $head = padAbcHead ( $m [1], $m [2], $m [3], $st );
        $len  = padAbcLength ( $m [4] );

        if ( $len === NULL ) {
          $problems [] = "has a note length '" . padAbcShow ( $m [0] ) . "' it cannot read";
          $len = [ 1, 1 ];
        }

        $items [] = padAbcEvent ( 'note', [ $head ], $len, $gap, $wait, $st, count ( $problems ) );
        $last = array_key_last ( $items );
        $gap  = FALSE;
        $wait = padAbcWait ();
        $i   += strlen ( $m [0] );
        continue;

      }

      // A rest: z, or x that takes the time and draws nothing.

      if ( preg_match ( '/\G([zx])([0-9\/]*)/', $text, $m, 0, $i ) ) {

        $len = padAbcLength ( $m [2] );

        if ( $len === NULL ) {
          $problems [] = "has a rest length '" . padAbcShow ( $m [0] ) . "' it cannot read";
          $len = [ 1, 1 ];
        }

        $event = padAbcEvent ( 'rest', [], $len, $gap, $wait, $st, count ( $problems ) );
        $event ['invisible'] = ( $m [1] == 'x' );
        $items [] = $event;
        $last = array_key_last ( $items );
        $gap  = TRUE;
        $wait = padAbcWait ();
        $i   += strlen ( $m [0] );
        continue;

      }

      // A rest of whole bars: Z, Z4 for four.

      if ( preg_match ( '/\G([ZX])(\d*)/', $text, $m, 0, $i ) ) {

        $items [] = [ 'kind' => 'mrest', 'bars' => max ( 1, min ( 999, (int) ( $m [2] ?: 1 ) ) ), 'invisible' => ( $m [1] == 'X' ),
                      'chord' => $wait ['chord'], 'texts' => $wait ['texts'], 'deco' => $wait ['deco'], 'gap' => TRUE ];
        $last = NULL;
        $gap  = TRUE;
        $wait = padAbcWait ();
        $i   += strlen ( $m [0] );
        continue;

      }

      // The decorations written as one character.

      if ( isset ( PAD_ABC_DECORATIONS [$c] ) ) {
        $wait ['deco'] [] = PAD_ABC_DECORATIONS [$c];
        $i++;
        continue;
      }

      // Spacers, the backquote that keeps notes beamed and the score line break of ABC 2.2
      // change nothing in the drawing.

      if ( $c == 'y' or $c == '`' or $c == '$' or $c == '\\' ) {
        $i++;
        continue;
      }

      // One character - a byte when the text is no UTF-8 there.

      if ( ! preg_match ( '/\G./su', $text, $m, 0, $i ) )
        $m = [ $c ];

      if ( $c == '&' )
        $problems [] = "has a voice overlay '&' it cannot draw";
      else
        $problems [] = "has a '" . padAbcShow ( $m [0] ) . "' it cannot read";

      $i += max ( 1, strlen ( $m [0] ) );

    }

    padAbcValues ( $items, $problems );

    return $items;

  }

  // What waits for the next note: its chord name, annotations, decorations and the slurs
  // that start at it.

  function padAbcWait () {

    return [ 'chord' => '', 'texts' => [], 'deco' => [], 'slur' => 0 ];

  }

  // A note or rest as an event: its length the unit times what was written times a broken
  // rhythm waiting for it, its place in a tuplet, what waited for it, and how many problems
  // came before it - where a problem with its length goes in the list.

  function padAbcEvent ( $kind, $heads, $len, $gap, $wait, &$st, $mark ) {

    $len = padAbcMul ( padAbcMul ( $st ['unit'], $len ), $st ['broken'] ?? [ 1, 1 ] );

    $event = [ 'kind' => $kind, 'heads' => $heads, 'len' => $len, 'gap' => $gap, 'chord' => $wait ['chord'],
               'texts' => $wait ['texts'], 'deco' => $wait ['deco'], 'slur' => $wait ['slur'], 'slurEnd' => 0,
               'tuplet' => NULL, 'invisible' => FALSE, 'mark' => $mark ];

    if ( $st ['tuplet'] ) {

      $t = $st ['tuplet'];
      $event ['tuplet'] = [ 'id' => $t ['id'], 'p' => $t ['p'], 'q' => $t ['q'],
                            'first' => $t ['left'] == $t ['count'], 'last' => $t ['left'] == 1 ];

      $st ['tuplet'] ['left']--;

      if ( $st ['tuplet'] ['left'] <= 0 )
        $st ['tuplet'] = NULL;

    }

    $st ['broken'] = NULL;
    $st ['tied']   = [];

    return $event;

  }

  // A note head: its step, letter and octave, the accidental written and what the pitch is
  // altered by. An accidental holds for that pitch to the end of the bar; a note tied from
  // the one before keeps that one's pitch; else the key signature decides.

  function padAbcHead ( $acc, $letter, $marks, &$st ) {

    $upper  = strtoupper ( $letter );
    $octave = ( $letter === $upper ? 4 : 5 ) + substr_count ( $marks, "'" ) - substr_count ( $marks, ',' );
    $step   = $octave * 7 + strpos ( PAD_ABC_LETTERS, $upper );

    if ( $acc !== '' ) {
      $alter = PAD_ABC_ALTER [$acc];
      $st ['bar'] [$step] = $alter;
    } elseif ( isset ( $st ['tied'] [$step] ) )
      $alter = $st ['tied'] [$step];
    elseif ( isset ( $st ['bar'] [$step] ) )
      $alter = $st ['bar'] [$step];
    else
      $alter = $st ['key'] ['acc'] [$upper] ?? 0;

    return [ 'step' => $step, 'letter' => $upper, 'octave' => $octave, 'acc' => $acc, 'alter' => $alter, 'tie' => FALSE ];

  }

  // The heads of a chord from low to high, the same pitch written twice once.

  function padAbcStack ( $heads ) {

    usort ( $heads, fn ( $a, $b ) => $a ['step'] <=> $b ['step'] );

    $stack = [];

    foreach ( $heads as $head )
      if ( ! $stack or end ( $stack ) ['step'] != $head ['step'] or end ( $stack ) ['alter'] != $head ['alter'] )
        $stack [] = $head;

    return $stack;

  }

  // A length behind a note as a fraction of the unit: 2, 3/2, /, //, /4. NULL when it is none.

  function padAbcLength ( $text ) {

    if ( $text === '' )
      return [ 1, 1 ];

    if ( ! preg_match ( '/^(\d*)(\/*)(\d*)$/D', $text, $m ) or ( $m [2] === '' and $m [3] !== '' ) )
      return NULL;

    $num = ( $m [1] === '' ) ? 1 : (int) $m [1];

    if ( $m [2] === '' )
      $den = 1;
    elseif ( $m [3] !== '' )
      $den = ( strlen ( $m [2] ) == 1 ) ? (int) $m [3] : 0;
    else
      $den = 2 ** strlen ( $m [2] );

    if ( $num < 1 or $num > 64 or $den < 1 or $den > 64 )
      return NULL;

    return padAbcMul ( [ $num, $den ], [ 1, 1 ] );

  }

  // A fraction of a whole note as a note value: [ 4, 1 ] is a dotted quarter. NULL when no
  // single note - whole to thirty-second, dotted once or twice - is that long.

  function padAbcValue ( $len ) {

    foreach ( [ 1, 2, 4, 8, 16, 32 ] as $base )
      for ( $dots = 0; $dots <= 2; $dots++ )
        if ( $len [0] * $base * 2 ** $dots == ( 2 ** ( $dots + 1 ) - 1 ) * $len [1] )
          return [ $base, $dots ];

    return NULL;

  }

  // The longest single note that is not longer, the shortest when every one is.

  function padAbcNearest ( $len ) {

    $best  = [ 32, 0 ];
    $value = 0;

    foreach ( [ 1, 2, 4, 8, 16, 32 ] as $base )
      for ( $dots = 0; $dots <= 2; $dots++ ) {
        $v = ( 2 ** ( $dots + 1 ) - 1 ) / ( $base * 2 ** $dots );
        if ( $v * $len [1] <= $len [0] and $v > $value ) {
          $best  = [ $base, $dots ];
          $value = $v;
        }
      }

    return $best;

  }

  // Two fractions [ numerator, denominator ] multiplied, and reduced.

  function padAbcMul ( $a, $b ) {

    $num = $a [0] * $b [0];
    $den = $a [1] * $b [1];
    $gcd = padAbcGcd ( $num, $den );

    return [ intdiv ( $num, $gcd ), intdiv ( $den, $gcd ) ];

  }

  // The greatest common divisor, 1 at least so a fraction can always be divided by it.

  function padAbcGcd ( $a, $b ) {

    while ( $b )
      list ( $a, $b ) = [ $b, $a % $b ];

    return max ( 1, abs ( $a ) );

  }

  // The time of a tuplet when it is not written: three in the time of two, two in the time
  // of three, four in three, six in two, eight in three, and 5, 7 and 9 in three in a compound
  // meter, else in two.

  function padAbcTupletTime ( $p, $meter ) {

    if ( in_array ( $p, [ 2, 4, 8 ] ) )
      return 3;

    if ( in_array ( $p, [ 3, 6 ] ) )
      return 2;

    $compound = ( $meter ['kind'] == 'num' and $meter ['beats'] % 3 == 0 and $meter ['beats'] > 3 );

    return $compound ? 3 : 2;

  }

  // The key of a K: field: [ fifths, acc, name, none ] - acc the alteration of each letter.
  // A tonic with # or b and a mode - m, min, dor, Dorian, mix ... the first three letters
  // count - then clef=treble or the like.

  function padAbcKey ( $value, &$problems ) {

    $none  = [ 'fifths' => 0, 'acc' => [], 'name' => '', 'none' => TRUE ];
    $words = preg_split ( '/\s+/', trim ( $value ), -1, PREG_SPLIT_NO_EMPTY );
    $key   = NULL;

    if ( $words and preg_match ( '/^(none)$/i', $words [0] ) ) {
      array_shift ( $words );
      $key = $none;
    } elseif ( $words and ! str_contains ( $words [0], '=' ) and ! padAbcClef ( $words [0] ) ) {

      $tonic = array_shift ( $words );

      if ( $words and ! str_contains ( $words [0], '=' ) and preg_match ( '/^[a-z]{3,}$/i', $words [0] ) and ! padAbcClef ( $words [0] ) )
        $tonic .= array_shift ( $words );

      if ( preg_match ( '/^([A-Ga-g])([#b]?)([A-Za-z]*)$/', $tonic, $m ) ) {

        $mode = strtolower ( $m [3] );
        $mode = ( $mode === '' ) ? 'maj' : ( ( $mode === 'm' ) ? 'min' : substr ( $mode, 0, 3 ) );

        if ( isset ( PAD_ABC_MODES [$mode] ) and ( strlen ( $m [3] ) <= 1 or strlen ( $m [3] ) >= 3 ) ) {

          $letter = strtoupper ( $m [1] );
          $fifths = PAD_ABC_FIFTHS [$letter] + ( $m [2] == '#' ? 7 : ( $m [2] == 'b' ? -7 : 0 ) ) + PAD_ABC_MODES [$mode] [0];

          if ( abs ( $fifths ) <= 7 ) {
            $acc = [];
            foreach ( array_slice ( $fifths > 0 ? PAD_ABC_SHARPS : PAD_ABC_FLATS, 0, abs ( $fifths ) ) as list ( $l ) )
              $acc [$l] = ( $fifths > 0 ) ? 1 : -1;
            $key = [ 'fifths' => $fifths, 'acc' => $acc, 'none' => FALSE,
                     'name' => $letter . ( $m [2] == '#' ? ' sharp' : ( $m [2] == 'b' ? ' flat' : '' ) ) . ' ' . PAD_ABC_MODES [$mode] [1] ];
          }

        }

      }

      if ( $key === NULL ) {
        $problems [] = "has no key '" . padAbcShow ( $tonic ) . "' - a key is C, G, D, F, Bb, Am, Dmix ...";
        $key = padAbcKey ( 'C', $problems );
      }

    } else
      $key = padAbcKey ( 'C', $problems );

    // What follows the key: a clef - only the treble clef is drawn - or other settings of
    // the staff, which change nothing here; the accidentals of a key written out are not read.

    foreach ( $words as $word ) {

      $clef = str_starts_with ( strtolower ( $word ), 'clef=' ) ? substr ( $word, 5 ) : ( padAbcClef ( $word ) ? $word : NULL );

      if ( $clef !== NULL ) {
        if ( ! preg_match ( '/^((treble|g|g2)([+-]8)?|none|perc)?$/i', $clef ) )
          $problems [] = "has a clef '" . padAbcShow ( $clef ) . "' it cannot draw - only the treble clef";
      } elseif ( preg_match ( '/^(\^\^|\^|__|_|=)[a-g]$/i', $word ) or strtolower ( $word ) == 'exp' )
        $problems [] = "has a key part '" . padAbcShow ( $word ) . "' it cannot read - write the accidentals at the notes";

    }

    return $key;

  }

  // Whether a word of a K: field names a clef - the bare names ABC allows - and not a key.

  function padAbcClef ( $word ) {

    return (bool) preg_match ( '/^(treble|bass|alto|tenor|baritone|soprano|mezzo|perc|none|bass3|alto1|alto2|g|f|c)\d?$/i', $word )
           and ! preg_match ( '/^[A-G]$/', $word );

  }

  // The meter of an M: field: [ kind, top, bottom, beats, value ] - kind num, C, C| or none.
  // A number of it has at most three digits: the beats of 9999999999999999999+9999999999999999999
  // added up past what an integer holds, and the page ended with PHP's warning that the float
  // was no int.

  function padAbcMeter ( $value, &$problems ) {

    $value = trim ( $value );

    if ( $value === '' or strtolower ( $value ) === 'none' )
      return [ 'kind' => 'none' ];

    if ( $value === 'C' or $value === 'C|' )
      return [ 'kind' => $value, 'top' => $value == 'C' ? '4' : '2', 'bottom' => $value == 'C' ? '4' : '2', 'beats' => $value == 'C' ? 4 : 2, 'value' => 1 ];

    if ( preg_match ( '/^\(?\s*(\d{1,3}(?:\s*\+\s*\d{1,3})*)\s*\)?\s*\/\s*(\d{1,3})$/', $value, $m ) and (int) $m [2] > 0 ) {
      $top   = preg_replace ( '/\s+/', '', $m [1] );
      $beats = array_sum ( array_map ( 'intval', explode ( '+', $top ) ) );
      return [ 'kind' => 'num', 'top' => $top, 'bottom' => $m [2], 'beats' => $beats, 'value' => $beats / (int) $m [2] ];
    }

    $problems [] = "has a meter '" . padAbcShow ( $value ) . "' it cannot read - a meter is 4/4, 3/4, 6/8, C, C| or none";

    return [ 'kind' => 'none' ];

  }

  // The unit note length of an L: field, NULL when it is none.

  function padAbcUnit ( $value, &$problems ) {

    if ( preg_match ( '/^1\s*\/\s*(1|2|4|8|16|32|64)$/', trim ( $value ), $m ) )
      return [ 1, (int) $m [1] ];

    if ( trim ( $value ) === '1' )
      return [ 1, 1 ];

    $problems [] = "has a unit note length '" . padAbcShow ( $value ) . "' it cannot read - L: is 1/8, 1/4 or 1/16";

    return NULL;

  }

  // The unit note length when there is no L: field: a sixteenth when the meter is less than
  // 3/4, else an eighth.

  function padAbcDefaultUnit ( $meter ) {

    return ( $meter ['kind'] == 'num' and $meter ['value'] < 0.75 ) ? [ 1, 16 ] : [ 1, 8 ];

  }

  // The tempo of a Q: field: [ text, len, bpm ] - "Allegro" 1/4=120, 1/4=120, 120 (the beat
  // a quarter), "Andante".

  function padAbcTempo ( $value, &$problems ) {

    $value = trim ( $value );
    $text  = '';

    while ( preg_match ( '/^(.*?)"([^"]*)"(.*)$/s', $value, $m ) ) {
      $text  = trim ( $text . ' ' . $m [2] );
      $value = trim ( $m [1] . ' ' . $m [3] );
    }

    if ( $value === '' and $text !== '' )
      return [ 'text' => $text, 'len' => NULL, 'bpm' => '' ];

    if ( preg_match ( '/^(\d+)$/', $value, $m ) )
      return [ 'text' => $text, 'len' => [ 1, 4 ], 'bpm' => $m [1] ];

    if ( preg_match ( '/^((?:\d+\/\d+\s*)+)=\s*(\d+)$/', $value, $m ) ) {

      $len = [ 0, 1 ];

      foreach ( preg_split ( '/\s+/', trim ( $m [1] ) ) as $part ) {
        list ( $num, $den ) = array_map ( 'intval', explode ( '/', $part ) );
        if ( $num < 1 or $den < 1 or $den > 64 )
          return padAbcTempoProblem ( $value, $problems );
        $len = padAbcMul ( [ $len [0] * $den + $num * $len [1], $len [1] * $den ], [ 1, 1 ] );
      }

      return [ 'text' => $text, 'len' => $len, 'bpm' => $m [2] ];

    }

    return padAbcTempoProblem ( $value, $problems );

  }

  // A Q: field it cannot read: a problem, and no tempo.

  function padAbcTempoProblem ( $value, &$problems ) {

    $problems [] = "has a tempo '" . padAbcShow ( $value ) . "' it cannot read - Q: is 1/4=120";

    return NULL;

  }

  // ------------------------------------------------------------------------------------------
  // Laying out
  // ------------------------------------------------------------------------------------------

  // A line made ready to lay out: the beam groups, the direction of every stem, the room
  // every event takes, and the start of the line - clef, key signature, time signature.

  function padAbcPrepare ( $line ) {

    $items = $line ['items'];

    // The beam groups: two or more eighths or shorter, written together.

    $groups = [];
    $run    = [];

    foreach ( $items as $k => $item ) {

      $beamable = ( $item ['kind'] == 'note' and $item ['base'] >= 8 );

      if ( $beamable and $run and ! $item ['gap'] and end ( $run ) == $k - 1 )
        $run [] = $k;
      else {
        if ( count ( $run ) > 1 )
          $groups [] = $run;
        $run = $beamable ? [ $k ] : [];
      }

    }

    if ( count ( $run ) > 1 )
      $groups [] = $run;

    // The stems: up for notes below the middle line, down from it, a chord and a beam group
    // going by the note farthest from it.

    foreach ( $items as $k => $item )
      if ( $item ['kind'] == 'note' )
        $items [$k] ['dir'] = padAbcDirection ( [ $item ] );

    foreach ( $groups as $g => $group ) {
      $dir = padAbcDirection ( array_map ( fn ( $k ) => $items [$k], $group ) );
      foreach ( $group as $k ) {
        $items [$k] ['dir']   = $dir;
        $items [$k] ['group'] = $g;
      }
    }

    // The room of every event: left and right of its place, and the space its time asks.

    foreach ( $items as $k => $item ) {

      switch ( $item ['kind'] ) {

        case 'note':
          padAbcHeads ( $items [$k] );
          $items [$k] ['spring'] = padAbcSpring ( $item ['time'] );
          break;

        case 'rest':
          $items [$k] ['lead']   = 6;
          $items [$k] ['trail']  = 6 + ( $item ['dots'] ? 4 + $item ['dots'] * 5.5 : 0 );
          $items [$k] ['spring'] = padAbcSpring ( $item ['time'] );
          break;

        case 'mrest':
          $items [$k] ['lead']   = $item ['bars'] > 1 ? 26 : 16;
          $items [$k] ['trail']  = $items [$k] ['lead'];
          $items [$k] ['spring'] = 2 * $items [$k] ['lead'] + 10;
          break;

        case 'bar':
          list ( $items [$k] ['parts'], $items [$k] ['width'] ) = padAbcBarParts ( $item );
          $items [$k] ['hidden'] = ( $k == 0 and ! $item ['start'] and ( $item ['core'] == '|' or $item ['core'] == '||' ) );
          if ( $items [$k] ['hidden'] )
            $items [$k] ['width'] = 0;
          break;

        case 'key':
          $items [$k] ['width'] = padAbcKeyWidth ( $item ['key'], $item ['from'] ) + 4;
          break;

        case 'meter':
          $items [$k] ['width'] = padAbcMeterWidth ( $item ['meter'] ) + 6;
          break;

        default:
          $items [$k] ['width'] = 0;

      }

    }

    // The start of the line: the clef, the key signature, the time signature on the first
    // line and after a change.

    $x     = PAD_ABC_LEFT + 33;
    $keyX  = $x;
    $x    += padAbcKeyWidth ( $line ['key'], NULL );
    $meter = ( $line ['meter'] and $line ['meter'] ['kind'] != 'none' ) ? $line ['meter'] : NULL;
    $at    = NULL;

    if ( $meter ) {
      $w  = padAbcMeterWidth ( $meter );
      $at = $x + 5 + $w / 2;
      $x += $w + 7;
    }

    return [ 'items' => $items, 'groups' => $groups, 'prefix' => $x, 'key' => $line ['key'], 'keyX' => $keyX,
             'meter' => $meter, 'meterX' => $at ];

  }

  // The direction of the stems of notes: up when the note farthest from the middle line is
  // below it, down when it is above it or on it.

  function padAbcDirection ( $notes ) {

    $low  = PHP_INT_MAX;
    $high = PHP_INT_MIN;

    foreach ( $notes as $note )
      foreach ( $note ['heads'] as $head ) {
        $low  = min ( $low, $head ['step'] );
        $high = max ( $high, $head ['step'] );
      }

    return ( PAD_ABC_MIDDLE - $low > $high - PAD_ABC_MIDDLE ) ? 'up' : 'down';

  }

  // The heads of a note or chord set out: a head a second from the one before goes to the
  // other side of the stem, the accidentals stand in columns left of the heads so that none
  // collide, the dots in a column right of them. Sets lead and trail, the room left and right.

  function padAbcHeads ( &$item ) {

    $up    = ( $item ['dir'] == 'up' );
    $whole = ( $item ['base'] == 1 );
    $half  = $whole ? 8.5 : PAD_ABC_HEAD;
    $shift = $whole ? 17 : 2 * PAD_ABC_STEM_X;
    $heads = $item ['heads'];
    $order = array_keys ( $heads );

    usort ( $order, fn ( $a, $b ) => $up ? $heads [$a] ['step'] <=> $heads [$b] ['step'] : $heads [$b] ['step'] <=> $heads [$a] ['step'] );

    $prev  = NULL;
    $moved = FALSE;
    $left  = 0;
    $right = 0;

    foreach ( $order as $h ) {

      $step = $heads [$h] ['step'];
      $move = ( $prev !== NULL and abs ( $step - $prev ) <= 1 and ! $moved );

      $item ['heads'] [$h] ['dx'] = $move ? ( $up ? $shift : - $shift ) : 0;

      if ( $move and $up )
        $right = $shift;
      elseif ( $move )
        $left = $shift;

      $prev  = $step;
      $moved = $move;

    }

    // The accidentals from the top down, each in the first column where it clears the others.

    $columns = [];
    $widths  = [];
    $top     = array_keys ( $heads );

    usort ( $top, fn ( $a, $b ) => $heads [$b] ['step'] <=> $heads [$a] ['step'] );

    foreach ( $top as $h ) {

      $acc = $heads [$h] ['acc'];

      if ( $acc === '' )
        continue;

      $step = $heads [$h] ['step'];

      for ( $c = 0; isset ( $columns [$c] ); $c++ ) {
        $clear = TRUE;
        foreach ( $columns [$c] as $other )
          if ( abs ( $other - $step ) < 6 )
            $clear = FALSE;
        if ( $clear )
          break;
      }

      $columns [$c] [] = $step;
      $widths  [$c]    = max ( $widths [$c] ?? 0, PAD_ABC_ACC [$acc] [1] );
      $item ['heads'] [$h] ['col'] = $c;

    }

    $edge = $half + $left + 2.5;
    $at   = [];

    foreach ( $widths as $c => $w ) {
      $at [$c] = - ( $edge + $w / 2 );
      $edge   += $w + 1;
    }

    foreach ( $item ['heads'] as $h => $head )
      if ( isset ( $head ['col'] ) )
        $item ['heads'] [$h] ['ax'] = $at [ $head ['col'] ];

    $item ['lead'] = $widths ? $edge : $half + $left;

    // The dots right of the heads - beyond the flags of a short note with its stem up - and
    // the flag of a lone note with its stem up.

    $flags = ( $item ['base'] >= 8 and ! isset ( $item ['group'] ) and $up );
    $trail = $half + $right;

    if ( $item ['dots'] ) {
      $item ['dotX'] = $half + $right + 3.8 + ( ( $flags and $item ['base'] >= 16 ) ? 6 : 0 );
      $trail = $item ['dotX'] + 1.8 + ( $item ['dots'] - 1 ) * 5.5;
    }

    if ( $flags )
      $trail = max ( $trail, PAD_ABC_STEM_X + 11 );

    $item ['trail'] = $trail;

  }

  // The space a note's time asks for, from one note to the next: growing with the square
  // root of the time, as engravers space notes - a quarter 30, an eighth 23, a half 40.

  function padAbcSpring ( $time ) {

    return 6 + 24 * sqrt ( max ( 0.0, $time * 4 ) );

  }

  // The parts of a bar line from left to right - t a thin line, T a thick one, : repeat dots,
  // each at its middle - and its width. A repeat back ends thin and thick, a repeat forward
  // starts thick and thin, the two together are thin, thick, thin.

  function padAbcBarParts ( $bar ) {

    $core = [ '|' => [ 't' ], '||' => [ 't', 't' ], '|]' => [ 't', 'T' ], '[|' => [ 'T', 't' ] ] [ $bar ['core'] ];

    if ( $bar ['core'] == '|' and $bar ['end'] and $bar ['start'] )
      $core = [ 't', 'T', 't' ];
    elseif ( $bar ['core'] == '|' and $bar ['end'] )
      $core = [ 't', 'T' ];
    elseif ( $bar ['core'] == '|' and $bar ['start'] )
      $core = [ 'T', 't' ];

    $parts = [];
    $x     = 0;

    if ( $bar ['end'] ) {
      $parts [] = [ ':', 1.8 ];
      $x = 6.2;
    }

    foreach ( $core as $index => $part ) {
      $x += $index ? 2.6 : 0;
      $w  = ( $part == 'T' ) ? 4 : 1.2;
      $parts [] = [ $part, $x + $w / 2 ];
      $x += $w;
    }

    if ( $bar ['start'] ) {
      $parts [] = [ ':', $x + 4.4 ];
      $x += 6.2;
    }

    return [ $parts, $x ];

  }

  // The accidentals of a key signature as [ glyph, step ] - after naturals for those of the
  // key before that the new one has not - and the room they take.

  function padAbcKeyGlyphs ( $key, $from ) {

    $glyphs = [];

    if ( $from )
      foreach ( padAbcKeyList ( $from ) as list ( $letter, $step, $alter ) )
        if ( ( $key ['acc'] [$letter] ?? 0 ) != $alter )
          $glyphs [] = [ 'natural', $step ];

    foreach ( padAbcKeyList ( $key ) as list ( $letter, $step, $alter ) )
      $glyphs [] = [ $alter > 0 ? 'sharp' : 'flat', $step ];

    return $glyphs;

  }

  // The sharps or flats of a key as [ letter, step, alteration ], in the order they are written.

  function padAbcKeyList ( $key ) {

    $list = [];

    foreach ( array_slice ( $key ['fifths'] > 0 ? PAD_ABC_SHARPS : PAD_ABC_FLATS, 0, abs ( $key ['fifths'] ) ) as list ( $letter, $step ) )
      $list [] = [ $letter, $step, $key ['fifths'] > 0 ? 1 : -1 ];

    return $list;

  }

  // The room a key signature takes, with the naturals of a change and a gap behind it.

  function padAbcKeyWidth ( $key, $from ) {

    $w = 0;

    foreach ( padAbcKeyGlyphs ( $key, $from ) as list ( $glyph ) )
      $w += [ 'sharp' => 9.4, 'flat' => 8.4, 'natural' => 7.4 ] [$glyph];

    return $w ? $w + 3 : 0;

  }

  // The room a time signature takes: the C, or the wider of its two numbers.

  function padAbcMeterWidth ( $meter ) {

    if ( $meter ['kind'] == 'C' or $meter ['kind'] == 'C|' )
      return 17;

    return max ( padAbcWidth ( $meter ['top'], 27, TRUE ), padAbcWidth ( $meter ['bottom'], 27, TRUE ) );

  }

  // The places of the events of a line: x of every event - a note's head, a bar line's left
  // edge - with the spaces of the notes' times stretched by $factor, and the width of the
  // line. Nothing comes closer to the event before than the room both take; a chord name
  // keeps clear of the one before.

  function padAbcPlace ( $prep, $factor ) {

    $items = $prep ['items'];
    $xs    = [];
    $x     = $prep ['prefix'];
    $prev  = NULL;
    $chord = -1000;

    foreach ( $items as $k => $item ) {

      $kind = $item ['kind'];

      if ( $kind == 'note' or $kind == 'rest' or $kind == 'mrest' ) {

        if ( $prev !== NULL )
          $at = max ( $xs [$prev] + $items [$prev] ['spring'] * $factor, $x + $item ['lead'] + 3 );
        else
          $at = $x + $item ['lead'] + 9;

        if ( $item ['chord'] !== '' ) {
          $at    = max ( $at, $chord + 10 );
          $chord = $at - 4 + padAbcWidth ( padAbcChordName ( $item ['chord'] ), 13 );
        }

        $xs [$k] = $at;
        $x       = $at + $item ['trail'];
        $prev    = $k;

      } elseif ( $kind == 'bar' ) {

        if ( $prev !== NULL )
          $at = max ( $x + 5, $xs [$prev] + $items [$prev] ['spring'] * $factor - 6 );
        else
          $at = $x + ( $item ['width'] ? 4 : 0 );

        $xs [$k] = $at;
        $x       = $at + $item ['width'];
        $prev    = NULL;

      } else {

        $xs [$k] = $x + ( $item ['width'] ? 4 : 0 );
        $x       = $xs [$k] + $item ['width'];

        if ( $item ['width'] )
          $prev = NULL;

      }

    }

    if ( $prev !== NULL )
      $x = max ( $x + 6, $xs [$prev] + $items [$prev] ['spring'] * $factor );

    return [ $xs, $x + PAD_ABC_RIGHT ];

  }

  // A line spread to a width: the spaces of the times stretched by the factor that makes it
  // that wide, found by halving, and a closing bar line put at the very end.

  function padAbcFit ( $prep, $target ) {

    list ( $xs, $end ) = padAbcPlace ( $prep, 1 );

    if ( $end >= $target - 0.01 )
      return [ $xs, $end ];

    $low  = 1;
    $high = 2;

    while ( $high < 64 and padAbcPlace ( $prep, $high ) [1] < $target )
      $high *= 2;

    for ( $round = 0; $round < 40; $round++ ) {
      $mid = ( $low + $high ) / 2;
      if ( padAbcPlace ( $prep, $mid ) [1] < $target )
        $low = $mid;
      else
        $high = $mid;
    }

    $xs = padAbcPlace ( $prep, $high ) [0];
    $k  = array_key_last ( $prep ['items'] );

    if ( $k !== NULL and $prep ['items'] [$k] ['kind'] == 'bar' )
      $xs [$k] = $target - PAD_ABC_RIGHT - $prep ['items'] [$k] ['width'];

    return [ $xs, $target ];

  }

  // ------------------------------------------------------------------------------------------
  // Drawing
  // ------------------------------------------------------------------------------------------

  // A line drawn with the top line of its staff at y 0: [ svg, top, bottom ] - top and bottom
  // how far what it draws reaches above and below. $carry holds what goes on into the next
  // line: ties, slurs and an ending still open.

  function padAbcDraw ( &$d, $prep, $xs, $end, &$carry ) {

    $d ['staff']  = '';
    $d ['lines']  = '';
    $d ['fill']   = '';
    $d ['text']   = '';
    $d ['top']    = 0;
    $d ['bottom'] = 40;

    $items = $prep ['items'];
    $right = $end - PAD_ABC_RIGHT;

    // The staff, the clef, the key signature and the time signature.

    for ( $line = 0; $line < 5; $line++ )
      $d ['staff'] .= 'M' . PAD_ABC_LEFT . ' ' . ( $line * 10 ) . 'H' . padAbcNum ( $right );

    padAbcUse ( $d, 'clef', PAD_ABC_LEFT + 3, 0 );
    padAbcKeyDraw ( $d, $prep ['key'], NULL, $prep ['keyX'] );

    if ( $prep ['meter'] )
      padAbcMeterDraw ( $d, $prep ['meter'], $prep ['meterX'] );

    // The events, then the beams over the groups, then what goes over or under them.

    $geo = [];

    foreach ( $items as $k => $item ) {

      $x = $xs [$k];

      switch ( $item ['kind'] ) {
        case 'note':  $geo [$k] = padAbcNote ( $d, $item, $x ); break;
        case 'rest':  $geo [$k] = padAbcRest ( $d, $item, $x ); break;
        case 'mrest': $geo [$k] = padAbcBarRest ( $d, $item, $x ); break;
        case 'bar':   if ( ! $item ['hidden'] ) padAbcBar ( $d, $item, $x ); break;
        case 'key':   padAbcKeyDraw ( $d, $item ['key'], $item ['from'], $x ); break;
        case 'meter': padAbcMeterDraw ( $d, $item ['meter'], $x + ( $item ['width'] - 6 ) / 2 ); break;
      }

    }

    foreach ( $prep ['groups'] as $group )
      padAbcBeam ( $d, $items, $group, $xs, $geo );

    padAbcTies ( $d, $items, $xs, $geo, $prep ['prefix'], $right, $carry );
    padAbcSlurs ( $d, $items, $xs, $geo, $prep ['prefix'], $right, $carry );
    padAbcTuplets ( $d, $items, $xs, $geo );
    padAbcDecorations ( $d, $items, $xs, $geo );

    // Above the staff: the chord names on one line clear of the notes, the brackets of the
    // endings above them, the annotations above those; below it the annotations below.

    $chords = [];
    $above  = [];
    $below  = [];

    foreach ( $items as $k => $item ) {

      if ( ! isset ( $geo [$k] ) )
        continue;

      if ( $item ['chord'] !== '' )
        $chords [] = [ $xs [$k] - 4, padAbcChordName ( $item ['chord'] ) ];

      foreach ( $item ['texts'] as list ( $where, $text ) )
        if ( $where == '_' )
          $below [] = [ $xs [$k] - 4, $text ];
        elseif ( $where == '<' )
          $d ['text'] .= padAbcTextAt ( $xs [$k] - $item ['lead'] - 2, $geo [$k] ['mid'] + 4, $text, 12, ' text-anchor="end"' );
        elseif ( $where == '>' )
          $d ['text'] .= padAbcTextAt ( $xs [$k] + $item ['trail'] + 2, $geo [$k] ['mid'] + 4, $text, 12 );
        else
          $above [] = [ $xs [$k] - 4, $text ];

    }

    $edge = min ( 0, $d ['top'] );

    if ( $chords ) {
      $chordY = min ( -5, $d ['top'] - 5 );
      foreach ( $chords as list ( $x, $name ) )
        $d ['text'] .= padAbcTextAt ( $x, $chordY, $name, 13 );
      $edge = $chordY - 11;
    }

    $brackets = padAbcEndings ( $items, $xs, $prep ['prefix'], $right, $carry );

    if ( $brackets ) {

      $endY = min ( -14, $edge - 13 );

      foreach ( $brackets as list ( $x1, $x2, $label, $open, $close ) ) {
        $d ['lines'] .= 'M' . padAbcNum ( $x1 ) . ' ' . padAbcNum ( $open ? $endY + 10 : $endY ) . 'V' . padAbcNum ( $endY )
                      . 'H' . padAbcNum ( $x2 ) . ( $close ? 'V' . padAbcNum ( $endY + 10 ) : '' );
        if ( $label !== '' )
          $d ['text'] .= padAbcTextAt ( $x1 + 3, $endY + 10, $label . '.', 11 );
      }

      $edge = $endY - 1;

    }

    if ( $above ) {
      $annY = min ( -5, $edge - 4 );
      foreach ( $above as list ( $x, $text ) )
        $d ['text'] .= padAbcTextAt ( $x, $annY, $text, 12 );
      $edge = $annY - 11;
    }

    if ( $below ) {
      $belowY = max ( 40, $d ['bottom'] ) + 13;
      foreach ( $below as list ( $x, $text ) )
        $d ['text'] .= padAbcTextAt ( $x, $belowY, $text, 12 );
      $d ['bottom'] = $belowY + 4;
    }

    // The clef reaches a space and a half above the staff and below it; it counts for the
    // room of the line, not for where the chord names go.

    $top = min ( $d ['top'], $edge, -15 ) - 2;

    $svg = '<path d="' . $d ['staff'] . '" fill="none" stroke="currentColor" stroke-width="1"/>';

    if ( $d ['lines'] !== '' )
      $svg .= '<path d="' . $d ['lines'] . '" fill="none" stroke="currentColor" stroke-width="1.2"/>';

    return [ 'svg' => $svg . $d ['fill'] . $d ['text'], 'top' => $top, 'bottom' => max ( $d ['bottom'], 57 ) + 2 ];

  }

  // The y of a step: the top line F5 at 0, every step down 5 more.

  function padAbcY ( $step ) {

    return ( PAD_ABC_TOP - $step ) * 5;

  }

  // Widens how far the line reaches up and down to take in what is drawn from $top to $bottom.

  function padAbcSpan ( &$d, $top, $bottom ) {

    $d ['top']    = min ( $d ['top'], $top );
    $d ['bottom'] = max ( $d ['bottom'], $bottom );

  }

  // A glyph placed: a <use> of its path in the <defs>, which is written when one is used.

  function padAbcUse ( &$d, $glyph, $x, $y ) {

    $d ['used'] [$glyph] = TRUE;
    $d ['fill'] .= '<use href="#' . padAbcText ( $d ['id'] . '-' . $glyph ) . '" x="' . padAbcNum ( $x ) . '" y="' . padAbcNum ( $y ) . '"/>';

  }

  // A dot: of a dotted note, a staccato, a repeat sign.

  function padAbcDot ( &$d, $x, $y, $r = 1.8 ) {

    $d ['fill'] .= '<circle cx="' . padAbcNum ( $x ) . '" cy="' . padAbcNum ( $y ) . '" r="' . padAbcNum ( $r ) . '"/>';

  }

  // A text at a place, in the size given; the font is the drawing's, system-ui.

  function padAbcTextAt ( $x, $y, $text, $size, $extra = '' ) {

    return '<text x="' . padAbcNum ( $x ) . '" y="' . padAbcNum ( $y ) . '" font-size="' . $size . '"' . $extra . '>' . padAbcText ( $text ) . '</text>';

  }

  // A key signature from x on - after the naturals that cancel the key before it, when one
  // changes.

  function padAbcKeyDraw ( &$d, $key, $from, $x ) {

    foreach ( padAbcKeyGlyphs ( $key, $from ) as list ( $glyph, $step ) ) {
      $w = [ 'sharp' => 9.4, 'flat' => 8.4, 'natural' => 7.4 ] [$glyph];
      padAbcUse ( $d, $glyph, $x + $w / 2, padAbcY ( $step ) );
      $x += $w;
    }

  }

  // A time signature: the C of common time, the C with a stroke of cut time, or the two
  // numbers, each as high as half the staff.

  function padAbcMeterDraw ( &$d, $meter, $x ) {

    if ( $meter ['kind'] == 'C' or $meter ['kind'] == 'C|' ) {
      padAbcUse ( $d, 'common', $x, 20 );
      if ( $meter ['kind'] == 'C|' )
        $d ['fill'] .= '<path d="M' . padAbcNum ( $x - 0.7 ) . ' 5h1.4v30h-1.4z"/>';
      return;
    }

    foreach ( [ 'top' => 20, 'bottom' => 40 ] as $part => $y )
      $d ['text'] .= padAbcTextAt ( $x, $y - 0.5, $meter [$part], 27, ' font-weight="bold" text-anchor="middle"' );

  }

  // A note or chord: its heads, ledger lines, accidentals and dots, and when it is not beamed
  // its stem and flags. Answers its geometry: the stem's x and tip, the heads nearest and
  // farthest from the tip, the head of every step, and how far it reaches up and down.

  function padAbcNote ( &$d, $item, $x ) {

    $up     = ( $item ['dir'] == 'up' );
    $glyph  = [ 1 => 'whole', 2 => 'half' ] [ $item ['base'] ] ?? 'black';
    $steps  = array_column ( $item ['heads'], 'step' );
    $low    = min ( $steps );
    $high   = max ( $steps );
    $dxs    = array_column ( $item ['heads'], 'dx' );
    $wide   = ( $item ['base'] == 1 ) ? 11.5 : 9.5;
    $from   = $x - $wide + min ( 0, ...$dxs );
    $to     = $x + $wide + max ( 0, ...$dxs );
    $top    = padAbcY ( $high ) - 5;
    $bottom = padAbcY ( $low ) + 5;

    // Ledger lines, every second step from the staff out to the farthest head.

    for ( $s = PAD_ABC_TOP - 10; $s >= $low; $s -= 2 )
      $d ['lines'] .= 'M' . padAbcNum ( $from ) . ' ' . padAbcY ( $s ) . 'H' . padAbcNum ( $to );

    for ( $s = PAD_ABC_TOP + 2; $s <= $high; $s += 2 )
      $d ['lines'] .= 'M' . padAbcNum ( $from ) . ' ' . padAbcY ( $s ) . 'H' . padAbcNum ( $to );

    // The heads and their accidentals; then the dots, a head on a line having its dot in the
    // space above, two heads never sharing one.

    $heads = [];
    $dots  = [];

    foreach ( $item ['heads'] as $head ) {

      $hx = $x + $head ['dx'];
      $hy = padAbcY ( $head ['step'] );
      $heads [ $head ['step'] ] = [ $hx, $hy ];

      padAbcUse ( $d, $glyph, $hx, $hy );

      if ( $head ['acc'] !== '' ) {
        $ax = $x + $head ['ax'];
        if ( $head ['acc'] == '__' ) {
          padAbcUse ( $d, 'flat', $ax - 3.2, $hy );
          padAbcUse ( $d, 'flat', $ax + 3.2, $hy );
        } else
          padAbcUse ( $d, PAD_ABC_ACC [ $head ['acc'] ] [0], $ax, $hy );
        $reach = [ '^' => [ 13, 13 ], '^^' => [ 5, 5 ], '=' => [ 12, 12 ], '_' => [ 17, 6 ], '__' => [ 17, 6 ] ] [ $head ['acc'] ];
        $top    = min ( $top, $hy - $reach [0] );
        $bottom = max ( $bottom, $hy + $reach [1] );
      }

    }

    if ( $item ['dots'] ) {

      $byTop = $steps;
      rsort ( $byTop );

      foreach ( $byTop as $step ) {
        $y = padAbcY ( $step ) - ( ( $step % 2 == 0 ) ? 5 : 0 );
        while ( in_array ( $y, $dots ) )
          $y += 10;
        $dots [] = $y;
        for ( $n = 0; $n < $item ['dots']; $n++ )
          padAbcDot ( $d, $x + $item ['dotX'] + $n * 5.5, $y );
      }

    }

    $geo = [ 'x' => $x, 'up' => $up, 'heads' => $heads, 'sx' => NULL, 'tip' => NULL, 'mid' => padAbcY ( ( $low + $high ) / 2 ),
             'near' => padAbcY ( $up ? $high : $low ), 'far' => padAbcY ( $up ? $low : $high ),
             'high' => padAbcY ( $high ), 'low' => padAbcY ( $low ) ];

    // The stem and flags of a note that is not beamed; a whole note has none. A stem is three
    // and a half spaces, longer with more flags, and reaches the middle line at least.

    if ( $item ['base'] >= 2 ) {

      $geo ['sx'] = $x + ( $up ? PAD_ABC_STEM_X : - PAD_ABC_STEM_X );

      if ( ! isset ( $item ['group'] ) ) {

        $levels = padAbcLevels ( $item ['base'] );
        $length = PAD_ABC_STEM + max ( 0, $levels - 1 ) * 6;
        $tip    = $up ? min ( $geo ['near'] - $length, 20 ) : max ( $geo ['near'] + $length, 20 );

        $d ['lines'] .= 'M' . padAbcNum ( $geo ['sx'] ) . ' ' . padAbcNum ( $geo ['far'] + ( $up ? -1.1 : 1.1 ) ) . 'V' . padAbcNum ( $tip );

        for ( $n = 0; $n < $levels; $n++ )
          padAbcUse ( $d, $up ? 'flagU' : 'flagD', $geo ['sx'], $tip + ( $up ? $n : - $n ) * PAD_ABC_BEAM_GAP );

        $geo ['tip'] = $tip;
        $top    = min ( $top, $tip );
        $bottom = max ( $bottom, $tip );

      }

    }

    $geo ['top']    = $top;
    $geo ['bottom'] = $bottom;

    padAbcSpan ( $d, $top, $bottom );

    return $geo;

  }

  // The number of flags or beams of a note value: an eighth one, a sixteenth two.

  function padAbcLevels ( $base ) {

    return [ 8 => 1, 16 => 2, 32 => 3 ] [$base] ?? 0;

  }

  // A rest: its shape at its place on the staff and its dots in the third space. An x rest
  // takes its place and draws nothing.

  function padAbcRest ( &$d, $item, $x ) {

    $reach = PAD_ABC_RESTS [ $item ['base'] ];
    $geo   = [ 'x' => $x, 'up' => TRUE, 'heads' => [], 'sx' => NULL, 'tip' => NULL, 'mid' => 20, 'near' => 20, 'far' => 20,
               'high' => $reach [0], 'low' => $reach [1], 'top' => $reach [0], 'bottom' => $reach [1], 'rest' => TRUE ];

    if ( $item ['invisible'] )
      return $geo;

    padAbcUse ( $d, 'r' . $item ['base'], $x, 0 );

    for ( $n = 0; $n < $item ['dots']; $n++ )
      padAbcDot ( $d, $x + 8 + ( $item ['base'] >= 16 ? 2 : 0 ) + $n * 5.5, 15 );

    return $geo;

  }

  // A rest of whole bars: one bar the whole rest in the middle of the bar, more the thick bar
  // with the number of bars above the staff.

  function padAbcBarRest ( &$d, $item, $x ) {

    $geo = [ 'x' => $x, 'up' => TRUE, 'heads' => [], 'sx' => NULL, 'tip' => NULL, 'mid' => 20, 'near' => 20, 'far' => 20,
             'high' => 10, 'low' => 30, 'top' => 10, 'bottom' => 30, 'rest' => TRUE ];

    if ( $item ['invisible'] )
      return $geo;

    if ( $item ['bars'] == 1 ) {
      padAbcUse ( $d, 'r1', $x, 0 );
      return $geo;
    }

    $d ['fill']  .= '<path d="M' . padAbcNum ( $x - 20 ) . ' 16.5h40v7h-40z"/>';
    $d ['lines'] .= 'M' . padAbcNum ( $x - 20 ) . ' 10V30M' . padAbcNum ( $x + 20 ) . ' 10V30';
    $d ['text']  .= padAbcTextAt ( $x, -5, (string) $item ['bars'], 15, ' font-weight="bold" text-anchor="middle"' );
    padAbcSpan ( $d, -17, 40 );
    $geo ['top'] = -17;

    return $geo;

  }

  // A bar line through the staff: thin and thick lines and repeat dots.

  function padAbcBar ( &$d, $item, $x ) {

    foreach ( $item ['parts'] as list ( $part, $at ) )
      if ( $part == 't' )
        $d ['lines'] .= 'M' . padAbcNum ( $x + $at ) . ' 0V40';
      elseif ( $part == 'T' )
        $d ['fill'] .= '<path d="M' . padAbcNum ( $x + $at - 2 ) . ' 0h4v40h-4z"/>';
      else {
        padAbcDot ( $d, $x + $at, 15 );
        padAbcDot ( $d, $x + $at, 25 );
      }

  }

  // The beam of a group, with its stems. All stems go one way; the beam slants with the
  // first and last note, at most half a space a note and never more than a space and a quarter,
  // and lies flat when a note inside reaches past both ends. It sits so that the stem nearest
  // to it is long enough, and reaches the middle line. A sixteenth gets a second beam, joined
  // to a sixteenth beside it or a short stub, pointing into the group, where it has none.

  function padAbcBeam ( &$d, $items, $group, $xs, &$geo ) {

    $up     = ( $items [ $group [0] ] ['dir'] == 'up' );
    $n      = count ( $group );
    $sx     = [];
    $near   = [];
    $levels = [];

    foreach ( $group as $i => $k ) {
      $sx     [$i] = $geo [$k] ['sx'];
      $near   [$i] = $geo [$k] ['near'];
      $levels [$i] = padAbcLevels ( $items [$k] ['base'] );
    }

    $length = PAD_ABC_STEM - 3 + 6 * ( max ( $levels ) - 1 );
    $rise   = ( $near [$n - 1] - $near [0] ) / 2;
    $limit  = min ( 5 * ( $n - 1 ), 12.5 );
    $rise   = max ( - $limit, min ( $limit, $rise ) );

    for ( $i = 1; $i < $n - 1; $i++ )
      if ( $up ? $near [$i] < min ( $near [0], $near [$n - 1] ) : $near [$i] > max ( $near [0], $near [$n - 1] ) )
        $rise = 0;

    $span  = $sx [$n - 1] - $sx [0];
    $slope = ( $span > 0 ) ? $rise / $span : 0;
    $base  = $up ? INF : -INF;

    for ( $i = 0; $i < $n; $i++ ) {
      $want = $near [$i] + ( $up ? - $length : $length ) - $slope * ( $sx [$i] - $sx [0] );
      $base = $up ? min ( $base, $want ) : max ( $base, $want );
    }

    $ends = [ $base, $base + $slope * $span ];

    if ( $up and max ( $ends ) > 20 )
      $base -= max ( $ends ) - 20;
    elseif ( ! $up and min ( $ends ) < 20 )
      $base += 20 - min ( $ends );

    $at = fn ( $x ) => $base + $slope * ( $x - $sx [0] );

    foreach ( $group as $i => $k ) {
      $tip = $at ( $sx [$i] );
      $d ['lines'] .= 'M' . padAbcNum ( $sx [$i] ) . ' ' . padAbcNum ( $geo [$k] ['far'] + ( $up ? -1.1 : 1.1 ) ) . 'V' . padAbcNum ( $tip );
      $geo [$k] ['tip']    = $tip;
      $geo [$k] ['top']    = min ( $geo [$k] ['top'], $tip );
      $geo [$k] ['bottom'] = max ( $geo [$k] ['bottom'], $tip );
    }

    // The beams: the first over the whole group, every next one between the notes that have
    // it, a stub where a note has it alone.

    $starts = [ 0 ];

    for ( $i = 1; $i < $n; $i++ )
      $starts [$i] = $starts [$i - 1] + $items [ $group [$i - 1] ] ['time'];

    for ( $level = 0; $level < max ( $levels ); $level++ ) {

      $shift = $level * PAD_ABC_BEAM_GAP;

      for ( $i = 0; $i < $n; $i++ ) {

        if ( $levels [$i] <= $level )
          continue;

        if ( $i + 1 < $n and $levels [$i + 1] > $level ) {
          padAbcBeamBar ( $d, $sx [$i] - 0.6, $sx [$i + 1] + 0.6, $at, $up, $shift );
          continue;
        }

        if ( $i > 0 and $levels [$i - 1] > $level )
          continue;

        // A note alone on this level: a stub toward the note it belongs with - into the group
        // at either end, and inside it back to the note before when it does not start a beat
        // of its own value.

        $value = $items [ $group [$i] ] ['time'];
        $back  = ( $i == $n - 1 or ( $i > 0 and fmod ( $starts [$i], 2 * $value ) > 1e-9 ) );
        $other = $back ? $sx [$i - 1] : $sx [$i + 1];
        $stub  = min ( 9, abs ( $other - $sx [$i] ) * 0.45 );

        if ( $back )
          padAbcBeamBar ( $d, $sx [$i] - $stub, $sx [$i] + 0.6, $at, $up, $shift );
        else
          padAbcBeamBar ( $d, $sx [$i] - 0.6, $sx [$i] + $stub, $at, $up, $shift );

      }

    }

    padAbcSpan ( $d, min ( $at ( $sx [0] ), $at ( $sx [$n - 1] ) ), max ( $at ( $sx [0] ), $at ( $sx [$n - 1] ) ) );

  }

  // One beam between two x: a slanted bar as thick as a beam, its outer edge on the line of
  // the beam moved toward the heads by $shift.

  function padAbcBeamBar ( &$d, $x1, $x2, $at, $up, $shift ) {

    $y1 = $at ( $x1 ) + ( $up ? $shift : - $shift - PAD_ABC_BEAM );
    $y2 = $at ( $x2 ) + ( $up ? $shift : - $shift - PAD_ABC_BEAM );

    $d ['fill'] .= '<path d="M' . padAbcNum ( $x1 ) . ' ' . padAbcNum ( $y1 ) . 'L' . padAbcNum ( $x2 ) . ' ' . padAbcNum ( $y2 )
                 . 'V' . padAbcNum ( $y2 + PAD_ABC_BEAM ) . 'L' . padAbcNum ( $x1 ) . ' ' . padAbcNum ( $y1 + PAD_ABC_BEAM ) . 'z"/>';

  }

  // A curve between two points bulging by $h (negative is up), as thick as $t in the middle
  // and thin at the ends: a tie or a slur.

  function padAbcArc ( $x1, $y1, $x2, $y2, $h, $t ) {

    $c1 = $x1 + ( $x2 - $x1 ) * 0.25;
    $c2 = $x2 - ( $x2 - $x1 ) * 0.25;
    $i  = $h - ( $h < 0 ? - $t : $t );

    return '<path d="M' . padAbcNum ( $x1 ) . ' ' . padAbcNum ( $y1 )
         . 'C' . padAbcNum ( $c1 ) . ' ' . padAbcNum ( $y1 + $h ) . ' ' . padAbcNum ( $c2 ) . ' ' . padAbcNum ( $y2 + $h ) . ' ' . padAbcNum ( $x2 ) . ' ' . padAbcNum ( $y2 )
         . 'C' . padAbcNum ( $c2 ) . ' ' . padAbcNum ( $y2 + $i ) . ' ' . padAbcNum ( $c1 ) . ' ' . padAbcNum ( $y1 + $i ) . ' ' . padAbcNum ( $x1 ) . ' ' . padAbcNum ( $y1 ) . 'z"/>';

  }

  // The ties: from a tied head to the head of the same pitch in the next note, curving away
  // from the stem - in a chord the upper heads up and the lower down. A tie at the end of a
  // line runs to the end of the staff and goes on from the start of the next.

  function padAbcTies ( &$d, $items, $xs, $geo, $prefix, $right, &$carry ) {

    $notes = array_keys ( array_filter ( $items, fn ( $item ) => $item ['kind'] == 'note' ) );

    if ( $carry ['ties'] and $notes ) {
      $first = $geo [ $notes [0] ];
      foreach ( $carry ['ties'] as $step => $over )
        if ( isset ( $first ['heads'] [$step] ) ) {
          list ( $hx, $hy ) = $first ['heads'] [$step];
          padAbcTie ( $d, $prefix - 2, $hx - 5, $hy, $over );
        }
    }

    $carry ['ties'] = [];

    foreach ( $notes as $index => $k ) {

      $item = $items [$k];
      $next = $notes [$index + 1] ?? NULL;
      $count = count ( $item ['heads'] );

      foreach ( $item ['heads'] as $h => $head ) {

        if ( ! $head ['tie'] )
          continue;

        $over = ( $count == 1 or ( $count % 2 == 1 and $h == ( $count - 1 ) / 2 ) ) ? ! $geo [$k] ['up'] : ( $h >= $count / 2 );
        list ( $hx, $hy ) = $geo [$k] ['heads'] [ $head ['step'] ];

        if ( $next === NULL ) {
          padAbcTie ( $d, $hx + 5, $right - 2, $hy, $over );
          $carry ['ties'] [ $head ['step'] ] = $over;
          continue;
        }

        $to = $geo [$next] ['heads'] [ $head ['step'] ] [0] ?? $xs [$next];
        padAbcTie ( $d, $hx + 5, $to - 5, $hy, $over );

      }

    }

  }

  // One tie from x1 to x2 at the head's height, just over or under it, flatter when short.

  function padAbcTie ( &$d, $x1, $x2, $y, $over ) {

    if ( $x2 - $x1 < 4 )
      $x2 = $x1 + 4;

    $lift = min ( 6, 2.5 + ( $x2 - $x1 ) * 0.06 ) / 0.75;
    $y   += $over ? -5 : 5;

    $d ['fill'] .= padAbcArc ( $x1, $y, $x2, $y, $over ? - $lift : $lift, 2.2 );
    padAbcSpan ( $d, $y - ( $over ? $lift : 0 ), $y + ( $over ? 0 : $lift ) );

  }

  // The slurs: over the notes they bind, under them when every stem goes up, lifted to clear
  // the notes between. One open at the end of a line runs to its end and goes on in the next,
  // on the same side.

  function padAbcSlurs ( &$d, $items, $xs, $geo, $prefix, $right, &$carry ) {

    $open  = [];
    $notes = array_keys ( array_filter ( $items, fn ( $item ) => $item ['kind'] == 'note' or $item ['kind'] == 'rest' ) );
    $keys  = array_keys ( $geo );

    foreach ( $carry ['slurs'] as $over )
      $open [] = [ NULL, $over ];

    foreach ( $notes as $k ) {

      for ( $n = 0; $n < $items [$k] ['slurEnd'] and $open; $n++ ) {
        list ( $from, $over ) = array_pop ( $open );
        padAbcSlur ( $d, $xs, $geo, $keys, $from, $k, $over, $prefix, $right );
      }

      for ( $n = 0; $n < $items [$k] ['slur']; $n++ )
        $open [] = [ $k, NULL ];

    }

    $carry ['slurs'] = [];

    foreach ( $open as list ( $from, $over ) )
      $carry ['slurs'] [] = padAbcSlur ( $d, $xs, $geo, $keys, $from, NULL, $over, $prefix, $right );

  }

  // One slur from the note $from to the note $to - NULL for the start or end of the line -
  // over the notes or under them as $over says, or as its notes decide when it is NULL. It
  // answers the side it took. $keys are the keys of $geo, in order: the notes the slur spans
  // are found by halving them, where every key was tried for every slur - a line of slurs
  // took time with the square of its length.

  function padAbcSlur ( &$d, $xs, $geo, $keys, $from, $to, $over, $prefix, $right ) {

    $first = ( $from === NULL ) ? 0 : padAbcFirstKey ( $keys, $from );
    $after = ( $to === NULL ) ? count ( $keys ) : padAbcFirstKey ( $keys, $to + 1 );
    $span  = array_slice ( $keys, $first, max ( 0, $after - $first ) );

    if ( ! $span )
      return (bool) $over;

    if ( $over === NULL ) {
      $over = FALSE;
      foreach ( $span as $k )
        if ( ! $geo [$k] ['up'] and ! isset ( $geo [$k] ['rest'] ) )
          $over = TRUE;
    }

    // Where a slur meets a note: at the head, or at the tip of a stem on its side.

    $end = function ( $k ) use ( $geo, $over ) {
      $g = $geo [$k];
      if ( $over )
        return ( $g ['up'] and $g ['tip'] !== NULL ) ? [ $g ['sx'], $g ['tip'] - 4 ] : [ $g ['x'], $g ['high'] - 7 ];
      return ( ! $g ['up'] and $g ['tip'] !== NULL ) ? [ $g ['sx'], $g ['tip'] + 4 ] : [ $g ['x'], $g ['low'] + 7 ];
    };

    list ( $x1, $y1 ) = ( $from === NULL ) ? [ $prefix, $end ( $span [0] ) [1] ] : $end ( $from );
    list ( $x2, $y2 ) = ( $to === NULL ) ? [ $right - 2, $end ( end ( $span ) ) [1] ] : $end ( $to );

    if ( $x2 - $x1 < 6 )
      $x2 = $x1 + 6;

    // The lift: enough for the curve, three quarters of its handles high in the middle, to
    // clear every note between.

    $lift = min ( 12, 4 + ( $x2 - $x1 ) * 0.04 ) / 0.75;

    foreach ( $span as $k ) {
      if ( $k === $from or $k === $to )
        continue;
      $t = ( $geo [$k] ['x'] - $x1 ) / ( $x2 - $x1 );
      if ( $t <= 0.05 or $t >= 0.95 )
        continue;
      $line = $y1 + ( $y2 - $y1 ) * $t;
      $need = $over ? $line - ( $geo [$k] ['top'] - 4 ) : ( $geo [$k] ['bottom'] + 4 ) - $line;
      $lift = max ( $lift, $need / ( 3 * $t * ( 1 - $t ) ) );
    }

    $lift = min ( $lift, 40 );

    $d ['fill'] .= padAbcArc ( $x1, $y1, $x2, $y2, $over ? - $lift : $lift, 2.4 );
    padAbcSpan ( $d, min ( $y1, $y2 ) - ( $over ? 0.75 * $lift : 0 ), max ( $y1, $y2 ) + ( $over ? 0 : 0.75 * $lift ) );

    return $over;

  }

  // Where the first key of a sorted list that is $key or more stands: the count when none is.

  function padAbcFirstKey ( $keys, $key ) {

    $low  = 0;
    $high = count ( $keys );

    while ( $low < $high ) {
      $middle = ( $low + $high ) >> 1;
      if ( $keys [$middle] < $key )
        $low = $middle + 1;
      else
        $high = $middle;
    }

    return $low;

  }

  // The tuplets: the number over a beamed group that is the tuplet, else a bracket with the
  // number in the middle over its notes.

  function padAbcTuplets ( &$d, $items, $xs, &$geo ) {

    $sets  = [];
    $sizes = [];

    foreach ( $items as $k => $item ) {
      if ( ( $item ['tuplet'] ?? NULL ) and isset ( $geo [$k] ) )
        $sets [ $item ['tuplet'] ['id'] ] [] = $k;
      if ( ( $item ['group'] ?? NULL ) !== NULL )
        $sizes [ $item ['group'] ] = ( $sizes [ $item ['group'] ] ?? 0 ) + 1;
    }

    foreach ( $sets as $set ) {

      $first = $items [ $set [0] ];
      $p     = $first ['tuplet'] ['p'];
      $group = $first ['group'] ?? NULL;
      $beamed = ( $group !== NULL );

      foreach ( $set as $k )
        if ( ( $items [$k] ['group'] ?? NULL ) !== $group )
          $beamed = FALSE;

      // A beamed group that is the tuplet and nothing more - its size counted once for the
      // line, where every item of the line was counted for every tuplet.

      if ( $beamed )
        $beamed = ( ( $sizes [$group] ?? 0 ) == count ( $set ) );

      if ( $beamed ) {
        $a  = $geo [ $set [0] ];
        $b  = $geo [ end ( $set ) ];
        $x  = ( $a ['sx'] + $b ['sx'] ) / 2;
        $up = $a ['up'];
        $y  = $up ? ( $a ['tip'] + $b ['tip'] ) / 2 - 4 : ( $a ['tip'] + $b ['tip'] ) / 2 + 13;
        $d ['text'] .= padAbcTextAt ( $x, $y, (string) $p, 12, ' font-style="italic" font-weight="bold" text-anchor="middle"' );
        padAbcSpan ( $d, $y - 10, $y + 2 );
        continue;
      }

      $top = 0;

      foreach ( $set as $k )
        $top = min ( $top, $geo [$k] ['top'] );

      $y  = $top - 8;
      $x1 = $xs [ $set [0] ] - 6;
      $x2 = $xs [ end ( $set ) ] + 6;
      $mx = ( $x1 + $x2 ) / 2;

      $d ['lines'] .= 'M' . padAbcNum ( $x1 ) . ' ' . padAbcNum ( $y + 4 ) . 'V' . padAbcNum ( $y ) . 'H' . padAbcNum ( $mx - 6 )
                    . 'M' . padAbcNum ( $mx + 6 ) . ' ' . padAbcNum ( $y ) . 'H' . padAbcNum ( $x2 ) . 'V' . padAbcNum ( $y + 4 );
      $d ['text']  .= padAbcTextAt ( $mx, $y + 4, (string) $p, 12, ' font-style="italic" font-weight="bold" text-anchor="middle"' );
      padAbcSpan ( $d, $y - 6, $y );

    }

  }

  // The decorations drawn: a staccato dot and an accent on the side of the head away from
  // the stem, in a space when inside the staff; a fermata and a trill above. The others -
  // rolls, mordents, bowings, segno and coda - are read and not drawn.

  function padAbcDecorations ( &$d, $items, $xs, &$geo ) {

    foreach ( $items as $k => $item ) {

      if ( ! ( $item ['deco'] ?? [] ) or ! isset ( $geo [$k] ) )
        continue;

      $g     = $geo [$k];
      $x     = $xs [$k];
      $below = ( $g ['up'] and ! isset ( $g ['rest'] ) );
      $edge  = $below ? $g ['low'] : $g ['high'];

      foreach ( [ 'staccato', 'accent' ] as $mark ) {

        if ( ! in_array ( $mark, $item ['deco'] ) or isset ( $g ['rest'] ) )
          continue;

        $y = $edge + ( $below ? 9 : -9 ) + ( $mark == 'accent' ? ( $below ? 2 : -2 ) : 0 );

        if ( $y > 0 and $y < 40 and fmod ( $y, 10 ) != 5 )
          $y = $below ? ceil ( ( $y - 5 ) / 10 ) * 10 + 5 : floor ( ( $y - 5 ) / 10 ) * 10 + 5;

        if ( $mark == 'staccato' )
          padAbcDot ( $d, $x, $y, 1.5 );
        else
          $d ['lines'] .= 'M' . padAbcNum ( $x - 4.5 ) . ' ' . padAbcNum ( $y - 3 ) . 'L' . padAbcNum ( $x + 4.5 ) . ' ' . padAbcNum ( $y )
                        . 'L' . padAbcNum ( $x - 4.5 ) . ' ' . padAbcNum ( $y + 3 );

        $edge = $below ? $y + 4 : $y - 4;
        padAbcSpan ( $d, $y - 4, $y + 4 );

      }

      $top = min ( -4, $geo [$k] ['top'] - 4 );

      if ( in_array ( 'fermata', $item ['deco'] ) ) {
        padAbcUse ( $d, 'fermata', $x, $top );
        $top -= 13;
      }

      if ( in_array ( 'trill', $item ['deco'] ) ) {
        $d ['text'] .= padAbcTextAt ( $x - 6, $top - 1, 'tr', 13, ' font-style="italic" font-weight="bold"' );
        $top -= 13;
      }

      $geo [$k] ['top'] = min ( $geo [$k] ['top'], $top );
      padAbcSpan ( $d, $top, 40 );

    }

  }

  // The brackets of the endings in a line: [ x1, x2, label, left hook, right hook ]. A first
  // ending runs to the repeat that sends the player back, a double bar or the next ending;
  // a later one over the bar it starts, closed when a repeat ends it. One still open at the
  // end of the line runs to its end and goes on in the next.

  function padAbcEndings ( $items, $xs, $prefix, $right, &$carry ) {

    $brackets = [];
    $open     = $carry ['ending'];

    if ( $open )
      $open = [ 'x' => $prefix - 2, 'label' => '', 'hook' => FALSE, 'first' => $open ['first'] ];

    foreach ( $items as $k => $item ) {

      if ( $item ['kind'] != 'bar' and $item ['kind'] != 'ending' )
        continue;

      $x = $xs [$k];

      if ( $open and $item ['kind'] == 'bar' ) {

        $repeat = $item ['end'];
        $double = ( $item ['core'] != '|' );

        if ( $repeat or $double or $item ['ending'] !== '' or ! $open ['first'] ) {
          $brackets [] = [ $open ['x'], $x + ( $repeat ? 1 : 0 ), $open ['label'], $open ['hook'], $repeat ];
          $open = NULL;
        }

      } elseif ( $open ) {
        $brackets [] = [ $open ['x'], $x, $open ['label'], $open ['hook'], FALSE ];
        $open = NULL;
      }

      if ( $item ['ending'] !== '' ) {
        $start = ( $item ['kind'] == 'bar' ) ? $x + ( $item ['hidden'] ?? FALSE ? 0 : $item ['width'] - 1 ) : $x;
        if ( $item ['kind'] == 'bar' and $k == 0 )
          $start = $prefix + 2;
        $open = [ 'x' => $start, 'label' => $item ['ending'], 'hook' => TRUE, 'first' => str_contains ( ',' . str_replace ( '-', ',', $item ['ending'] ) . ',', ',1,' ) ];
      }

    }

    if ( $open )
      $brackets [] = [ $open ['x'], $right - 2, $open ['label'], $open ['hook'], FALSE ];

    $carry ['ending'] = $open;

    return $brackets;

  }

  // ------------------------------------------------------------------------------------------
  // The header and the description
  // ------------------------------------------------------------------------------------------

  // The title centred, a subtitle under it, then the tempo on the left and the composer on
  // the right. $y comes in as the top and goes out under the header.

  function padAbcHeader ( &$d, $tune, $full, &$y ) {

    $svg = '';

    foreach ( $tune ['titles'] as $index => $title ) {
      $size = $index ? 15 : 20;
      $y   += $size + 3;
      $svg .= padAbcTextAt ( $full / 2, $y, $title, $size, ( $index ? '' : ' font-weight="bold"' ) . ' text-anchor="middle"' );
      $y   += 3;
    }

    if ( $tune ['composer'] or $tune ['tempo'] ) {

      $y += 15;

      if ( $tune ['composer'] )
        $svg .= padAbcTextAt ( $full - PAD_ABC_RIGHT, $y, implode ( ', ', $tune ['composer'] ), 12, ' text-anchor="end"' );

      if ( $tune ['tempo'] )
        $svg .= padAbcTempoDraw ( $d, $tune ['tempo'], PAD_ABC_LEFT + 2, $y );

      $y += 4;

    }

    return $svg;

  }

  // The tempo: its text, then the note of the beat drawn small and = and the beats a minute.

  function padAbcTempoDraw ( &$d, $tempo, $x, $y ) {

    $svg = '';

    if ( $tempo ['text'] !== '' ) {
      $svg .= padAbcTextAt ( $x, $y, $tempo ['text'], 12, ' font-weight="bold"' );
      $x   += padAbcWidth ( $tempo ['text'], 12, TRUE ) + 6;
    }

    if ( ! $tempo ['len'] )
      return $svg;

    $value = padAbcValue ( $tempo ['len'] );

    if ( $value === NULL )
      return $svg . padAbcTextAt ( $x, $y, $tempo ['len'] [0] . '/' . $tempo ['len'] [1] . ' = ' . $tempo ['bpm'], 12 );

    list ( $base, $dots ) = $value;

    $glyph = [ 1 => 'whole', 2 => 'half' ] [$base] ?? 'black';
    $d ['used'] [$glyph] = TRUE;
    $id    = padAbcText ( $d ['id'] );
    $note  = '<use href="#' . $id . '-' . $glyph . '"/>';

    if ( $base >= 2 )
      $note .= '<path d="M5.7-1.1V-30" fill="none" stroke="currentColor" stroke-width="1.4"/>';

    for ( $n = 0; $n < padAbcLevels ( $base ); $n++ ) {
      $d ['used'] ['flagU'] = TRUE;
      $note .= '<use href="#' . $id . '-flagU" x="5.7" y="' . ( -30 + $n * 7.5 ) . '"/>';
    }

    for ( $n = 0; $n < $dots; $n++ )
      $note .= '<circle cx="' . padAbcNum ( 10.5 + $n * 5 ) . '" cy="0" r="2"/>';

    $svg .= '<g transform="translate(' . padAbcNum ( $x + 6 ) . ' ' . padAbcNum ( $y - 3.5 ) . ') scale(.6)">' . $note . '</g>';
    $x   += 6 + 6 + ( $base >= 8 ? 6 : 0 ) + $dots * 3.5 + 4;

    return $svg . padAbcTextAt ( $x, $y, '= ' . $tempo ['bpm'], 12 );

  }

  // The room the title and the composer and tempo line ask, so the drawing is at least as wide.

  function padAbcHeaderWidth ( $tune ) {

    $w = 0;

    foreach ( $tune ['titles'] as $index => $title )
      $w = max ( $w, padAbcWidth ( $title, $index ? 15 : 20, ! $index ) + 16 );

    $row = 0;

    if ( $tune ['composer'] )
      $row += padAbcWidth ( implode ( ', ', $tune ['composer'] ), 12 ) + 12;

    if ( $tune ['tempo'] )
      $row += padAbcWidth ( $tune ['tempo'] ['text'], 12, TRUE ) + 60;

    return max ( $w, $row );

  }

  // The tune in words for a screen reader: the title, the composer, the meter, the key, the
  // tempo, and then every bar with its notes - "bar 1: G 4 quarter, A 4 quarter, B 4 half" -
  // a note's name with its accidental and octave (C 4 is middle C), its value, tied, the
  // chord name above it, a tuplet's notes together, the repeats and endings at their bars.

  function padAbcDescribe ( $tune ) {

    $parts = [];

    if ( $tune ['titles'] )
      $parts [] = implode ( ', ', $tune ['titles'] );

    if ( $tune ['composer'] )
      $parts [] = 'By ' . implode ( ', ', $tune ['composer'] );

    $facts = [];
    $meter = padAbcSayMeter ( $tune ['meter'] );

    if ( $meter !== '' )
      $facts [] = "$meter time";

    if ( ! $tune ['key'] ['none'] and $tune ['notes'] )
      $facts [] = 'key of ' . $tune ['key'] ['name'];

    if ( $tune ['tempo'] ) {
      $tempo = $tune ['tempo'];
      $beat  = $tempo ['len'] ? padAbcValue ( $tempo ['len'] ) : NULL;
      $say   = $beat ? padAbcSayValue ( $beat [0], $beat [1] ) : ( $tempo ['len'] ? $tempo ['len'] [0] . '/' . $tempo ['len'] [1] : '' );
      $facts [] = 'tempo ' . trim ( $tempo ['text'] . ( ( $tempo ['text'] !== '' and $say !== '' ) ? ', ' : '' ) . ( $say !== '' ? "$say = " . $tempo ['bpm'] : '' ) );
    }

    if ( $facts )
      $parts [] = ucfirst ( implode ( ', ', $facts ) );

    // The bars: what is in each until a bar line, with what the bar lines say about repeats
    // and endings.

    $bars    = [];
    $words   = [];
    $number  = 1;
    $span    = 1;
    $label   = '';
    $tuplet  = NULL;
    $closing = function () use ( &$bars, &$words, &$number, &$span, &$label, &$tuplet ) {
      if ( $tuplet ) {
        $words [] = $tuplet;
        $tuplet = NULL;
      }
      if ( ! $words )
        return;
      $bars [] = ( $span > 1 ? "bars $number to " . ( $number + $span - 1 ) : "bar $number" ) . $label . ': ' . implode ( ', ', $words );
      $number += $span;
      $span    = 1;
      $label   = '';
      $words   = [];
    };

    foreach ( $tune ['lines'] as $line )
      foreach ( $line ['items'] as $item ) {

        switch ( $item ['kind'] ) {

          case 'note':
          case 'rest':
            if ( $item ['kind'] == 'rest' and $item ['invisible'] )
              break;
            $say = padAbcSayEvent ( $item );
            if ( $item ['tuplet'] ) {
              if ( $item ['tuplet'] ['first'] or ! $tuplet )
                $tuplet = ( $item ['tuplet'] ['p'] == 3 ? 'triplet of ' : 'tuplet of ' . $item ['tuplet'] ['p'] . ': ' ) . $say;
              else
                $tuplet .= ( $item ['tuplet'] ['last'] ? ' and ' : ', ' ) . $say;
              if ( $item ['tuplet'] ['last'] ) {
                $words [] = $tuplet;
                $tuplet = NULL;
              }
            } else
              $words [] = $say;
            break;

          case 'mrest':
            if ( $item ['invisible'] )
              break;
            $words [] = padAbcSayChord ( $item ) . ( $item ['bars'] > 1 ? 'rest for ' . $item ['bars'] . ' bars' : 'whole bar rest' );
            $span = $item ['bars'];
            break;

          case 'key':
            $words [] = 'key change to ' . ( $item ['key'] ['none'] ? 'no key' : $item ['key'] ['name'] );
            break;

          case 'meter':
            $words [] = 'meter change to ' . padAbcSayMeter ( $item ['meter'] );
            break;

          case 'bar':
            if ( $item ['end'] ) {
              if ( $words )
                $words [] = 'repeat back';
              elseif ( $bars )
                $bars [ array_key_last ( $bars ) ] .= ', repeat back';
            }
            $closing ();
            if ( $item ['start'] )
              $label .= ', repeat from here';
            if ( $item ['ending'] !== '' )
              $label .= ', ending ' . $item ['ending'];
            break;

          case 'ending':
            $label .= ', ending ' . $item ['ending'];
            break;

        }

      }

    $closing ();

    $text = implode ( '. ', $parts );

    if ( $bars )
      $text .= ( $text !== '' ? '. ' : '' ) . ucfirst ( implode ( '; ', $bars ) );

    return $text . '.';

  }

  // A meter in words: common, cut, 6/8 - empty for none.

  function padAbcSayMeter ( $meter ) {

    return [ 'C' => 'common', 'C|' => 'cut', 'num' => ( $meter ['top'] ?? '' ) . '/' . ( $meter ['bottom'] ?? '' ) ] [ $meter ['kind'] ] ?? '';

  }

  // A note value in words: quarter, dotted half, double dotted eighth.

  function padAbcSayValue ( $base, $dots ) {

    return [ '', 'dotted ', 'double dotted ' ] [$dots] . PAD_ABC_VALUES [$base];

  }

  // The chord name and annotations of a note in words, in front of the note itself.

  function padAbcSayChord ( $item ) {

    $say = '';

    if ( $item ['chord'] !== '' )
      $say .= 'chord ' . padAbcChordName ( $item ['chord'] ) . ', ';

    foreach ( $item ['texts'] as list ( $where, $text ) )
      if ( $text !== '' )
        $say .= '"' . $text . '", ';

    return $say;

  }

  // A note or rest in words: F sharp 5 eighth, C 4 and E 4 and G 4 half tied, quarter rest.

  function padAbcSayEvent ( $item ) {

    $say   = padAbcSayChord ( $item );
    $value = padAbcSayValue ( $item ['base'], $item ['dots'] );

    if ( $item ['kind'] == 'rest' )
      return $say . "$value rest" . padAbcSayDecorations ( $item );

    $names = [];
    $tied  = FALSE;

    foreach ( $item ['heads'] as $head ) {
      $alter    = [ 2 => ' double sharp', 1 => ' sharp', -1 => ' flat', -2 => ' double flat' ] [ $head ['alter'] ] ?? ( $head ['acc'] === '=' ? ' natural' : '' );
      $names [] = $head ['letter'] . $alter . ' ' . $head ['octave'];
      $tied     = $tied || $head ['tie'];
    }

    return $say . implode ( ' and ', $names ) . " $value" . ( $tied ? ' tied' : '' ) . padAbcSayDecorations ( $item );

  }

  // The decorations that are drawn, in words, behind the note.

  function padAbcSayDecorations ( $item ) {

    $said = array_intersect ( [ 'staccato', 'accent', 'fermata', 'trill' ], $item ['deco'] );

    return $said ? ' ' . implode ( ' ', $said ) : '';

  }

  // ------------------------------------------------------------------------------------------
  // Text
  // ------------------------------------------------------------------------------------------

  // A chord name as it is printed: # as a sharp sign, b after the root or before a number as
  // a flat sign - Bb7 is B♭7, F#m7b5 F♯m7♭5.

  function padAbcChordName ( $name ) {

    return preg_replace ( [ '/#/', '/(?<=[A-G])b|b(?=\d)/' ], [ '♯', '♭' ], $name );

  }

  // How wide a text is, guessed from its characters: system-ui has no fixed widths, and the
  // guess only keeps chord names and titles from running into each other.

  function padAbcWidth ( $text, $size, $bold = FALSE ) {

    preg_match_all ( '/./su', (string) $text, $m );

    $w = 0;

    foreach ( $m [0] as $char )
      if ( str_contains ( "il.,:;'|!", $char ) )
        $w += 0.28;
      elseif ( str_contains ( 'fjrt()/- ', $char ) )
        $w += 0.36;
      elseif ( str_contains ( 'mwMW', $char ) )
        $w += 0.86;
      elseif ( ctype_upper ( $char ) )
        $w += 0.68;
      elseif ( ctype_digit ( $char ) )
        $w += 0.6;
      else
        $w += 0.55;

    return $w * $size * ( $bold ? 1.06 : 1 );

  }

  // A text escaped for an element or an attribute, a byte that is no UTF-8 replaced.

  function padAbcText ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // What was written, for a message: at most 20 characters on one line, a byte that is no
  // UTF-8 a question mark.

  function padAbcShow ( $text ) {

    $text = (string) $text;

    if ( ! preg_match ( '//u', $text ) )
      $text = preg_replace ( '/[\x80-\xff]/', '?', $text );

    $text = preg_replace ( '/\s+/u', ' ', $text );

    return preg_match ( '/^(.{20})./su', $text, $m ) ? $m [1] . '...' : $text;

  }

  // A number as an SVG coordinate: at most two decimals, no trailing zeros, never -0.

  function padAbcNum ( $n ) {

    $text = rtrim ( rtrim ( number_format ( (float) $n, 2, '.', '' ), '0' ), '.' );

    return ( $text === '-0' or $text === '' ) ? '0' : $text;

  }

?>
