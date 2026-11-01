<?php

  // it - Italian: Snowball's stop words and its Italian stemmer as Snowball 3.1 has it
  // (snowballstem.org/algorithms/italian), step for step after italian.sbl - an elided
  // article or pronoun before an apostrophe, an attached pronoun, a standard suffix or else a
  // verb ending, then a final vowel and the h of -che and -ghe: l'università is univers,
  // abbandonata abbandon, portarlo port, amiche amic.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Italiano',

    'stopwords' => 'ad al allo ai agli all agl alla alle con col coi da dal dallo dai dagli dall dagl dalla
      dalle di del dello dei degli dell degl della delle in nel nello nei negli nell negl nella
      nelle su sul sullo sui sugli sull sugl sulla sulle per tra contro io tu lui lei noi voi loro
      mio mia miei mie tuo tua tuoi tue suo sua suoi sue nostro nostra nostri nostre vostro vostra
      vostri vostre mi ti ci vi lo la li le gli ne il un uno una ma ed se perché anche come dov
      dove che chi cui non più quale quanto quanti quanta quante quello quelli quella quelle
      questo questi questa queste si tutto tutti a c e i l o ho hai ha abbiamo avete hanno abbia
      abbiate abbiano avrò avrai avrà avremo avrete avranno avrei avresti avrebbe avremmo avreste
      avrebbero avevo avevi aveva avevamo avevate avevano ebbi avesti ebbe avemmo aveste ebbero
      avessi avesse avessimo avessero avendo avuto avuta avuti avute sono sei è siamo siete sia
      siate siano sarò sarai sarà saremo sarete saranno sarei saresti sarebbe saremmo sareste
      sarebbero ero eri era eravamo eravate erano fui fosti fu fummo foste furono fossi fosse
      fossimo fossero essendo faccio fai facciamo fanno faccia facciate facciano farò farai farà
      faremo farete faranno farei faresti farebbe faremmo fareste farebbero facevo facevi faceva
      facevamo facevate facevano feci facesti fece facemmo faceste fecero facessi facesse
      facessimo facessero facendo sto stai sta stiamo stanno stia stiate stiano starò starai starà
      staremo starete staranno starei staresti starebbe staremmo stareste starebbero stavo stavi
      stava stavamo stavate stavano stetti stesti stette stemmo steste stettero stessi stesse
      stessimo stessero stando',

    'stem' => function ( $word ) {

      static $v, $aeio, $cg, $elisions, $pronouns, $gerunds, $standard, $group, $amente, $ita, $verb;

      if ( ! $v ) {

        $v        = padSnowballGroup ( 'aeiouàèìòù' );
        $aeio     = padSnowballGroup ( 'aeioàèìò' );
        $cg       = padSnowballGroup ( 'cg' );
        $elisions = padSnowballSet ( [ "d'", "l'", "m'", "s'", "t'", "v'", "all'", "dall'", "dell'", "gl'", "nell'", "quell'", "quest'",
                                       "sull'", "tutt'", "un'" ] );
        $pronouns = padSnowballSet ( [ 'ci', 'gli', 'la', 'le', 'li', 'lo', 'mi', 'ne', 'si', 'ti', 'vi', 'sene', 'gliela', 'gliele',
                                       'glieli', 'glielo', 'gliene', 'mela', 'mele', 'meli', 'melo', 'mene', 'tela', 'tele', 'teli',
                                       'telo', 'tene', 'cela', 'cele', 'celi', 'celo', 'cene', 'vela', 'vele', 'veli', 'velo', 'vene' ] );
        $gerunds  = padSnowballSet ( [ 'ando', 'endo', 'ar', 'er', 'ir' ] );

        $group = [];

        foreach ( [ 1 => 'anza anze ico ici ica ice iche ichi ismo ismi abile abili ibile ibili ista iste isti istà istè istì oso osi
                          osa ose mente atrice atrici ante anti',
                    2 => 'azione azioni atore atori',
                    3 => 'logia logie',
                    4 => 'uzione uzioni usione usioni',
                    5 => 'enza enze',
                    6 => 'amento amenti imento imenti',
                    7 => 'amente',
                    8 => 'ità',
                    9 => 'ivo ivi iva ive' ] as $n => $list )
          foreach ( preg_split ( '/\s+/', $list ) as $one )
            $group [$one] = $n;

        $standard = padSnowballSet ( array_keys ( $group ) );
        $amente   = padSnowballSet ( [ 'iv', 'os', 'ic', 'abil' ] );
        $ita      = padSnowballSet ( [ 'abil', 'ic', 'iv' ] );
        $verb     = padSnowballSet ( preg_split ( '/\s+/', 'ammo ando ano are arono asse assero assi assimo ata ate ati ato ava
          avamo avano avate avi avo emmo enda ende endi endo erà erai eranno ere erebbe erebbero erei eremmo eremo ereste eresti
          erete erò erono essero ete eva evamo evano evate evi evo Yamo iamo immo irà irai iranno ire irebbe irebbero irei iremmo
          iremo ireste iresti irete irò irono isca iscano isce isci isco iscono issero ita ite iti ito iva ivamo ivano ivate ivi
          ivo ono uta ute uti uto ar ir' ) );

      }

      $z = new PadSnowball ( $word );

      // elisions: d', l', dell', un' ... before a word, the longest, and not the whole word.

      $z->bra = $z->c = 0;

      if ( $z->among ( $elisions ) !== NULL and $z->c < $z->l ) {
        $z->ket = $z->c;
        $z->del ();
      }

      // prelude: the acute accents grave, the u of qu a consonant, U; then a u or an i between
      // vowels a consonant, U or I - left to right, so of uiu only the i.

      $z = new PadSnowball ( strtr ( $z->word (), [ 'á' => 'à', 'é' => 'è', 'í' => 'ì', 'ó' => 'ò', 'ú' => 'ù', 'qu' => 'qU' ] ) );

      for ( $i = 1; $i < $z->l - 1; $i++ )
        if ( ( $z->p [$i] === 'u' or $z->p [$i] === 'i' ) and isset ( $v [ $z->p [ $i - 1 ] ] ) and isset ( $v [ $z->p [ $i + 1 ] ] ) )
          $z->p [$i] = strtoupper ( $z->p [$i] );

      // mark_regions: RV after a vowel and a consonant up to the next vowel, after two vowels
      // up to the next consonant, after divan, or after the third letter of a consonant and a
      // vowel; R1 and R2 as in every Snowball stemmer.

      $pV = $p1 = $p2 = $z->l;
      $z->c = $ok = 0;

      if ( $z->in ( $v ) ) {
        $c  = $z->c;
        $ok = ( ( $z->out ( $v ) and $z->past ( $v ) ) or ( $z->to ( $c ) and $z->in ( $v ) and $z->pastNon ( $v ) ) );
      }

      if ( ! $ok )
        $ok = ( $z->to ( 0 ) and $z->eq ( 'divan' ) );

      if ( ! $ok and $z->to ( 0 ) and $z->out ( $v ) ) {
        $c  = $z->c;
        $ok = ( ( $z->out ( $v ) and $z->past ( $v ) ) or ( $z->to ( $c ) and $z->in ( $v ) and $z->c < $z->l and $z->to ( $z->c + 1 ) ) );
      }

      if ( $ok )
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

      // A suffix after the one just deleted, deleted when in R2.

      $more = function ( $set ) use ( $z, $R2 ) {
        $keep   = $z->l - $z->c;
        $z->ket = $z->c;
        $s      = is_string ( $set ) ? ( $z->eqB ( $set ) ? $set : NULL ) : $z->amongB ( $set );
        if ( $s !== NULL ) {
          $z->bra = $z->c;
          if ( $R2 () ) {
            $z->del ();
            return $s;
          }
        }
        $z->c = $z->l - $keep;
        return NULL;
      };

      // attached_pronoun: -ci, -gli, -lo, -glielo ... after -ando or -endo in RV, deleted;
      // after -ar, -er or -ir in RV, an e: portarlo is portare.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->amongB ( $pronouns ) !== NULL ) {
        $z->bra = $z->c;
        $s      = $z->amongB ( $gerunds );
        if ( $s !== NULL and $RV () ) {
          if ( $s === 'ando' or $s === 'endo' )
            $z->del ();
          else
            $z->from ( 'e' );
        }
      }

      // standard_suffix, else verb_suffix.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $standard );
      $done   = FALSE;

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $n      = $group [$s];

        if ( $n == 6 ? $RV () : ( $n == 7 ? $R1 () : $R2 () ) ) {

          $done = TRUE;

          if ( $n == 3 )
            $z->from ( 'log' );
          elseif ( $n == 4 )
            $z->from ( 'u' );
          elseif ( $n == 5 )
            $z->from ( 'ente' );
          else {
            $z->del ();
            if ( $n == 2 )
              $more ( 'ic' );
            elseif ( $n == 7 ) {
              if ( $more ( $amente ) === 'iv' )
                $more ( 'at' );
            } elseif ( $n == 8 )
              $more ( $ita );
            elseif ( $n == 9 ) {
              if ( $more ( 'at' ) !== NULL )
                $more ( 'ic' );
            }
          }

        }

      }

      if ( ! $done ) {

        $z->c = $z->l;

        if ( $z->c >= $pV ) {

          $lb     = $z->lb;
          $z->lb  = $pV;
          $z->ket = $z->c;
          $s      = $z->amongB ( $verb );
          $z->bra = $z->c;
          $z->lb  = $lb;

          if ( $s !== NULL )
            $z->del ();

        }

      }

      // vowel_suffix: a final a, e, i or o in RV, and an i before it in RV; the h of a final
      // ch or gh in RV.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->inB ( $aeio ) ) {
        $z->bra = $z->c;
        if ( $RV () ) {
          $z->del ();
          $z->ket = $z->c;
          if ( $z->eqB ( 'i' ) ) {
            $z->bra = $z->c;
            if ( $RV () )
              $z->del ();
          }
        }
      }

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->eqB ( 'h' ) ) {
        $z->bra = $z->c;
        if ( $z->inB ( $cg ) and $RV () )
          $z->del ();
      }

      // postlude: I and U back to i and u.

      return strtr ( $z->word (), [ 'I' => 'i', 'U' => 'u' ] );

    }

  ];

?>
