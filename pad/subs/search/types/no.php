<?php

  // no - Norwegian: Snowball's stop words and its Norwegian stemmer as Snowball 3.1 has it
  // (snowballstem.org/algorithms/norwegian), for Bokmål and Nynorsk alike - the locales nb and
  // nn search in it too - step for step after norwegian.sbl: a main suffix in R1 (-ers not
  // after the letters that keep it, -s after an s-ending, an r not after an e or a k after a
  // consonant, -ert and -erte as -er), the t of dt and vt, then -leg, -lig, -els, -lov ...,
  // and an apostrophe at the end dropped: husene is hus, barna barn, hyggelig hygg.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Norsk',

    'stopwords' => 'og i jeg det at en et den til er som på de med han av ikke ikkje der så var meg seg men ett
      har om vi min mitt ha hadde hun nå over da ved fra du ut sin dem oss opp man kan hans hvor
      eller hva skal selv sjøl her alle vil bli ble blei blitt kunne inn når være kom noen noe
      ville dere deres kun ja etter ned skulle denne for deg si sine sitt mot å meget hvorfor
      dette disse uten hvordan ingen din ditt blir samme hvilken hvilke sånn inni mellom vår hver
      hvem vors hvis både bare enn fordi før mange også slik vært båe begge siden dykk dykkar dei
      deira deires deim di då eg ein eit eitt elles honom hjå ho hoe henne hennar hennes hoss
      hossen ingi inkje korleis korso kva kvar kvarhelst kven kvi kvifor me medan mi mine mykje no
      nokon noka nokor noko nokre sia sidan so somt somme um upp vere vore verte vort varte vart',

    'stem' => function ( $word ) {

      static $v, $sEnding, $main, $ers, $pairs, $other;

      if ( ! $v ) {
        $v       = padSnowballGroup ( 'aeêioòóôuyæåø' );
        $sEnding = padSnowballGroup ( 'bcdfghjlmnoptvyz' );
        $main    = padSnowballSet ( [ 'a', 'e', 'ede', 'ande', 'ende', 'ane', 'ene', 'hetene', 'en', 'heten', 'ar', 'er', 'heter', 'as', 'es',
                                      'edes', 'endes', 'enes', 'hetenes', 'ens', 'hetens', 'ets', 'et', 'het', 'ast', 'ers', 's', 'erte', 'ert' ] );
        $ers     = padSnowballSet ( [ 'amm', 'ast', 'ind', 'kap', 'kk', 'lt', 'nk', 'omm', 'pp', 'v', 'øst', 'giv', 'hav', 'skap', '' ] );
        $pairs   = padSnowballSet ( [ 'dt', 'vt' ] );
        $other   = padSnowballSet ( [ 'leg', 'eleg', 'ig', 'eig', 'lig', 'elig', 'els', 'lov', 'elov', 'slov', 'hetslov' ] );
      }

      $z = new PadSnowball ( $word );

      // mark_regions: R1 after the first apostrophe, else after the first consonant after a
      // vowel, three letters in at the least; a word of fewer than three letters is left as
      // it is.

      if ( $z->l < 3 )
        return $word;

      $p1 = $z->l;

      if ( ( $z->to ( 0 ) and $z->past ( [ "'" => TRUE ] ) ) or ( $z->to ( 0 ) and $z->past ( $v ) and $z->pastNon ( $v ) ) )
        $p1 = $z->c;

      $p1 = max ( 3, $p1 );

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

      // main_suffix: -a, -ene, -hetene, -ast ... deleted; -ers deleted unless after amm, ast,
      // ind, kap, kk, lt, nk, omm, pp, v or øst (but after giv, hav and skap); -s after an
      // s-ending, after an r not after an e, or after a k after a consonant; -erte and -ert
      // -er.

      $s = $suffix ( $main );

      if ( $s === 'ers' ) {
        if ( in_array ( $z->amongB ( $ers ), [ 'giv', 'hav', 'skap', '' ], TRUE ) )
          $z->del ();
      } elseif ( $s === 's' ) {
        $c = $z->c;
        if ( $z->inB ( $sEnding )
          or ( $z->to ( $c ) and $z->eqB ( 'r' ) and ! ( $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'e' ) )
          or ( $z->to ( $c ) and $z->eqB ( 'k' ) and $z->outB ( $v ) ) )
          $z->del ();
      } elseif ( $s === 'erte' or $s === 'ert' )
        $z->from ( 'er' );
      elseif ( $s !== NULL )
        $z->del ();

      // consonant_pair: of dt and vt in R1 the t deleted.

      if ( $suffix ( $pairs ) !== NULL ) {
        $z->c   = $z->l;
        $z->ket = $z->c;
        $z->c--;
        $z->bra = $z->c;
        $z->del ();
      }

      // other_suffix: -leg, -eig, -lig, -els, -lov, -hetslov ... deleted.

      if ( $suffix ( $other ) !== NULL )
        $z->del ();

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
