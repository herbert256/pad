<?php

  // Reads the OEIS table: pqOeis($a) hands back sequence A<a> as a term array, and an empty
  // array when the table has no entry for that A-number.
  //
  // The terms live in oeis.sqlite, one row per A-number holding the list as text, and are
  // fetched a sequence at a time rather than all at once - the const array this replaced
  // cost 870MB of memory and a second of parsing on every request that touched an oeis
  // sequence, however few terms it wanted.
  //
  // A term arrives as text and is put through + 0, the same conversion PHP makes of a
  // numeric literal: whole while it fits a 64-bit integer and a float beyond that. So a term
  // reads exactly as it did from the const array, and build/one.php still ends a build at
  // the first one out of integer range.
  //
  // The sequence last asked for is kept, because oeis/make.php asks for the same one once
  // per term. The handle and the statement are opened once, on the first term of the first
  // oeis sequence a request builds.

  function pqOeis ( $a ) {

    static $pqOeisDb = NULL, $pqOeisGet = NULL, $pqOeisLast = NULL, $pqOeisTerms = [];

    $a = (int) $a;

    if ( $pqOeisLast === $a )
      return $pqOeisTerms;

    if ( ! $pqOeisDb ) {

      if ( ! class_exists ( 'SQLite3' ) ) {
        padError ( 'The oeis sequence needs the sqlite3 extension' );
        return [];
      }

      if ( ! file_exists ( PT . 'oeis/oeis.sqlite' ) ) {
        padError ( 'The oeis table is missing - regenerate it with oeis/regenerate.php' );
        return [];
      }

      $pqOeisDb  = new SQLite3 ( PT . 'oeis/oeis.sqlite', SQLITE3_OPEN_READONLY );
      $pqOeisGet = $pqOeisDb->prepare ( 'SELECT terms FROM oeis WHERE a = ?' );

    }

    $pqOeisGet->reset ();
    $pqOeisGet->bindValue ( 1, $a, SQLITE3_INTEGER );

    $pqOeisRow = $pqOeisGet->execute ()->fetchArray ( SQLITE3_NUM );

    $pqOeisLast  = $a;

    // A row is a sequence of whole numbers or none: the row for A375613, the last of the PHP
    // file the table was converted from, held that file's closing tag, and adding 0 to it ended
    // the request on a TypeError. The row holds its terms now; regenerate.php leaves such a
    // row out, and one that gets in all the same reads as no sequence.

    $pqOeisTerms = ( $pqOeisRow === FALSE or ! preg_match ( '/^-?[0-9]+(,-?[0-9]+)*$/', $pqOeisRow [0] ) )
                 ? []
                 : array_map ( function ( $pqOeisOne ) { return $pqOeisOne + 0; },
                               explode ( ',', $pqOeisRow [0] ) );

    return $pqOeisTerms;

  }

?>
