<?php

  // de - German: Snowball's stop words and its German stemmer as Snowball 3 has it
  // (snowballstem.org/algorithms/german), step for step after german.sbl: ß is ss, ae, oe and
  // ue are the umlauts they spell, and the stem is written without umlauts - Häuser and
  // Hauser are both haus, aufeinanderfolgenden aufeinanderfolg, Kategorien kategori.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Deutsch',

    'stopwords' => 'aber alle allem allen aller alles als also am an ander andere anderem anderen anderer
      anderes anderm andern anderr anders auch auf aus bei bin bis bist da damit dann der den
      des dem die das daß derselbe derselben denselben desselben demselben dieselbe dieselben
      dasselbe dazu dein deine deinem deinen deiner deines denn derer dessen dich dir du dies
      diese diesem diesen dieser dieses doch dort durch ein eine einem einen einer eines einig
      einige einigem einigen einiger einiges einmal er ihn ihm es etwas euer eure eurem euren
      eurer eures für gegen gewesen hab habe haben hat hatte hatten hier hin hinter ich mich
      mir ihr ihre ihrem ihren ihrer ihres euch im in indem ins ist jede jedem jeden jeder jedes
      jene jenem jenen jener jenes jetzt kann kein keine keinem keinen keiner keines können
      könnte machen man manche manchem manchen mancher manches mein meine meinem meinen meiner
      meines mit muss musste nach nicht nichts noch nun nur ob oder ohne sehr sein seine seinem
      seinen seiner seines selbst sich sie ihnen sind so solche solchem solchen solcher solches
      soll sollte sondern sonst über um und uns unse unsem unsen unser unses unter viel vom von
      vor während war waren warst was weg weil weiter welche welchem welchen welcher welches
      wenn werde werden wie wieder will wir wird wirst wo wollen wollte würde würden zu zum
      zur zwar zwischen',

    'stem' => function ( $word ) {

      static $v, $sEnding, $stEnding, $etEnding, $pre, $post, $a2, $a3, $a4, $a5, $a6, $a7;

      if ( ! $v ) {
        $v        = padSnowballGroup ( 'aeiouyäöü' );
        $sEnding  = padSnowballGroup ( 'bdfghklmnrt' );
        $stEnding = padSnowballGroup ( 'bdfghklmnt' );
        $etEnding = padSnowballGroup ( 'dfgklmnrstUzä' );
        $pre      = padSnowballSet ( [ '', 'ß', 'ae', 'oe', 'ue', 'qu' ] );
        $post     = padSnowballSet ( [ '', 'Y', 'U', 'ä', 'ö', 'ü' ] );
        $a2       = padSnowballSet ( [ 'em', 'ern', 'er', 'erin', 'erinnen', 'e', 'en', 'es', 's', 'ln', 'lns' ] );
        $a3       = padSnowballSet ( [ 'geordn', 'intern', 'plan', 'tick', 'tr' ] );
        $a4       = padSnowballSet ( [ 'en', 'er', 'est', 'st', 'et' ] );
        $a5       = padSnowballSet ( [ 'lich', 'ig' ] );
        $a6       = padSnowballSet ( [ 'end', 'ung', 'ig', 'ik', 'isch', 'lich', 'heit', 'keit' ] );
        $a7       = padSnowballSet ( [ "'s", "'sch", "'" ] );
      }

      $z = new PadSnowball ( $word );

      // prelude: u and y between vowels are consonants, U and Y; then ß is ss, ae ä, oe ö and
      // ue ü - but not the ue of qu.

      for ( $i = 1; $i < $z->l - 1; $i++ )
        if ( ( $z->p [$i] === 'u' or $z->p [$i] === 'y' ) and isset ( $v [ $z->p [ $i - 1 ] ] ) and isset ( $v [ $z->p [ $i + 1 ] ] ) )
          $z->p [$i] = strtoupper ( $z->p [$i] );

      for ( $z->c = 0; ; ) {
        $z->bra = $z->c;
        $s      = $z->among ( $pre );
        $z->ket = $z->c;
        if ( $s === '' ) {
          if ( $z->c >= $z->l )
            break;
          $z->c++;
        } elseif ( $s !== 'qu' )
          $z->from ( [ 'ß' => 'ss', 'ae' => 'ä', 'oe' => 'ö', 'ue' => 'ü' ] [$s] );
      }

      // mark_regions: R1 begins three letters in at the least.

      $p1 = $p2 = $z->l;
      $z->c = 0;

      if ( $z->l >= 3 and $z->past ( $v ) and $z->pastNon ( $v ) ) {
        $p1 = max ( 3, $z->c );
        if ( $z->past ( $v ) and $z->pastNon ( $v ) )
          $p2 = $z->c;
      }

      $R1 = fn () => $p1 <= $z->c;
      $R2 = fn () => $p2 <= $z->c;

      // standard_suffix, the first step: -em (not of -system), -ern, -er, -erin, -erinnen,
      // -e, -en, -es (and the s of -niss), -s after an s-ending, -ln and -lns.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $a2 );

      if ( $s !== NULL and $z->bra () and $R1 () ) {

        if ( $s === 'em' ) {
          if ( ! $z->eqB ( 'syst' ) )
            $z->del ();
        } elseif ( in_array ( $s, [ 'ern', 'er', 'erin', 'erinnen' ], TRUE ) )
          $z->del ();
        elseif ( in_array ( $s, [ 'e', 'en', 'es' ], TRUE ) ) {
          $z->del ();
          $keep   = $z->l - $z->c;
          $z->ket = $z->c;
          if ( $z->eqB ( 's' ) and $z->bra () and $z->eqB ( 'nis' ) )
            $z->del ();
          else
            $z->c = $z->l - $keep;
        } elseif ( $s === 's' ) {
          if ( $z->inB ( $sEnding ) )
            $z->del ();
        } else
          $z->from ( 'l' );

      }

      // The second: -en, -er, -est, -st after an st-ending three letters in, -et after an
      // et-ending but not in geordnet, internet, planet, ticket or -tret.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $a4 );

      if ( $s !== NULL and $z->bra () and $R1 () ) {

        if ( $s === 'st' ) {
          if ( $z->inB ( $stEnding ) and $z->c - 3 >= $z->lb )
            $z->del ();
        } elseif ( $s === 'et' ) {
          $keep = $z->l - $z->c;
          if ( $z->inB ( $etEnding ) ) {
            $z->c = $z->l - $keep;
            if ( $z->amongB ( $a3 ) === NULL ) {
              $z->c = $z->l - $keep;
              $z->del ();
            }
          }
        } else
          $z->del ();

      }

      // The third, in R2: -end and -ung (and an -ig before, not after an e), -ig, -ik and
      // -isch not after an e, -lich and -heit (and an -er or -en in R1 before), -keit (and a
      // -lich or -ig in R2 before).

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $a6 );

      if ( $s !== NULL and $z->bra () and $R2 () ) {

        if ( $s === 'end' or $s === 'ung' ) {
          $z->del ();
          $keep   = $z->l - $z->c;
          $z->ket = $z->c;
          if ( $z->eqB ( 'ig' ) and $z->bra () and $z->at ( $z->c - 1 ) !== 'e' and $R2 () )
            $z->del ();
          else
            $z->c = $z->l - $keep;
        } elseif ( $s === 'ig' or $s === 'ik' or $s === 'isch' ) {
          if ( ! ( $z->c > $z->lb and $z->at ( $z->c - 1 ) === 'e' ) )
            $z->del ();
        } elseif ( $s === 'lich' or $s === 'heit' ) {
          $z->del ();
          $keep   = $z->l - $z->c;
          $z->ket = $z->c;
          if ( ( $z->eqB ( 'er' ) or $z->eqB ( 'en' ) ) and $z->bra () and $R1 () )
            $z->del ();
          else
            $z->c = $z->l - $keep;
        } else {
          $z->del ();
          $keep   = $z->l - $z->c;
          $z->ket = $z->c;
          if ( $z->amongB ( $a5 ) !== NULL and $z->bra () and $R2 () )
            $z->del ();
          else
            $z->c = $z->l - $keep;
        }

      }

      // The fourth: an apostrophe s, sch or alone, after two letters at the least.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->amongB ( $a7 ) !== NULL ) {
        $z->bra = $z->c;
        if ( $z->c - 1 > $z->lb )
          $z->del ();
      }

      // postlude: Y, U and the umlauts back to y, u, a, o and u.

      for ( $z->c = 0; ; ) {
        $z->bra = $z->c;
        $s      = $z->among ( $post );
        $z->ket = $z->c;
        if ( $s === '' ) {
          if ( $z->c >= $z->l )
            break;
          $z->c++;
        } else
          $z->from ( [ 'Y' => 'y', 'U' => 'u', 'ä' => 'a', 'ö' => 'o', 'ü' => 'u' ] [$s] );
      }

      return $z->word ();

    }

  ];

?>
