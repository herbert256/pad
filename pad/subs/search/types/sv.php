<?php

  // sv - Swedish: Snowball's stop words and its Swedish stemmer as Snowball 3.1 has it
  // (snowballstem.org/algorithms/swedish), step for step after swedish.sbl - a main suffix in
  // R1 (an -s after an s-ending, -et and -ets not in words such as frihet, societet or
  // paket), a double consonant halved, then -lig, -ig, -els, -öst and -fullt: flickorna is
  // flick, husets hus, tidningarna tidning, tydligt tyd.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Svenska',

    'stopwords' => 'och det att i en jag hon som han på den med var sig för så till är men ett om hade de av
      icke mig du henne då sin nu har inte hans honom skulle hennes där min man ej vid kunde något
      från ut när efter upp vi dem vara vad över än dig kan sina här ha mot alla under någon eller
      allt mycket sedan ju denna själv detta åt utan varit hur ingen mitt ni bli blev oss din
      dessa några deras blir mina samma vilken er sådan vår blivit dess inom mellan sådant varför
      varje vilka ditt vem vilket sitt sådana vart dina vars vårt våra ert era vilkas',

    'stem' => function ( $word ) {

      static $v, $sEnding, $ostEnding, $notEt, $main, $pairs, $other;

      if ( ! $v ) {
        $v         = padSnowballGroup ( 'aeiouyäåö' );
        $sEnding   = padSnowballGroup ( 'bcdfghjklmnoprtvy' );
        $ostEnding = padSnowballGroup ( 'iklnprtuv' );
        $notEt     = padSnowballSet ( [ 'h', 'iet', 'uit', 'fab', 'cit', 'dit', 'alit', 'ilit', 'mit', 'nit', 'pit', 'rit', 'sit', 'tit',
                                        'ivit', 'kvit', 'xit', 'kom', 'rak', 'pak', 'stak' ] );
        $main      = padSnowballSet ( [ 'a', 'arna', 'erna', 'heterna', 'orna', 'ad', 'e', 'ade', 'ande', 'arne', 'are', 'aste', 'en',
                                        'anden', 'aren', 'heten', 'ern', 'ar', 'er', 'heter', 'or', 'as', 'arnas', 'ernas', 'ornas', 'es',
                                        'ades', 'andes', 'ens', 'arens', 'hetens', 'erns', 'at', 'andet', 'het', 'ast', 's', 'et' ] );
        $pairs     = padSnowballSet ( [ 'dd', 'gd', 'nn', 'dt', 'gt', 'kt', 'tt' ] );
        $other     = padSnowballSet ( [ 'lig', 'ig', 'els', 'öst', 'fullt' ] );
      }

      $z = new PadSnowball ( $word );

      // mark_regions: R1 after the first consonant after a vowel, three letters in at the
      // least; a word of fewer than three letters has none.

      $p1 = $z->l;

      if ( $z->l >= 3 and $z->to ( 0 ) and $z->past ( $v ) and $z->pastNon ( $v ) )
        $p1 = max ( 3, $z->c );

      // The suffix of a set that ends the word in R1, NULL when none does: the slice is set
      // around it, the cursor before it.

      $suffix = function ( $set ) use ( $z, $p1 ) {
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

      // et_condition: a consonant and a vowel before, not at the start, and none of the
      // endings of frihet, societet, alfabet, paket ... before the cursor.

      $et = function () use ( $z, $v, $notEt ) {
        $c  = $z->c;
        $ok = ( $z->outB ( $v ) and $z->inB ( $v ) and $z->c > $z->lb );
        $z->c = $c;
        if ( $ok and $z->amongB ( $notEt ) !== NULL )
          $ok = FALSE;
        $z->c = $c;
        return $ok;
      };

      // main_suffix: -arna, -heterna, -ande, -ens ... deleted; -s after an s-ending, and -ets
      // with its et when the et_condition holds; -et when it holds.

      $s = $suffix ( $main );

      if ( $s === 's' ) {
        $c = $z->c;
        if ( $z->eqB ( 'et' ) and $et () ) {
          $z->bra = $z->c;
          $z->del ();
        } elseif ( $z->to ( $c ) and $z->inB ( $sEnding ) )
          $z->del ();
      } elseif ( $s === 'et' ) {
        if ( $et () )
          $z->del ();
      } elseif ( $s !== NULL )
        $z->del ();

      // consonant_pair: of dd, gd, nn, dt, gt, kt and tt in R1 the last letter deleted.

      if ( $suffix ( $pairs ) !== NULL ) {
        $z->c   = $z->l;
        $z->ket = $z->c;
        $z->c--;
        $z->bra = $z->c;
        $z->del ();
      }

      // other_suffix: -lig, -ig and -els deleted, -öst after an öst-ending -ös, -fullt -full.

      $s = $suffix ( $other );

      if ( $s === 'öst' ) {
        if ( $z->inB ( $ostEnding ) )
          $z->from ( 'ös' );
      } elseif ( $s === 'fullt' )
        $z->from ( 'full' );
      elseif ( $s !== NULL )
        $z->del ();

      return $z->word ();

    }

  ];

?>
