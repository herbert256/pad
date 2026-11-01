<?php

  // pt - Portuguese: Snowball's stop words and its Portuguese stemmer as Snowball 3.1 has it
  // (snowballstem.org/algorithms/portuguese), step for step after portuguese.sbl - a standard
  // suffix or else a verb ending (and an i after a c then), else a residual vowel, then a
  // final e and the u of gue or the i of cie, and ç as c - for Portuguese as written in
  // Portugal and in Brazil alike, Snowball having one stemmer for both: informações is
  // inform, rapidamente rapid, brasileira brasileir, cidades cidad.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Português',

    'stopwords' => 'de a o que e do da em um para com não uma os no se na por mais as dos como mas ao ele das à
      seu sua ou quando muito nos já eu também só pelo pela até isso ela entre depois sem mesmo
      aos seus quem nas me esse eles você essa num nem suas meu às minha numa pelos elas qual nós
      lhe deles essas esses pelas este dele tu te vocês vos lhes meus minhas teu tua teus tuas
      nosso nossa nossos nossas dela delas esta estes estas aquele aquela aqueles aquelas isto
      aquilo estou está estamos estão estive esteve estivemos estiveram estava estávamos estavam
      estivera estivéramos esteja estejamos estejam estivesse estivéssemos estivessem estiver
      estivermos estiverem hei há havemos hão houve houvemos houveram houvera houvéramos haja
      hajamos hajam houvesse houvéssemos houvessem houver houvermos houverem houverei houverá
      houveremos houverão houveria houveríamos houveriam sou somos são era éramos eram fui foi
      fomos foram fora fôramos seja sejamos sejam fosse fôssemos fossem for formos forem serei
      será seremos serão seria seríamos seriam tenho tem temos tém tinha tínhamos tinham tive teve
      tivemos tiveram tivera tivéramos tenha tenhamos tenham tivesse tivéssemos tivessem tiver
      tivermos tiverem terei terá teremos terão teria teríamos teriam',

    'stem' => function ( $word ) {

      static $v, $standard, $group, $amente, $mente, $idade, $verb, $residual, $form;

      if ( ! $v ) {

        $v = padSnowballGroup ( 'aeiouáéíóúâêô' );

        $group = [];

        foreach ( [ 1 => 'eza ezas ico ica icos icas ismo ismos ável ível ista istas oso osa osos osas amento amentos imento
                          imentos adora ador aça~o adoras adores aço~es ante antes ância',
                    2 => 'logia logias',
                    3 => 'uça~o uço~es',
                    4 => 'ência ências',
                    5 => 'amente',
                    6 => 'mente',
                    7 => 'idade idades',
                    8 => 'iva ivo ivas ivos',
                    9 => 'ira iras' ] as $n => $list )
          foreach ( preg_split ( '/\s+/', $list ) as $one )
            $group [$one] = $n;

        $standard = padSnowballSet ( array_keys ( $group ) );
        $amente   = padSnowballSet ( [ 'iv', 'os', 'ic', 'ad' ] );
        $mente    = padSnowballSet ( [ 'ante', 'avel', 'ível' ] );
        $idade    = padSnowballSet ( [ 'abil', 'ic', 'iv' ] );
        $verb     = padSnowballSet ( preg_split ( '/\s+/', 'ada ida ia aria eria iria ará ara erá era irá ava asse esse isse aste
          este iste ei arei erei irei am iam ariam eriam iriam aram eram iram avam em arem erem irem assem essem issem ado ido ando
          endo indo ara~o era~o ira~o ar er ir as adas idas ias arias erias irias arás aras erás eras irás avas es ardes erdes
          irdes ares eres ires asses esses isses astes estes istes is ais eis íeis aríeis eríeis iríeis áreis areis éreis ereis
          íreis ireis ásseis ésseis ísseis áveis ados idos ámos amos íamos aríamos eríamos iríamos áramos éramos íramos ávamos
          emos aremos eremos iremos ássemos êssemos íssemos imos armos ermos irmos eu iu ou ira iras' ) );
        $residual = padSnowballSet ( [ 'os', 'a', 'i', 'o', 'á', 'í', 'ó' ] );
        $form     = padSnowballSet ( [ 'e', 'é', 'ê', 'ç' ] );

      }

      // prelude: ã and õ as a~ and o~, two letters, the second no vowel.

      $z = new PadSnowball ( strtr ( $word, [ 'ã' => 'a~', 'õ' => 'o~' ] ) );

      // mark_regions: RV after a vowel and a consonant up to the next vowel, after two vowels
      // up to the next consonant, or after the third letter of a consonant and a vowel; R1 and
      // R2 as in every Snowball stemmer.

      $pV = $p1 = $p2 = $z->l;
      $z->c = 0;

      if ( $z->in ( $v ) ) {
        $c = $z->c;
        if ( ( $z->out ( $v ) and $z->past ( $v ) ) or ( $z->to ( $c ) and $z->in ( $v ) and $z->pastNon ( $v ) ) )
          $pV = $z->c;
      } elseif ( $z->out ( $v ) ) {
        $c = $z->c;
        if ( ( $z->out ( $v ) and $z->past ( $v ) ) or ( $z->to ( $c ) and $z->in ( $v ) and $z->c < $z->l and $z->to ( $z->c + 1 ) ) )
          $pV = $z->c;
      }

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

      // standard_suffix, else verb_suffix - and when either took a suffix, an i after a c in
      // RV; when neither did, residual_suffix: -os, -a, -i, -o, -á, -í, -ó in RV.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $standard );
      $done   = FALSE;

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $n      = $group [$s];

        if ( $n == 9 ) {
          if ( $RV () and $z->eqB ( 'e' ) ) {
            $done = TRUE;
            $z->from ( 'ir' );
          }
        } elseif ( $n == 5 ? $R1 () : $R2 () ) {

          $done = TRUE;

          if ( $n == 2 )
            $z->from ( 'log' );
          elseif ( $n == 3 )
            $z->from ( 'u' );
          elseif ( $n == 4 )
            $z->from ( 'ente' );
          else {
            $z->del ();
            if ( $n == 5 ) {
              if ( $more ( $amente ) === 'iv' )
                $more ( 'at' );
            } elseif ( $n == 6 )
              $more ( $mente );
            elseif ( $n == 7 )
              $more ( $idade );
            elseif ( $n == 8 )
              $more ( 'at' );
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

          if ( $s !== NULL ) {
            $z->del ();
            $done = TRUE;
          }

        }

      }

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $done ) {
        if ( $z->eqB ( 'i' ) ) {
          $z->bra = $z->c;
          if ( $z->at ( $z->c - 1 ) === 'c' and $z->c > $z->lb and $RV () )
            $z->del ();
        }
      } elseif ( $z->amongB ( $residual ) !== NULL ) {
        $z->bra = $z->c;
        if ( $RV () )
          $z->del ();
      }

      // residual_form: a final e, é or ê in RV, and the u of gue or the i of cie in RV; a
      // final ç a c.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $form );

      if ( $s === 'ç' ) {
        $z->bra = $z->c;
        $z->from ( 'c' );
      } elseif ( $s !== NULL ) {
        $z->bra = $z->c;
        if ( $RV () ) {
          $z->del ();
          $z->ket = $z->c;
          $last   = $z->at ( $z->c - 1 );
          if ( ( $last === 'u' or $last === 'i' ) and $z->c - 1 > $z->lb and $z->at ( $z->c - 2 ) === ( $last === 'u' ? 'g' : 'c' ) ) {
            $z->c--;
            $z->bra = $z->c;
            if ( $RV () )
              $z->del ();
          }
        }
      }

      // postlude: a~ and o~ back to ã and õ.

      return strtr ( $z->word (), [ 'a~' => 'ã', 'o~' => 'õ' ] );

    }

  ];

?>
