<?php

  // What the chord and the piano notations share: the names of the notes and of the chords.
  //
  // padMusicNote    a note name - C, F#, Bb, Ebb, C#4 - as [ pitch class 0-11, octave or NULL,
  //                 the name as written with ♯ and ♭, the semitones of its accidental ], or
  //                 NULL
  // padMusicChord   a chord name - C, Am, F#m7, Bbmaj7, Dsus4, G7/B - as [ root, intervals,
  //                 bass, the name, the name in words ], or NULL
  // padMusicName    the name of a pitch class in the spelling of a chord's root: sharps for a
  //                 root with a sharp or a natural root of a sharp key, flats for a flat root
  //
  // The intervals are semitones above the root: a major chord is 0 4 7. A chord name is a root,
  // a quality from the list below, and a bass after a slash.

  const PAD_MUSIC_STEPS = [ 'C' => 0, 'D' => 2, 'E' => 4, 'F' => 5, 'G' => 7, 'A' => 9, 'B' => 11 ];

  // The qualities, each with its intervals and what it is called; the spellings a chord chart
  // writes for one quality all lead to it.

  const PAD_MUSIC_QUALITIES = [
    ''       => [ [ 0, 4, 7 ],          'major'                 ],
    'm'      => [ [ 0, 3, 7 ],          'minor'                 ],
    '5'      => [ [ 0, 7 ],             'power chord'           ],
    '7'      => [ [ 0, 4, 7, 10 ],      'dominant seventh'      ],
    'maj7'   => [ [ 0, 4, 7, 11 ],      'major seventh'         ],
    'm7'     => [ [ 0, 3, 7, 10 ],      'minor seventh'         ],
    'mmaj7'  => [ [ 0, 3, 7, 11 ],      'minor major seventh'   ],
    '6'      => [ [ 0, 4, 7, 9 ],       'sixth'                 ],
    'm6'     => [ [ 0, 3, 7, 9 ],       'minor sixth'           ],
    '9'      => [ [ 0, 4, 7, 10, 14 ],  'dominant ninth'        ],
    'maj9'   => [ [ 0, 4, 7, 11, 14 ],  'major ninth'           ],
    'm9'     => [ [ 0, 3, 7, 10, 14 ],  'minor ninth'           ],
    'add9'   => [ [ 0, 4, 7, 14 ],      'added ninth'           ],
    'madd9'  => [ [ 0, 3, 7, 14 ],      'minor added ninth'     ],
    'sus2'   => [ [ 0, 2, 7 ],          'suspended second'      ],
    'sus4'   => [ [ 0, 5, 7 ],          'suspended fourth'      ],
    '7sus4'  => [ [ 0, 5, 7, 10 ],      'seventh suspended fourth' ],
    'dim'    => [ [ 0, 3, 6 ],          'diminished'            ],
    'dim7'   => [ [ 0, 3, 6, 9 ],       'diminished seventh'    ],
    'm7b5'   => [ [ 0, 3, 6, 10 ],      'half-diminished'       ],
    'aug'    => [ [ 0, 4, 8 ],          'augmented'             ],
    'aug7'   => [ [ 0, 4, 8, 10 ],      'augmented seventh'     ] ];

  const PAD_MUSIC_SPELLINGS = [
    'maj' => '', 'M' => '', 'min' => 'm', '-' => 'm', 'M7' => 'maj7', 'Δ' => 'maj7', 'Δ7' => 'maj7', 'ma7' => 'maj7',
    'min7' => 'm7', '-7' => 'm7', 'mM7' => 'mmaj7', 'm(maj7)' => 'mmaj7', 'minmaj7' => 'mmaj7', 'min6' => 'm6',
    'M9' => 'maj9', 'min9' => 'm9', 'sus' => 'sus4', '°' => 'dim', 'o' => 'dim', '°7' => 'dim7', 'o7' => 'dim7',
    'ø' => 'm7b5', 'ø7' => 'm7b5', 'm7-5' => 'm7b5', 'min7b5' => 'm7b5', '+' => 'aug', '+7' => 'aug7', '7#5' => 'aug7',
    '7+5' => 'aug7', '2' => 'sus2', 'add2' => 'add9', 'minadd9' => 'madd9' ];

  const PAD_MUSIC_SHARPS = [ 'C', 'C♯', 'D', 'D♯', 'E', 'F', 'F♯', 'G', 'G♯', 'A', 'A♯', 'B' ];
  const PAD_MUSIC_FLATS  = [ 'C', 'D♭', 'D', 'E♭', 'E', 'F', 'G♭', 'G', 'A♭', 'A', 'B♭', 'B' ];

  function padMusicNote ( $text ) {

    $text = str_replace ( [ '♯', '♭', '𝄪', '𝄫' ], [ '#', 'b', 'x', 'bb' ], trim ( (string) $text ) );

    if ( ! preg_match ( '/^([A-Ga-g])(##|#|x|bb|b)?(-?\d)?$/D', $text, $m ) )
      return NULL;

    $letter = strtoupper ( $m [1] );
    $shift  = [ '' => 0, '#' => 1, '##' => 2, 'x' => 2, 'b' => -1, 'bb' => -2 ] [ $m [2] ?? '' ];
    $octave = ( isset ( $m [3] ) and $m [3] !== '' ) ? (int) $m [3] : NULL;
    $class  = PAD_MUSIC_STEPS [$letter] + $shift;

    // B#4 is C5 and Cb4 is B3: the octave goes with the letter, as scientific pitch names it.

    if ( $octave !== NULL )
      $octave += intdiv ( $class - ( ( $class % 12 + 12 ) % 12 ), 12 );

    $name = $letter . strtr ( (string) ( $m [2] ?? '' ), [ '##' => '𝄪', 'x' => '𝄪', 'bb' => '𝄫', '#' => '♯', 'b' => '♭' ] );

    return [ ( $class % 12 + 12 ) % 12, $octave, $name, $shift ];

  }

  function padMusicChord ( $text ) {

    $text = trim ( str_replace ( [ '♯', '♭' ], [ '#', 'b' ], (string) $text ) );

    if ( ! preg_match ( '/^([A-G](?:#|b)?)([^\/]*)(?:\/([A-G](?:#|b)?))?$/D', $text, $m ) )
      return NULL;

    $quality = $m [2];

    if ( ! isset ( PAD_MUSIC_QUALITIES [$quality] ) )
      $quality = PAD_MUSIC_SPELLINGS [$quality] ?? NULL;

    if ( $quality === NULL )
      return NULL;

    $root = padMusicNote ( $m [1] );
    $bass = ( isset ( $m [3] ) and $m [3] !== '' ) ? padMusicNote ( $m [3] ) : NULL;

    $name  = $root [2] . $quality . ( $bass ? '/' . $bass [2] : '' );
    $words = $root [2] . ' ' . PAD_MUSIC_QUALITIES [$quality] [1] . ( $bass ? ' over ' . $bass [2] : '' );

    return [ $root [0], PAD_MUSIC_QUALITIES [$quality] [0], $bass ? $bass [0] : NULL, $name, $words, $root [2] ];

  }

  // The name of a pitch class, sharps or flats as the root of its chord is spelt: a flat root -
  // B♭, E♭ - and F take flats, every other root sharps.

  function padMusicName ( $class, $root = 'C' ) {

    $flats = ( str_contains ( $root, '♭' ) or $root == 'F' );

    return ( $flats ? PAD_MUSIC_FLATS : PAD_MUSIC_SHARPS ) [ ( $class % 12 + 12 ) % 12 ];

  }

?>
