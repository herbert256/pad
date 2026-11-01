<?php

  // fr - French: Snowball's stop words and its French stemmer as Snowball 3 has it
  // (snowballstem.org/algorithms/french), step for step after french.sbl - an elision, a
  // standard suffix, else an i-verb or another verb ending, else a residual one, then a
  // double consonant and an accent undone: continuellement is continuel, l'homme homm,
  // nationales national.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Français',

    'stopwords' => 'au aux avec ce ces dans de des du elle en et eux il je la le leur lui ma mais me même mes
      moi mon ne nos notre nous on ou par pas pour qu que qui sa se ses sur ta te tes toi ton tu
      un une vos votre vous c d j l à m n s t y été étée étées étés étant suis es êtes sont serai
      seras sera serons serez seront serais serait serions seriez seraient étais était étions
      étiez étaient fus fut fûmes fûtes furent sois soit soyons soyez soient fusse fusses
      fussions fussiez fussent ayant eu eue eues eus ai avons avez ont aurai aurons aurez auront
      aurais aurait aurions auriez auraient avais avait aviez avaient eut eûmes eûtes eurent
      aie aies ait ayons ayez aient eusse eusses eût eussions eussiez eussent ceci cela celà
      cet cette ici ils les leurs quel quels quelle quelles sans soi',

    'stem' => function ( $word ) {

      static $v, $elision, $ouxEnding, $keepWithS, $start, $standard, $group, $ement, $ite, $iVerb, $verb, $verbGroup, $notAis, $residual, $double;

      if ( ! $v ) {

        $v         = padSnowballGroup ( 'aeiouyâàëéêèïîôûù' );
        $elision   = padSnowballGroup ( 'cdjlmnstz' );
        $ouxEnding = padSnowballGroup ( 'bhjlnp' );
        $keepWithS = padSnowballGroup ( 'aiouès' );
        $start     = padSnowballSet ( [ 'par', 'col', 'tap', 'ni' ] );

        $group = [];

        foreach ( [ 1  => 'ance iqUe isme able iste eux ances iqUes ismes ables istes',
                    2  => 'atrice ateur ation atrices ateurs ations',
                    3  => 'logie logies',
                    4  => 'usion ution usions utions',
                    5  => 'ence ences',
                    6  => 'ement ements',
                    7  => 'ité ités',
                    8  => 'if ive ifs ives',
                    9  => 'eaux',
                    10 => 'aux',
                    11 => 'oux',
                    12 => 'euse euses',
                    13 => 'issement issements',
                    14 => 'amment',
                    15 => 'emment',
                    16 => 'ment ments' ] as $n => $list )
          foreach ( explode ( ' ', $list ) as $one )
            $group [$one] = $n;

        $standard = padSnowballSet ( array_keys ( $group ) );
        $ement    = padSnowballSet ( [ 'iv', 'eus', 'abl', 'iqU', 'ièr', 'Ièr' ] );
        $ite      = padSnowballSet ( [ 'abil', 'ic', 'iv' ] );
        $iVerb    = padSnowballSet ( preg_split ( '/\s+/', 'îmes ît îtes i ie ies ir ira irai iraIent irais irait iras irent irez
          iriez irions irons iront is issaIent issais issait issant issante issantes issants isse issent isses issez issiez
          issions issons it' ) );

        $verbGroup = [];

        foreach ( [ 1 => 'ions',
                    2 => 'é ée ées és èrent er era erai eraIent erais erait eras erez eriez erions erons eront ez iez eais',
                    3 => 'âmes ât âtes a ai aIent ait ant ante antes ants as asse assent asses assiez assions',
                    4 => 'ais aise aises' ] as $n => $list )
          foreach ( explode ( ' ', $list ) as $one )
            $verbGroup [$one] = $n;

        $verb     = padSnowballSet ( array_keys ( $verbGroup ) );
        $notAis   = padSnowballSet ( [ 'al', 'auv', 'épl' ] );
        $residual = padSnowballSet ( [ 'ion', 'ier', 'ière', 'Ier', 'Ière', 'e' ] );
        $double   = padSnowballSet ( [ 'enn', 'onn', 'ett', 'ell', 'eill' ] );

      }

      $z = new PadSnowball ( $word );

      // elisions: l', d', qu' ... at the start, when a word follows.

      if ( ( $z->in ( $elision ) or $z->eq ( 'qu' ) ) and $z->eq ( "'" ) and $z->c < $z->l ) {
        $z->bra = 0;
        $z->ket = $z->c;
        $z->del ();
      }

      // prelude: u, i and y between vowels, y after or before a vowel and the u of qu are
      // consonants, U, I and Y; ë and ï are He and Hi. Each place is tried again after a
      // change, as Snowball's repeat goto does.

      for ( $k = 0; $k <= $z->l; ) {

        $here = $z->at ( $k );
        $next = $z->at ( $k + 1 );
        $done = TRUE;

        if ( isset ( $v [$here] ) and ( ( ( $next === 'u' or $next === 'i' ) and isset ( $v [ $z->at ( $k + 2 ) ] ) ) or $next === 'y' ) )
          $z->p [ $k + 1 ] = strtoupper ( $next );
        elseif ( $here === 'ë' or $here === 'ï' ) {
          $z->bra = $k; $z->ket = $k + 1;
          $z->from ( $here === 'ë' ? 'He' : 'Hi' );
        } elseif ( $here === 'y' and isset ( $v [$next] ) )
          $z->p [$k] = 'Y';
        elseif ( $here === 'q' and $next === 'u' )
          $z->p [ $k + 1 ] = 'U';
        else
          $done = FALSE;

        if ( ! $done )
          $k++;

      }

      // mark_regions: RV after two vowels at the start (and a letter), after par, col, tap or
      // ni and a vowel, else after the first vowel not at the start; R1 and R2 as usual.

      $pV = $p1 = $p2 = $z->l;
      $z->c = 0;

      if ( $z->in ( $v ) and $z->in ( $v ) and $z->c < $z->l )
        $pV = $z->c + 1;
      elseif ( $z->to ( 0 ) and ( $s = $z->among ( $start ) ) !== NULL and ( $s !== 'ni' or $z->in ( $v ) ) )
        $pV = $z->c;
      elseif ( $z->l >= 1 and $z->to ( 1 ) and $z->past ( $v ) )
        $pV = $z->c;

      $z->c = 0;

      if ( $z->past ( $v ) and $z->pastNon ( $v ) ) {
        $p1 = $z->c;
        if ( $z->past ( $v ) and $z->pastNon ( $v ) )
          $p2 = $z->c;
      }

      $RV = fn () => $pV <= $z->c;
      $R1 = fn () => $p1 <= $z->c;
      $R2 = fn () => $p2 <= $z->c;

      // In R2 the slice deleted, else replaced by $else ('' when there is no else).

      $delOr = function ( $else ) use ( $z, $R2 ) {
        if ( $R2 () )
          $z->del ();
        elseif ( $else !== '' )
          $z->from ( $else );
        else
          return FALSE;
        return TRUE;
      };

      // A suffix before the one just handled: $s found, bra set - or NULL, the cursor back.

      $before = function ( $set ) use ( $z ) {
        $keep   = $z->l - $z->c;
        $z->ket = $z->c;
        $s      = is_string ( $set ) ? ( $z->eqB ( $set ) ? $set : NULL ) : $z->amongB ( $set );
        if ( $s === NULL )
          $z->c = $z->l - $keep;
        else
          $z->bra = $z->c;
        return $s;
      };

      // standard_suffix - TRUE when it took a suffix. -amment, -emment and -ment change the
      // word and still answer FALSE: the verb suffixes are tried after them.

      $standardSuffix = function () use ( $z, $standard, $group, $ement, $ite, $ouxEnding, $v, $RV, $R1, $R2, $delOr, $before ) {

        $z->ket = $z->c;
        $s      = $z->amongB ( $standard );

        if ( $s === NULL )
          return FALSE;

        $z->bra = $z->c;
        $n      = $group [$s];

        switch ( $n ) {

          case 1:  if ( ! $R2 () ) return FALSE; $z->del (); return TRUE;
          case 3:  if ( ! $R2 () ) return FALSE; $z->from ( 'log' ); return TRUE;
          case 4:  if ( ! $R2 () ) return FALSE; $z->from ( 'u' );   return TRUE;
          case 5:  if ( ! $R2 () ) return FALSE; $z->from ( 'ent' ); return TRUE;
          case 9:  $z->from ( 'eau' ); return TRUE;
          case 10: if ( ! $R1 () ) return FALSE; $z->from ( 'al' ); return TRUE;

          case 2:
            if ( ! $R2 () ) return FALSE;
            $z->del ();
            if ( $before ( 'ic' ) !== NULL )
              $delOr ( 'iqU' );
            return TRUE;

          case 6:
            if ( ! $RV () ) return FALSE;
            $z->del ();
            $keep = $z->l - $z->c;
            $t    = $before ( $ement );
            if ( $t === 'iv' ) {
              if ( $R2 () ) {
                $z->del ();
                if ( $before ( 'at' ) !== NULL and $R2 () )
                  $z->del ();
                else
                  $z->c = $z->l - $keep;
              } else
                $z->c = $z->l - $keep;
            } elseif ( $t === 'eus' ) {
              if ( $R2 () )
                $z->del ();
              elseif ( $R1 () )
                $z->from ( 'eux' );
              else
                $z->c = $z->l - $keep;
            } elseif ( $t === 'abl' or $t === 'iqU' ) {
              if ( $R2 () )
                $z->del ();
              else
                $z->c = $z->l - $keep;
            } elseif ( $t !== NULL ) {
              if ( $RV () )
                $z->from ( 'i' );
              else
                $z->c = $z->l - $keep;
            }
            return TRUE;

          case 7:
            if ( ! $R2 () ) return FALSE;
            $z->del ();
            $t = $before ( $ite );
            if ( $t === 'abil' )
              $delOr ( 'abl' );
            elseif ( $t === 'ic' )
              $delOr ( 'iqU' );
            elseif ( $t === 'iv' and $R2 () )
              $z->del ();
            return TRUE;

          case 8:
            if ( ! $R2 () ) return FALSE;
            $z->del ();
            $keep = $z->l - $z->c;
            if ( $before ( 'at' ) !== NULL and $R2 () ) {
              $z->del ();
              if ( $before ( 'ic' ) !== NULL )
                $delOr ( 'iqU' );
            } else
              $z->c = $z->l - $keep;
            return TRUE;

          case 11:
            if ( ! $z->inB ( $ouxEnding ) ) return FALSE;
            $z->from ( 'ou' );
            return TRUE;

          case 12:
            if ( $R2 () )
              $z->del ();
            elseif ( $R1 () )
              $z->from ( 'eux' );
            else
              return FALSE;
            return TRUE;

          case 13:
            if ( ! $R1 () or ! $z->outB ( $v ) ) return FALSE;
            $z->del ();
            return TRUE;

          case 14:
          case 15:
            if ( $RV () )
              $z->from ( $n == 14 ? 'ant' : 'ent' );
            return FALSE;

          default:
            $keep = $z->l - $z->c;
            if ( $z->inB ( $v ) and $RV () ) {
              $z->c = $z->l - $keep;
              $z->del ();
            }
            return FALSE;

        }

      };

      // i_verb_suffix: an ending of an -ir verb in RV, after a consonant that is no H.

      $iVerbSuffix = function () use ( $z, $iVerb, $v, $pV ) {

        if ( $z->c < $pV )
          return FALSE;

        $lb     = $z->lb;
        $z->lb  = $pV;
        $z->ket = $z->c;
        $ok     = $z->amongB ( $iVerb ) !== NULL;

        if ( $ok ) {
          $z->bra = $z->c;
          $ok     = ( ! ( $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'H' ) and $z->outB ( $v ) );
          if ( $ok )
            $z->del ();
        }

        $z->lb = $lb;

        return $ok;

      };

      // verb_suffix: another verb ending in RV - -ions in R2; -ais, -aise and -aises not after
      // a word's al, nor after auv or épl.

      $verbSuffix = function () use ( $z, $verb, $verbGroup, $notAis, $pV, $R2 ) {

        if ( $z->c < $pV )
          return FALSE;

        $lb     = $z->lb;
        $z->lb  = $pV;
        $z->ket = $z->c;
        $s      = $z->amongB ( $verb );
        $z->bra = $z->c;
        $z->lb  = $lb;

        if ( $s === NULL )
          return FALSE;

        switch ( $verbGroup [$s] ) {

          case 1:
            if ( ! $R2 () ) return FALSE;
            break;

          case 3:
            $keep = $z->l - $z->c;
            if ( $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'e' and $z->c - 1 >= $pV )
              $z->bra = $z->c - 1;
            else
              $z->c = $z->l - $keep;
            break;

          case 4:
            $keep = $z->l - $z->c;
            $t    = $z->amongB ( $notAis );
            if ( $t === 'auv' or $t === 'épl' or ( $t === 'al' and $z->c - 1 == $z->lb ) )
              return FALSE;
            $z->c = $z->l - $keep;
            break;

        }

        $z->del ();

        return TRUE;

      };

      // residual_suffix: a final s not after a, i, o, u, è or s; then -ion after s or t in R2,
      // -ier and -ière to i, a final e - in RV.

      $residualSuffix = function () use ( $z, $keepWithS, $residual, $pV, $R2 ) {

        $keep   = $z->l - $z->c;
        $z->ket = $z->c;

        if ( $z->eqB ( 's' ) ) {
          $z->bra = $z->c;
          $here   = $z->l - $z->c;
          if ( $z->eqB ( 'Hi' ) or $z->outB ( $keepWithS ) ) {
            $z->c = $z->l - $here;
            $z->del ();
          } else
            $z->c = $z->l - $keep;
        } else
          $z->c = $z->l - $keep;

        if ( $z->c < $pV )
          return FALSE;

        $lb     = $z->lb;
        $z->lb  = $pV;
        $z->ket = $z->c;
        $s      = $z->amongB ( $residual );
        $ok     = $s !== NULL;

        if ( $ok ) {
          $z->bra = $z->c;
          if ( $s === 'ion' ) {
            $ok = ( $R2 () and $z->c > $z->lb and in_array ( $z->at ( $z->c - 1 ), [ 's', 't' ], TRUE ) );
            if ( $ok )
              $z->del ();
          } elseif ( $s === 'e' )
            $z->del ();
          else
            $z->from ( 'i' );
        }

        $z->lb = $lb;

        return $ok;

      };

      $z->c = $z->l;

      if ( $standardSuffix () or ( ( $z->c = $z->l ) >= 0 and $iVerbSuffix () ) or ( ( $z->c = $z->l ) >= 0 and $verbSuffix () ) ) {

        // A final Y is i, a final ç c.

        $z->c   = $z->l;
        $z->ket = $z->c;
        $last   = $z->at ( $z->c - 1 );

        if ( $z->c > $z->lb and ( $last === 'Y' or $last === 'ç' ) ) {
          $z->c--;
          $z->bra = $z->c;
          $z->from ( $last === 'Y' ? 'i' : 'c' );
        }

      } else {

        $z->c = $z->l;
        $residualSuffix ();

      }

      // un_double: -enn, -onn, -ett, -ell and -eill lose their last letter.

      $z->c = $z->l;

      if ( $z->amongB ( $double ) !== NULL ) {
        $z->c   = $z->l;
        $z->ket = $z->c;
        $z->c--;
        $z->bra = $z->c;
        $z->del ();
      }

      // un_accent: an é or è before the final consonants is e.

      $z->c = $z->l;
      $n    = 0;

      while ( $z->outB ( $v ) )
        $n++;

      if ( $n >= 1 ) {
        $z->ket = $z->c;
        $last   = $z->at ( $z->c - 1 );
        if ( $z->c > $z->lb and ( $last === 'é' or $last === 'è' ) ) {
          $z->c--;
          $z->bra = $z->c;
          $z->from ( 'e' );
        }
      }

      // postlude: I, U and Y back to i, u and y, He and Hi to ë and ï, an H alone gone.

      return strtr ( $z->word (), [ 'He' => 'ë', 'Hi' => 'ï', 'H' => '', 'I' => 'i', 'U' => 'u', 'Y' => 'y' ] );

    }

  ];

?>
