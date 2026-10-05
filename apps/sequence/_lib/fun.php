<?php

  // Sequences you can see and hear - the Gallery, Listen and Guess pages. A workshop
  // face for the subsystem: every type's terms drawn as a {sparkline} beside the OEIS
  // entry they start, a sequence played as notes, and a game that shows the start of a
  // sequence and asks for the next term.
  //
  // seqFunTypes      the types that need no parameter and give at least six terms, the
  //                  random ones left out - the gallery draws each of them
  // seqFunGuessable  the well-known ones the game picks from
  // seqFunTerms      the first terms of a type, as numbers
  // seqFunOeis       the A-number of the OEIS entry these terms come from, 0 for none
  // seqFunNotes      the terms as notes: MIDI numbers, five octaves up from C2
  // seqFunNoteName   C4, F#3 ... for a MIDI number
  // seqFunWav        the notes as a WAV file in a data: URI, for an <audio> element - the
  //                  sound is made on the server, so the page needs no JavaScript

  function seqFunTypes () {

    $out = [];

    foreach ( types () as $type )
      if ( ! in_array ( $type, [ 'random', 'chance', 'oeis' ] ) and pqParm ( $type ) === '' )
        if ( count ( seqFunTerms ( $type, 12 ) ) >= 6 )
          $out [] = $type;

    return $out;

  }

  function seqFunGuessable () {

    return [ 'fibonacci', 'prime', 'square', 'cubic', 'triangular', 'odd', 'lucas', 'pell',
             'catalan', 'pentagonal', 'hexagonal', 'tetrahedral', 'pronic', 'recaman',
             'tribonacci', 'xpadovan', 'lucky', 'happy', 'bell', 'caterer' ];

  }

  function seqFunTerms ( $type, $rows ) {

    if ( ! pqSeq ( $type ) )
      return [];

    return array_map ( fn ( $term ) => $term + 0, pqArray ( $type, '', "rows=$rows" ) );

  }

  // The OEIS table the oeis type reads (pad/sequence/types/oeis/oeis.sqlite) is searched
  // for an entry holding the first twelve terms in a row, at its start or a few terms in:
  // PAD's catalan starts 1, 2, 5 where A000108 starts 1, 1, 2, 5. Of the entries that
  // match, an early one that starts with them wins, else the lowest - the classic entry is
  // the old one. A search can scan
  // the whole table, so the answers are kept in DATA/sequence/oeis.json, keyed by the terms.

  function seqFunOeis ( $terms ) {

    $terms = array_slice ( array_values ( $terms ), 0, 12 );
    $key   = implode ( ',', $terms );

    if ( count ( $terms ) < 6 or count ( array_unique ( $terms ) ) < 2 )
      return 0;

    $cache = json_decode ( padFileGet ( 'sequence/oeis.json', '{}' ), TRUE ) ?: [];

    if ( isset ( $cache [$key] ) )
      return $cache [$key];

    $cache [$key] = seqFunOeisSearch ( $key );

    padFilePut ( 'sequence/oeis.json', json_encode ( $cache, JSON_PRETTY_PRINT ) );

    return $cache [$key];

  }

  function seqFunOeisSearch ( $list ) {

    if ( ! class_exists ( 'SQLite3' ) or ! file_exists ( PT . 'oeis/oeis.sqlite' ) )
      return 0;

    $db  = new SQLite3 ( PT . 'oeis/oeis.sqlite', SQLITE3_OPEN_READONLY );
    $get = $db->prepare ( 'SELECT a, terms FROM oeis WHERE terms = :list OR terms LIKE :start OR terms LIKE :inside ORDER BY a LIMIT 50' );

    $get->bindValue ( ':list',   $list );
    $get->bindValue ( ':start',  "$list,%" );
    $get->bindValue ( ':inside', "%,$list,%" );

    $rows  = $get->execute ();
    $start = $later = 0;

    while ( $row = $rows->fetchArray ( SQLITE3_NUM ) ) {

      $at = strpos ( ",$row[1],", ",$list," );

      if ( $at === 0 and ! $start )
        $start = $row [0];
      elseif ( $at !== FALSE and substr_count ( substr ( $row [1], 0, $at ), ',' ) <= 6 and ! $later )
        $later = $row [0];

    }

    $db->close ();

    // The entry that starts with the terms is the one - A005408 for the odd numbers, not
    // A004273, which puts a 0 in front - unless an entry with a term or two in front is
    // much older: A000110 for the Bell numbers, not A203642, and A000217 for the triangular
    // numbers, not A025724.

    if ( $start and ( ! $later or $later * 2 > $start ) )
      return $start;

    return $later;

  }

  function seqFunNotes ( $terms ) {

    return array_map ( fn ( $term ) => 36 + (int) abs ( fmod ( (float) $term, 60 ) ), $terms );

  }

  function seqFunNoteName ( $midi ) {

    return [ 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B' ] [ $midi % 12 ] . ( intdiv ( $midi, 12 ) - 1 );

  }

  // 8-bit mono at 8000 samples a second, a fifth of a second a note: a sine with a soft
  // second harmonic, a short attack and release so the notes do not click. Thirty-two
  // notes come to about 70 KB of base64.

  function seqFunWav ( $terms, $seconds = 0.2 ) {

    $rate = 8000;
    $size = (int) ( $seconds * $rate );
    $data = '';

    foreach ( seqFunNotes ( $terms ) as $midi ) {

      $freq = 440 * 2 ** ( ( $midi - 69 ) / 12 );

      for ( $i = 0; $i < $size; $i++ ) {
        $phase = 2 * M_PI * $freq * $i / $rate;
        $shape = min ( 1, $i / ( 0.01 * $rate ), ( $size - $i ) / ( 0.06 * $rate ) );
        $wave  = 0.75 * sin ( $phase ) + 0.25 * sin ( 2 * $phase );
        $data .= chr ( (int) round ( 128 + 96 * $shape * $wave ) );
      }

    }

    $head = 'RIFF' . pack ( 'V', 36 + strlen ( $data ) ) . 'WAVE'
          . 'fmt ' . pack ( 'VvvVVvv', 16, 1, 1, $rate, $rate, 1, 8 )
          . 'data' . pack ( 'V', strlen ( $data ) );

    return 'data:audio/wav;base64,' . base64_encode ( $head . $data );

  }

?>
