<?php

  // da - Danish: Snowball's stop words and its Danish stemmer as Snowball 3.1 has it
  // (snowballstem.org/algorithms/danish), step for step after danish.sbl - a main suffix in
  // R1 (an -s after an s-ending), the last letter of gd, dt, gt and kt, then -ig, -lig, -elig,
  // -els and -løst (and the st of -igst before), a double consonant undoubled and an
  // apostrophe at the end dropped: husene is hus, hurtigt hurt, venlighed ven, pc'en pc.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Dansk',

    'stopwords' => 'og i jeg det at en den til er som på de med han af for ikke der var mig sig men et har om vi
      min havde ham hun nu over da fra du ud sin dem os op man hans hvor eller hvad skal selv her
      alle vil blev kunne ind når være dog noget ville jo deres efter ned skulle denne end dette
      mit også under have dig anden hende mine alt meget sit sine vor mod disse hvis din nogle hos
      blive mange ad bliver hendes været thi jer sådan',

    'stem' => function ( $word ) {

      static $v, $sEnding, $undouble, $main, $pairs, $other;

      if ( ! $v ) {
        $v        = padSnowballGroup ( 'aeiouyæåø' );
        $sEnding  = padSnowballGroup ( "abcdfghjklmnoprtvyzå'" );
        $undouble = padSnowballGroup ( 'bdfgklmnprst' );
        $main     = padSnowballSet ( [ 'hed', 'ethed', 'ered', 'e', 'erede', 'ende', 'erende', 'ene', 'erne', 'ere', 'en', 'heden', 'eren',
                                       'er', 'heder', 'erer', 'heds', 'es', 'endes', 'erendes', 'enes', 'ernes', 'eres', 'ens', 'hedens',
                                       'erens', 'ers', 'ets', 'erets', 'et', 'eret', 's' ] );
        $pairs    = padSnowballSet ( [ 'gd', 'dt', 'gt', 'kt' ] );
        $other    = padSnowballSet ( [ 'ig', 'lig', 'elig', 'els', 'løst' ] );
      }

      $z = new PadSnowball ( $word );

      // mark_regions: R1 after the first apostrophe - pc'en, ep'en - else after the first
      // consonant after a vowel, three letters in at the least; a word of fewer than three
      // letters is left as it is.

      if ( $z->l < 3 )
        return $word;

      $p1 = $z->l;

      if ( ( $z->to ( 0 ) and $z->past ( [ "'" => TRUE ] ) ) or ( $z->to ( 0 ) and $z->past ( $v ) and $z->pastNon ( $v ) ) )
        $p1 = $z->c;

      $p1 = max ( 3, $p1 );

      // The suffix of a set that ends the word, or ends it at the cursor, in R1 - NULL when
      // none does: the slice is set around it, the cursor before it.

      $suffix = function ( $set, $end = TRUE ) use ( $z, $p1 ) {
        if ( $end )
          $z->c = $z->l;
        if ( $z->c < $p1 )
          return NULL;
        $lb     = $z->lb;
        $z->lb  = $p1;
        $z->ket = $z->c;
        $s      = $z->amongB ( $set );
        $z->bra = $z->c;
        $z->lb  = $lb;
        return $s;
      };

      // consonant_pair: of gd, dt, gt and kt at the cursor in R1 the last letter deleted.

      $pair = function () use ( $z, $suffix, $pairs ) {
        $c = $z->c;
        if ( $suffix ( $pairs, FALSE ) === NULL ) {
          $z->c = $c;
          return;
        }
        $z->c   = $c;
        $z->ket = $c;
        $z->c--;
        $z->bra = $z->c;
        $z->del ();
      };

      // main_suffix: -hed, -erende, -ernes, -ets ... deleted, -s after an s-ending.

      $s = $suffix ( $main );

      if ( $s === 's' ) {
        if ( $z->inB ( $sEnding ) )
          $z->del ();
      } elseif ( $s !== NULL )
        $z->del ();

      $z->c = $z->l;
      $pair ();

      // other_suffix: the st of -igst; then -ig, -lig, -elig and -els deleted, and a
      // consonant pair after them; -løst -løs.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->eqB ( 'st' ) ) {
        $z->bra = $z->c;
        if ( $z->eqB ( 'ig' ) )
          $z->del ();
      }

      $s = $suffix ( $other );

      if ( $s === 'løst' )
        $z->from ( 'løs' );
      elseif ( $s !== NULL ) {
        $z->del ();
        $pair ();
      }

      // undouble: a consonant after the same one in R1 deleted.

      $z->c = $z->l;

      if ( $z->c - 1 >= $p1 and isset ( $undouble [ $z->at ( $z->c - 1 ) ] ) and $z->c - 2 >= 0 and $z->at ( $z->c - 2 ) === $z->at ( $z->c - 1 ) ) {
        $z->ket = $z->c;
        $z->bra = $z->c - 1;
        $z->del ();
      }

      // An apostrophe at the end deleted.

      if ( $z->l and $z->at ( $z->l - 1 ) === "'" ) {
        $z->ket = $z->l;
        $z->bra = $z->l - 1;
        $z->del ();
      }

      return $z->word ();

    }

  ];

?>
