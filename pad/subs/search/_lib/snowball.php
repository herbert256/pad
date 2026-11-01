<?php

  // The runtime of the Snowball stemmers in subs/search/types/ - Snowball's own model
  // (snowballstem.org), so that every language's stemmer reads as its .sbl source reads, and
  // does what it does: a word as its characters, a cursor c that a forward step moves to the
  // right and a backward step to the left, the limits l and lb, and the slice from bra to ket
  // the steps replace or delete. A position kept over a change is kept as its distance from
  // the end in a backward step (l - c), as the generated code keeps it.
  //
  //   in / out ( $g )      the character at the cursor is in (not in) the grouping: moves past
  //   inB / outB ( $g )    the same backwards
  //   past / pastNon       gopast: past the first character in (not in) the grouping
  //   pastB / pastNonB     the same backwards
  //   eq / eqB ( $s )      the text at the cursor: moves past it
  //   among / amongB       the longest of a set (padSnowballSet) at the cursor: moves past it
  //                        and answers it, NULL when none is there
  //   from ( $s ), del ()  the slice replaced by $s, or deleted
  //   insert ( $a, $b, $s ) characters $a to $b replaced, the slice moved along
  //   to ( $c ), bra ()    the cursor set, the slice's start set at the cursor - TRUE both
  //
  // A grouping is [ character => TRUE ], padSnowballGroup ( 'aeiouy' ); a set of strings
  // padSnowballSet ( [ 'ed', 'ing' ] ), which knows its longest.

  function padSnowballGroup ( $chars ) {

    return array_fill_keys ( mb_str_split ( $chars ), TRUE );

  }

  function padSnowballSet ( $strings ) {

    $set = [ 'max' => 0, 'has' => [] ];

    foreach ( $strings as $s ) {
      $set ['has'] [$s] = TRUE;
      $set ['max']      = max ( $set ['max'], mb_strlen ( $s ) );
    }

    return $set;

  }

  final class PadSnowball {

    public $p, $c = 0, $l, $lb = 0, $bra = 0, $ket = 0;

    function __construct ( $word ) {

      $this->p = mb_str_split ( $word );
      $this->l = count ( $this->p );

    }

    function word () {

      return implode ( '', $this->p );

    }

    // The cursor set, or the slice's start set at it - both TRUE, to stand in a condition.

    function to ( $c ) {

      $this->c = $c;

      return TRUE;

    }

    function bra () {

      $this->bra = $this->c;

      return TRUE;

    }

    function at ( $i ) {

      return $this->p [$i] ?? '';

    }

    function in ( $g ) {

      if ( $this->c >= $this->l or ! isset ( $g [ $this->p [ $this->c ] ] ) )
        return FALSE;

      $this->c++;

      return TRUE;

    }

    function out ( $g ) {

      if ( $this->c >= $this->l or isset ( $g [ $this->p [ $this->c ] ] ) )
        return FALSE;

      $this->c++;

      return TRUE;

    }

    function inB ( $g ) {

      if ( $this->c <= $this->lb or ! isset ( $g [ $this->p [ $this->c - 1 ] ] ) )
        return FALSE;

      $this->c--;

      return TRUE;

    }

    function outB ( $g ) {

      if ( $this->c <= $this->lb or isset ( $g [ $this->p [ $this->c - 1 ] ] ) )
        return FALSE;

      $this->c--;

      return TRUE;

    }

    // gopast g: on to the first character in g, and past it.

    function past ( $g ) {

      while ( $this->c < $this->l and ! isset ( $g [ $this->p [ $this->c ] ] ) )
        $this->c++;

      if ( $this->c >= $this->l )
        return FALSE;

      $this->c++;

      return TRUE;

    }

    // gopast non-g: on to the first character not in g, and past it.

    function pastNon ( $g ) {

      while ( $this->c < $this->l and isset ( $g [ $this->p [ $this->c ] ] ) )
        $this->c++;

      if ( $this->c >= $this->l )
        return FALSE;

      $this->c++;

      return TRUE;

    }

    function pastB ( $g ) {

      while ( $this->c > $this->lb and ! isset ( $g [ $this->p [ $this->c - 1 ] ] ) )
        $this->c--;

      if ( $this->c <= $this->lb )
        return FALSE;

      $this->c--;

      return TRUE;

    }

    function pastNonB ( $g ) {

      while ( $this->c > $this->lb and isset ( $g [ $this->p [ $this->c - 1 ] ] ) )
        $this->c--;

      if ( $this->c <= $this->lb )
        return FALSE;

      $this->c--;

      return TRUE;

    }

    function eq ( $s ) {

      $n = mb_strlen ( $s );

      if ( $this->c + $n > $this->l or implode ( '', array_slice ( $this->p, $this->c, $n ) ) !== $s )
        return FALSE;

      $this->c += $n;

      return TRUE;

    }

    function eqB ( $s ) {

      $n = mb_strlen ( $s );

      if ( $this->c - $n < $this->lb or implode ( '', array_slice ( $this->p, $this->c - $n, $n ) ) !== $s )
        return FALSE;

      $this->c -= $n;

      return TRUE;

    }

    function among ( $set ) {

      for ( $n = min ( $set ['max'], $this->l - $this->c ); $n >= 0; $n-- ) {
        $s = implode ( '', array_slice ( $this->p, $this->c, $n ) );
        if ( isset ( $set ['has'] [$s] ) ) {
          $this->c += $n;
          return $s;
        }
      }

      return NULL;

    }

    function amongB ( $set ) {

      for ( $n = min ( $set ['max'], $this->c - $this->lb ); $n >= 0; $n-- ) {
        $s = implode ( '', array_slice ( $this->p, $this->c - $n, $n ) );
        if ( isset ( $set ['has'] [$s] ) ) {
          $this->c -= $n;
          return $s;
        }
      }

      return NULL;

    }

    // Characters $a to $b replaced by $s - the cursor and the limit moved along, as
    // Snowball's replace_s moves them.

    function replace ( $a, $b, $s ) {

      $chars  = ( $s === '' ) ? [] : mb_str_split ( $s );
      $adjust = count ( $chars ) - ( $b - $a );

      array_splice ( $this->p, $a, $b - $a, $chars );

      $this->l += $adjust;

      if ( $this->c >= $b )
        $this->c += $adjust;
      elseif ( $this->c > $a )
        $this->c = $a;

      return $adjust;

    }

    function from ( $s ) {

      $this->replace ( $this->bra, $this->ket, $s );
      $this->ket = $this->bra + mb_strlen ( $s );

    }

    function del () {

      $this->from ( '' );

    }

    function insert ( $a, $b, $s ) {

      $adjust = $this->replace ( $a, $b, $s );

      if ( $a <= $this->bra ) $this->bra += $adjust;
      if ( $a <= $this->ket ) $this->ket += $adjust;

    }

    function slice () {

      return implode ( '', array_slice ( $this->p, $this->bra, $this->ket - $this->bra ) );

    }

  }

?>
