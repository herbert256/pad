<?php

  // en - English: Snowball's stop words and its English stemmer, Porter2 as Snowball 3 has it
  // (snowballstem.org/algorithms/english), step for step after english.sbl - running, runs
  // and run are run, generously and generous are generous, traditional tradit.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'English',

    'stopwords' => 'i me my myself we our ours ourselves you your yours yourself yourselves he him his himself
      she her hers herself it its itself they them their theirs themselves what which who whom
      this that these those am is are was were be been being have has had having do does did
      doing would should could ought i\'m you\'re he\'s she\'s it\'s we\'re they\'re i\'ve you\'ve we\'ve
      they\'ve i\'d you\'d he\'d she\'d we\'d they\'d i\'ll you\'ll he\'ll she\'ll we\'ll they\'ll isn\'t
      aren\'t wasn\'t weren\'t hasn\'t haven\'t hadn\'t doesn\'t don\'t didn\'t won\'t wouldn\'t shan\'t
      shouldn\'t can\'t cannot couldn\'t mustn\'t let\'s that\'s who\'s what\'s here\'s there\'s when\'s
      where\'s why\'s how\'s a an the and but if or because as until while of at by for with about
      against between into through during before after above below to from up down in out on off
      over under again further then once here there when where why how all any both each few
      more most other some such no nor not only own same so than too very',

    'stem' => function ( $word ) {

      static $v, $vWXY, $aeo, $validLI, $prefixes, $apostrophe, $s1a, $s1b, $eed, $ing, $after, $s2, $s3, $s4, $s5, $exceptions;

      if ( ! $v ) {

        $v          = padSnowballGroup ( 'aeiouy' );
        $vWXY       = padSnowballGroup ( 'aeiouywxY' );
        $aeo        = padSnowballGroup ( 'aeo' );
        $validLI    = padSnowballGroup ( 'cdeghkmnrt' );
        $prefixes   = padSnowballSet ( [ 'gener', 'commun', 'arsen', 'past', 'univers', 'later', 'emerg', 'organ', 'inter' ] );
        $apostrophe = padSnowballSet ( [ "'", "'s", "'s'" ] );
        $s1a        = padSnowballSet ( [ 'sses', 'ied', 'ies', 's', 'us', 'ss' ] );
        $s1b        = padSnowballSet ( [ '', 'eed', 'eedly', 'ed', 'edly', 'ingly', 'ing' ] );
        $eed        = padSnowballSet ( [ 'proc', 'exc', 'succ' ] );
        $ing        = padSnowballSet ( [ 'y', 'inn', 'out', 'cann', 'herr', 'earr', 'even' ] );
        $after      = padSnowballSet ( [ '', 'at', 'bl', 'iz', 'bb', 'dd', 'ff', 'gg', 'mm', 'nn', 'pp', 'rr', 'tt' ] );

        $s2 = [ 'tional' => 'tion', 'enci' => 'ence', 'anci' => 'ance', 'abli' => 'able', 'entli' => 'ent',
                'izer' => 'ize', 'ization' => 'ize', 'ational' => 'ate', 'ation' => 'ate', 'ator' => 'ate',
                'alism' => 'al', 'aliti' => 'al', 'alli' => 'al', 'fulness' => 'ful', 'ousli' => 'ous',
                'ousness' => 'ous', 'iveness' => 'ive', 'iviti' => 'ive', 'biliti' => 'ble', 'bli' => 'ble',
                'ogist' => 'og', 'ogi' => 'og', 'fulli' => 'ful', 'lessli' => 'less', 'li' => '' ];
        $s3 = [ 'tional' => 'tion', 'ational' => 'ate', 'alize' => 'al', 'icate' => 'ic', 'iciti' => 'ic',
                'ical' => 'ic', 'ful' => '', 'ness' => '', 'ative' => '' ];
        $s4 = padSnowballSet ( [ 'al', 'ance', 'ence', 'er', 'ic', 'able', 'ible', 'ant', 'ement', 'ment', 'ent',
                                 'ism', 'ate', 'iti', 'ous', 'ive', 'ize', 'ion' ] );
        $s5 = padSnowballSet ( [ 'e', 'l' ] );

        $s2 = [ 'map' => $s2, 'set' => padSnowballSet ( array_keys ( $s2 ) ) ];
        $s3 = [ 'map' => $s3, 'set' => padSnowballSet ( array_keys ( $s3 ) ) ];

        $exceptions = [ 'skis' => 'ski', 'skies' => 'sky', 'idly' => 'idl', 'gently' => 'gentl', 'ugly' => 'ugli',
                        'early' => 'earli', 'only' => 'onli', 'singly' => 'singl', 'sky' => 'sky', 'news' => 'news',
                        'howe' => 'howe', 'atlas' => 'atlas', 'cosmos' => 'cosmos', 'bias' => 'bias', 'andes' => 'andes' ];

      }

      // exception1, and a word of fewer than three letters stays as it is.

      if ( isset ( $exceptions [$word] ) )
        return $exceptions [$word];

      if ( mb_strlen ( $word ) < 3 )
        return $word;

      $z = new PadSnowball ( $word );

      // prelude: a leading apostrophe goes; a y at the start or after a vowel is a consonant, Y.

      $found = FALSE;

      if ( $z->at ( 0 ) === "'" ) {
        $z->bra = 0; $z->ket = 1; $z->del ();
      }

      if ( $z->at ( 0 ) === 'y' ) {
        $z->p [0] = 'Y';
        $found    = TRUE;
      }

      for ( $i = 1; $i < $z->l; $i++ )
        if ( $z->p [$i] === 'y' and isset ( $v [ $z->p [ $i - 1 ] ] ) ) {
          $z->p [$i] = 'Y';
          $found     = TRUE;
        }

      // mark_regions

      $p1 = $p2 = $z->l;
      $z->c = 0;

      if ( $z->among ( $prefixes ) !== NULL or ( $z->past ( $v ) and $z->pastNon ( $v ) ) ) {
        $p1 = $z->c;
        if ( $z->past ( $v ) and $z->pastNon ( $v ) )
          $p2 = $z->c;
      }

      $R1 = fn () => $p1 <= $z->c;
      $R2 = fn () => $p2 <= $z->c;

      $shortv = function () use ( $z, $v, $vWXY ) {
        $keep = $z->l - $z->c;
        if ( $z->outB ( $vWXY ) and $z->inB ( $v ) and $z->outB ( $v ) )
          return TRUE;
        $z->c = $z->l - $keep;
        if ( $z->outB ( $v ) and $z->inB ( $v ) and $z->c <= $z->lb )
          return TRUE;
        $z->c = $z->l - $keep;
        return $z->eqB ( 'past' );
      };

      // Step_1a

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->amongB ( $apostrophe ) !== NULL ) {
        $z->bra = $z->c;
        $z->del ();
      }

      $z->ket = $z->c;
      $s      = $z->amongB ( $s1a );
      $z->bra = $z->c;

      if ( $s === 'sses' )
        $z->from ( 'ss' );
      elseif ( $s === 'ied' or $s === 'ies' )
        $z->from ( $z->c - 2 >= $z->lb ? 'i' : 'ie' );
      elseif ( $s === 's' and $z->c > $z->lb ) {
        $z->c--;
        if ( $z->pastB ( $v ) )
          $z->del ();
      }

      // Step_1b

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s1b );
      $z->bra = $z->c;
      $other  = FALSE;

      if ( $s === 'eed' or $s === 'eedly' ) {
        if ( $R1 () ) {
          $keep = $z->l - $z->c;
          if ( ! ( $z->amongB ( $eed ) !== NULL and $z->c <= $z->lb ) ) {
            $z->c = $z->l - $keep;
            $z->from ( 'ee' );
          }
        }
      } elseif ( $s === 'ed' or $s === 'edly' or $s === 'ingly' )
        $other = TRUE;
      elseif ( $s === 'ing' ) {
        $t = $z->amongB ( $ing );
        if ( $t === NULL )
          $other = TRUE;
        elseif ( $t === 'y' ) {
          $keep = $z->l - $z->c;
          if ( $z->outB ( $v ) and $z->c <= $z->lb ) {
            $z->c   = $z->l - $keep;
            $z->bra = $z->c;
            $z->from ( 'ie' );
          } else
            $other = TRUE;
        } elseif ( $z->c > $z->lb )
          $other = TRUE;
      }

      if ( $other ) {

        $z->c = $z->bra;
        $keep = $z->l - $z->c;

        if ( $z->pastB ( $v ) ) {

          $z->c = $z->l - $keep;
          $z->del ();
          $z->ket = $z->bra = $z->c;
          $keep   = $z->l - $z->c;
          $t      = $z->amongB ( $after );

          if ( $t === 'at' or $t === 'bl' or $t === 'iz' )
            $z->from ( 'e' );
          elseif ( $t === '' ) {
            if ( $z->c == $p1 and $shortv () )
              $z->from ( 'e' );
          } elseif ( ! ( $z->inB ( $aeo ) and $z->c <= $z->lb ) ) {
            $z->c   = $z->l - $keep;
            $z->ket = $z->c;
            $z->c--;
            $z->bra = $z->c;
            $z->del ();
          }

        }

      }

      // Step_1c: a y or Y after a consonant that is not the first letter is i.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( in_array ( $z->at ( $z->c - 1 ), [ 'y', 'Y' ], TRUE ) and $z->c > $z->lb ) {
        $z->c--;
        $z->bra = $z->c;
        if ( $z->outB ( $v ) and $z->c > $z->lb )
          $z->from ( 'i' );
      }

      // Step_2 and Step_3: the suffix in R1 replaced.

      foreach ( [ $s2, $s3 ] as $n => $step ) {

        $z->c   = $z->l;
        $z->ket = $z->c;
        $s      = $z->amongB ( $step ['set'] );

        if ( $s === NULL )
          continue;

        $z->bra = $z->c;

        if ( ! $R1 () )
          continue;

        if ( $n == 0 and $s === 'ogi' ) {
          if ( $z->at ( $z->c - 1 ) === 'l' and $z->c > $z->lb )
            $z->from ( 'og' );
        } elseif ( $n == 0 and $s === 'li' ) {
          if ( $z->inB ( $validLI ) )
            $z->del ();
        } elseif ( $n == 1 and $s === 'ative' ) {
          if ( $R2 () )
            $z->del ();
        } else
          $z->from ( $step ['map'] [$s] );

      }

      // Step_4: the suffix in R2 deleted - ion after an s or a t.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s4 );

      if ( $s !== NULL ) {
        $z->bra = $z->c;
        if ( $R2 () and ( $s !== 'ion' or ( $z->c > $z->lb and in_array ( $z->at ( $z->c - 1 ), [ 's', 't' ], TRUE ) ) ) )
          $z->del ();
      }

      // Step_5: a final e in R2, or in R1 after no short syllable; a final l of ll in R2.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s5 );

      if ( $s !== NULL ) {
        $z->bra = $z->c;
        if ( $s === 'e' ) {
          if ( $R2 () )
            $z->del ();
          elseif ( $R1 () ) {
            $keep = $z->l - $z->c;
            if ( ! $shortv () ) {
              $z->c = $z->l - $keep;
              $z->del ();
            }
          }
        } elseif ( $R2 () and $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'l' )
          $z->del ();
      }

      // postlude

      $word = $z->word ();

      return $found ? str_replace ( 'Y', 'y', $word ) : $word;

    }

  ];

?>
