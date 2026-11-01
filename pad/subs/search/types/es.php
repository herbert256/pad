<?php

  // es - Spanish: Snowball's stop words and its Spanish stemmer as Snowball 3 has it
  // (snowballstem.org/algorithms/spanish), step for step after spanish.sbl - an attached
  // pronoun, a standard suffix or else a verb ending, then a residual vowel, and the acute
  // accents gone: canciones is cancion, rápidamente rapid, trabajadores trabaj.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Español',

    'stopwords' => 'de la que el en y a los del se las por un para con no una su al lo como más pero sus le
      ya o este sí porque esta entre cuando muy sin sobre también me hasta hay donde quien
      desde todo nos durante todos uno les ni contra otros ese eso ante ellos e esto mí antes
      algunos qué unos yo otro otras otra él tanto esa estos mucho quienes nada muchos cual
      poco ella estar estas algunas algo nosotros mi mis tú te ti tu tus ellas nosotras
      vosotros vosotras os mío mía míos mías tuyo tuya tuyos tuyas suyo suya suyos suyas
      nuestro nuestra nuestros nuestras vuestro vuestra vuestros vuestras esos esas estoy estás
      está estamos estáis están esté estés estemos estéis estén estaré estarás estará
      estaremos estaréis estarán estaría estarías estaríamos estaríais estarían estaba
      estabas estábamos estabais estaban estuve estuviste estuvo estuvimos estuvisteis
      estuvieron estuviera estuvieras estuviéramos estuvierais estuvieran estuviese estuvieses
      estuviésemos estuvieseis estuviesen estando estado estada estados estadas estad he has ha
      hemos habéis han haya hayas hayamos hayáis hayan habré habrás habrá habremos habréis
      habrán habría habrías habríamos habríais habrían había habías habíamos habíais
      habían hube hubiste hubo hubimos hubisteis hubieron hubiera hubieras hubiéramos
      hubierais hubieran hubiese hubieses hubiésemos hubieseis hubiesen habiendo habido habida
      habidos habidas soy eres es somos sois son sea seas seamos seáis sean seré serás será
      seremos seréis serán sería serías seríamos seríais serían era eras éramos erais
      eran fui fuiste fue fuimos fuisteis fueron fuera fueras fuéramos fuerais fueran fuese
      fueses fuésemos fueseis fuesen siendo sido tengo tienes tiene tenemos tenéis tienen
      tenga tengas tengamos tengáis tengan tendré tendrás tendrá tendremos tendréis
      tendrán tendría tendrías tendríamos tendríais tendrían tenía tenías teníamos
      teníais tenían tuve tuviste tuvo tuvimos tuvisteis tuvieron tuviera tuvieras tuviéramos
      tuvierais tuvieran tuviese tuvieses tuviésemos tuvieseis tuviesen teniendo tenido tenida
      tenidos tenidas tened',

    'stem' => function ( $word ) {

      static $v, $pronouns, $verbs, $verbTo, $standard, $group, $amente, $mente, $idad, $yVerb, $verb, $residual;

      if ( ! $v ) {

        $v        = padSnowballGroup ( 'aeiouáéíóúü' );
        $pronouns = padSnowballSet ( [ 'me', 'se', 'sela', 'selo', 'selas', 'selos', 'la', 'le', 'lo', 'las', 'les', 'los', 'nos' ] );
        $verbTo   = [ 'iéndo' => 'iendo', 'ándo' => 'ando', 'ár' => 'ar', 'ér' => 'er', 'ír' => 'ir',
                      'ando' => '', 'iendo' => '', 'ar' => '', 'er' => '', 'ir' => '', 'yendo' => 'u' ];
        $verbs    = padSnowballSet ( array_keys ( $verbTo ) );

        $group = [];

        foreach ( [ 1 => 'anza anzas ico ica icos icas ismo ismos able ables ible ibles ista istas oso osa osos osas amiento amientos imiento imientos',
                    2 => 'adora ador ación adoras adores aciones ante antes ancia ancias acion',
                    3 => 'logía logías',
                    4 => 'ución uciones ucion',
                    5 => 'encia encias',
                    6 => 'amente',
                    7 => 'mente',
                    8 => 'idad idades',
                    9 => 'iva ivo ivas ivos' ] as $n => $list )
          foreach ( explode ( ' ', $list ) as $one )
            $group [$one] = $n;

        $standard = padSnowballSet ( array_keys ( $group ) );
        $amente   = padSnowballSet ( [ 'iv', 'os', 'ic', 'ad' ] );
        $mente    = padSnowballSet ( [ 'ante', 'able', 'ible' ] );
        $idad     = padSnowballSet ( [ 'abil', 'ic', 'iv' ] );
        $yVerb    = padSnowballSet ( [ 'ya', 'ye', 'yan', 'yen', 'yeron', 'yendo', 'yo', 'yó', 'yas', 'yes', 'yais', 'yamos' ] );
        $verb     = padSnowballSet ( preg_split ( '/\s+/', 'en es éis emos arían arías arán arás aríais aría aréis aríamos aremos ará aré
          erían erías erán erás eríais ería eréis eríamos eremos erá eré irían irías irán irás iríais iría iréis iríamos iremos irá
          iré aba ada ida ía ara iera ad ed id ase iese aste iste an aban ían aran ieran asen iesen aron ieron ado ido ando iendo ió
          ar er ir as abas adas idas ías aras ieras ases ieses ís áis abais íais arais ierais aseis ieseis asteis isteis ados idos
          amos ábamos íamos imos áramos iéramos iésemos ásemos' ) );
        $residual = padSnowballSet ( [ 'os', 'a', 'o', 'á', 'í', 'ó', 'e', 'é' ] );

      }

      $z = new PadSnowball ( $word );

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

      // attached_pronoun: -me, -se, -la ... after -iendo, -ando, -ar, -er, -ir (their accents
      // dropped) or -yendo after a u, in RV.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->amongB ( $pronouns ) !== NULL ) {
        $z->bra = $z->c;
        $s      = $z->amongB ( $verbs );
        if ( $s !== NULL and $RV () ) {
          if ( $verbTo [$s] === 'u' ) {
            if ( $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'u' )
              $z->del ();
          } elseif ( $verbTo [$s] === '' )
            $z->del ();
          else {
            $z->bra = $z->c;
            $z->from ( $verbTo [$s] );
          }
        }
      }

      // standard_suffix, else y_verb_suffix, else verb_suffix.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $standard );
      $done   = FALSE;

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $n      = $group [$s];

        if ( $n == 6 ? $R1 () : $R2 () ) {

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
            elseif ( $n == 6 ) {
              if ( $more ( $amente ) === 'iv' )
                $more ( 'at' );
            } elseif ( $n == 7 )
              $more ( $mente );
            elseif ( $n == 8 )
              $more ( $idad );
            elseif ( $n == 9 )
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
          $s      = $z->amongB ( $yVerb );
          $z->bra = $z->c;
          $z->lb  = $lb;

          if ( $s !== NULL and $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'u' ) {
            $z->c--;
            $z->del ();
            $done = TRUE;
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
            if ( in_array ( $s, [ 'en', 'es', 'éis', 'emos' ], TRUE ) and $z->c - 1 > $z->lb and $z->at ( $z->c - 1 ) === 'u' and $z->at ( $z->c - 2 ) === 'g' )
              $z->bra = $z->c - 1;
            $z->del ();
          }

        }

      }

      // residual_suffix: -os, -a, -o, -á, -í, -ó in RV; -e or -é in RV, and the u of a gu
      // before it.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $residual );

      if ( $s !== NULL ) {
        $z->bra = $z->c;
        if ( $RV () ) {
          $z->del ();
          if ( $s === 'e' or $s === 'é' ) {
            $keep   = $z->l - $z->c;
            $z->ket = $z->c;
            if ( $z->eqB ( 'u' ) ) {
              $z->bra = $z->c;
              if ( $z->at ( $z->c - 1 ) === 'g' and $z->c > $z->lb and $RV () )
                $z->del ();
              else
                $z->c = $z->l - $keep;
            }
          }
        }
      }

      // postlude: the acute accents off.

      return strtr ( $z->word (), [ 'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u' ] );

    }

  ];

?>
