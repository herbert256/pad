<?php

  // nl - Dutch: Snowball's stop words and its Dutch stemmer as Snowball 3 has it - the
  // algorithm of Wessel Kraaij and Renée Pohlmann (snowballstem.org/algorithms/dutch), step
  // for step after dutch.sbl: plurals, diminutives and adjective endings off, a vowel
  // lengthened where the spelling shortened it, the ge- of a participle dropped - maken and
  // maak are maak, huisjes huis, gelopen loop.
  //
  // The stop words and the algorithm are Snowball's (BSD licence; the algorithm Kraaij and
  // Pohlmann's, written for Snowball by Olly Betts); subs/search/_lib/snowball.php is the
  // runtime the steps are written in.

  return [

    'name' => 'Nederlands',

    'stopwords' => 'de en van ik te dat die in een hij het niet zijn is was op aan met als voor had er maar om
      hem dan zou of wat mijn men dit zo door over ze zich bij ook tot je mij uit der daar haar
      naar heb hoe heeft hebben deze u want nog zal me zij nu ge geen omdat iets worden toch al
      waren veel meer doen toen moet ben zonder kan hun dus alles onder ja eens hier wie werd
      altijd doch wordt wezen kunnen ons zelf tegen na reeds wil kon niets uw iemand geweest
      andere',

    'stem' => function ( $word ) {

      static $v, $vWX, $AEIOU, $AIOU, $E, $long, $s1, $s2, $s3, $s4, $s4b, $s6, $s7, $s1c, $geNot, $trema, $groups;

      if ( ! $v ) {

        $A = 'aäáàâ'; $Ev = 'eëéèê'; $I = 'iïíìî'; $O = 'oöóòô'; $U = 'uüúùû';

        $AIOU  = padSnowballGroup ( $A . $I . $O . $U );
        $AEIOU = padSnowballGroup ( $A . $Ev . $I . $O . $U );
        $E     = padSnowballGroup ( $Ev );
        $v     = padSnowballGroup ( $A . $Ev . $I . $O . $U . 'y' );
        $vWX   = padSnowballGroup ( $A . $Ev . $I . $O . $U . 'ywx' );

        $groups = [
          'long' => [ 'a' => 1, 'ä' => 1, 'á' => 1, 'à' => 1, 'â' => 1, 'o' => 1, 'ö' => 1, 'ó' => 1, 'ò' => 1, 'ô' => 1,
                      'u' => 1, 'ü' => 1, 'ú' => 1, 'ù' => 1, 'û' => 1, 'e' => 2, 'é' => 2, 'è' => 2, 'ê' => 2, 'eë' => 3, 'ië' => 4 ],
          's3'   => [ 'atie' => 1, 'iteit' => 2, 'heid' => 3, 'sel' => 3, 'ster' => 3, 'rder' => 4, 'ing' => 5, 'isme' => 5,
                      'erij' => 5, 'arij' => 6, 'fie' => 7, 'gie' => 8, 'tst' => 9, 'dst' => 10 ],
          's4'   => [ 'ioneel' => 1, 'atief' => 2, 'baar' => 3, 'achtig' => 3, 'achtiger' => 3, 'achtigst' => 3, 'naar' => 4,
                      'laar' => 5, 'raar' => 6, 'tant' => 7, 'lijker' => 8, 'lijkst' => 8, 'eriger' => 9, 'erigst' => 9,
                      'erig' => 9, 'end' => 9 ]
        ];

        $long  = padSnowballSet ( array_keys ( $groups ['long'] ) );
        $s1    = padSnowballSet ( [ "'s", 's', 'ies', 'es', 'és', 'aus', 'en', 'nde' ] );
        $s2    = padSnowballSet ( [ 'je', 'ge', 'lijke', 'ische', 'de', 'te', 'se', 're', 'le', 'ene', 'ieve' ] );
        $s3    = padSnowballSet ( array_keys ( $groups ['s3'] ) );
        $s4    = padSnowballSet ( array_keys ( $groups ['s4'] ) );
        $s4b   = padSnowballSet ( [ 'iger', 'igst', 'ig' ] );
        $s6    = padSnowballSet ( [ 'bb', 'cc', 'dd', 'ff', 'gg', 'hh', 'jj', 'kk', 'll', 'mm', 'nn', 'pp', 'qq', 'rr', 'ss',
                                    'tt', 'vv', 'ww', 'xx', 'zz', 'v', 'z' ] );
        $s7    = padSnowballSet ( [ 'kt', 'ft', 'pt' ] );
        $s1c   = padSnowballSet ( [ 'd', 't' ] );
        $geNot = padSnowballSet ( [ '', 'eft', 'val', 'vali', 'vaa', 'vare' ] );
        $trema = padSnowballSet ( [ 'ë', 'ï' ] );

      }

      $z  = new PadSnowball ( $word );
      $p1 = $p2 = 0;

      // measure: R1 after the first consonants, vowels (ij a vowel too) and one consonant; R2
      // after the next such run.

      $measure = function () use ( $z, $v, &$p1, &$p2 ) {
        $p1 = $p2 = $z->l;
        $z->c = 0;
        foreach ( [ 1, 2 ] as $n ) {
          while ( $z->out ( $v ) ) ;
          $vowels = 0;
          while ( $z->eq ( 'ij' ) or $z->in ( $v ) )
            $vowels++;
          if ( ! $vowels or ! $z->out ( $v ) )
            break;
          if ( $n == 1 ) $p1 = $z->c; else $p2 = $z->c;
        }
        $z->c = 0;
      };

      // R1 and R2 by reference: the measure is taken again after a ge- comes off.

      $R1 = function () use ( $z, &$p1 ) { return $p1 <= $z->c; };
      $R2 = function () use ( $z, &$p2 ) { return $p2 <= $z->c; };

      // V: a vowel or ij before the cursor; VX: the same one letter further; C: a consonant
      // that is not the j of ij. None moves the cursor.

      $V = function () use ( $z, $v ) {
        $keep = $z->l - $z->c;
        $ok   = ( $z->inB ( $v ) or $z->eqB ( 'ij' ) );
        $z->c = $z->l - $keep;
        return $ok;
      };

      $VX = function () use ( $z, $v ) {
        $keep = $z->l - $z->c;
        $ok   = ( $z->c > $z->lb and $z->to ( $z->c - 1 ) and ( $z->inB ( $v ) or $z->eqB ( 'ij' ) ) );
        $z->c = $z->l - $keep;
        return $ok;
      };

      $C = function () use ( $z, $v ) {
        $keep = $z->l - $z->c;
        $ok   = ( ! $z->eqB ( 'ij' ) and $z->to ( $z->l - $keep ) and $z->outB ( $v ) );
        $z->c = $z->l - $keep;
        return $ok;
      };

      // lengthen_V: after a consonant (not w or x), a vowel the spelling shortened written
      // double again - maak from mak.

      $lengthen = function () use ( $z, $vWX, $AEIOU, $AIOU, $E, $long, $groups ) {

        $keep = $z->l - $z->c;

        if ( $z->outB ( $vWX ) ) {

          $z->ket = $z->c;
          $s      = $z->amongB ( $long );

          if ( $s !== NULL ) {

            $z->bra = $z->c;
            $g      = $groups ['long'] [$s];
            $here   = $z->l - $z->c;
            $double = FALSE;

            if ( $g == 1 )
              $double = ( $z->outB ( $AEIOU ) or $z->c <= $z->lb );
            elseif ( $g == 2 ) {
              if ( $z->outB ( $AEIOU ) or $z->c <= $z->lb ) {
                $at = $z->l - $z->c;
                if ( ! ( $z->inB ( $AIOU ) or ( $z->inB ( $E ) and $z->c <= $z->lb ) ) ) {
                  $z->c   = $z->l - $at;
                  $double = ! ( $z->c > $z->lb and $z->to ( $z->c - 1 ) and $z->inB ( $AIOU ) and $z->outB ( $AEIOU ) );
                }
              }
            } else
              $z->from ( $g == 3 ? 'eëe' : 'iee' );

            if ( $double ) {
              $z->c = $z->l - $here;
              $ch   = $z->slice ();
              $at   = $z->c;
              $z->insert ( $z->c, $z->c, $ch );
              $z->c = $at;
            }

          }

        }

        $z->c = $z->l - $keep;

      };

      $measure ();

      $z->lb = 0;

      // Step_1: 's, -s, -ies, -es, -és, -aus, -en, -nde.

      $stemmed = FALSE;

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s1 );

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $at     = $z->l - $z->c;
        $back   = fn () => $z->c = $z->l - $at;
        $ok     = TRUE;

        if ( $s === "'s" )
          $z->del ();
        elseif ( $s === 's' ) {
          $ok = $R1 () && ! ( $z->eqB ( 't' ) && $R1 () ) && $back () && $C ();
          if ( $ok ) $z->del ();
        } elseif ( $s === 'ies' ) {
          $ok = $R1 ();
          if ( $ok ) $z->from ( 'ie' );
        } elseif ( $s === 'es' ) {
          if ( $z->eqB ( 'ar' ) and $R1 () and $C () ) {
            $back ();
            $z->del ();
            $lengthen ();
          } elseif ( $back () and $z->eqB ( 'er' ) and $R1 () and $C () ) {
            $back ();
            $z->del ();
          } else {
            $back ();
            $ok = $R1 () && $C ();
            if ( $ok ) $z->from ( 'e' );
          }
        } elseif ( $s === 'és' ) {
          $ok = $R1 ();
          if ( $ok ) $z->from ( 'é' );
        } elseif ( $s === 'aus' ) {
          $ok = $R1 () && $V ();
          if ( $ok ) $z->from ( 'au' );
        } elseif ( $s === 'en' ) {
          if ( $z->eqB ( 'hed' ) and $R1 () ) {
            $z->bra = $z->c;
            $z->from ( 'heid' );
          } elseif ( $back () and $z->eqB ( 'nd' ) )
            $z->del ();
          elseif ( $back () and $z->eqB ( 'd' ) and $R1 () and $C () ) {
            $z->bra = $z->c;
            $z->del ();
          } elseif ( $back () and ( $z->eqB ( 'i' ) or $z->eqB ( 'j' ) ) and $V () )
            $z->del ();
          else {
            $back ();
            $ok = $R1 () && $C ();
            if ( $ok ) {
              $z->del ();
              $lengthen ();
            }
          }
        } else
          $z->from ( 'nd' );

        $stemmed = $ok;

      }

      // Step_2: -je (diminutives), -ge, -lijke, -ische, -de, -te, -se, -re, -le, -ene, -ieve.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s2 );

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $at     = $z->l - $z->c;
        $back   = fn () => $z->c = $z->l - $at;
        $ok     = TRUE;

        if ( $s === 'je' ) {
          if ( $z->eqB ( "'t" ) ) {
            $z->bra = $z->c;
            $z->del ();
          } elseif ( $back () and $z->eqB ( 'et' ) and $z->bra () and $R1 () and $C () )
            $z->del ();
          elseif ( $back () and $z->eqB ( 'rnt' ) ) {
            $z->bra = $z->c;
            $z->from ( 'rn' );
          } elseif ( $back () and $z->eqB ( 't' ) and $z->bra () and $R1 () and $VX () )
            $z->del ();
          elseif ( $back () and $z->eqB ( 'ink' ) ) {
            $z->bra = $z->c;
            $z->from ( 'ing' );
          } elseif ( $back () and $z->eqB ( 'mp' ) ) {
            $z->bra = $z->c;
            $z->from ( 'm' );
          } elseif ( $back () and $z->eqB ( "'" ) and $z->bra () and $R1 () )
            $z->del ();
          else {
            $back ();
            $z->bra = $z->c;
            $ok     = $R1 () && $C ();
            if ( $ok ) $z->del ();
          }
        } elseif ( ! $R1 () )
          $ok = FALSE;
        elseif ( $s === 'ge' )    $z->from ( 'g' );
        elseif ( $s === 'lijke' ) $z->from ( 'lijk' );
        elseif ( $s === 'ische' ) $z->from ( 'isch' );
        elseif ( $s === 'te' )    $z->from ( 't' );
        elseif ( $s === 'se' )    $z->from ( 's' );
        elseif ( $s === 're' )    $z->from ( 'r' );
        elseif ( $s === 'le' ) {
          $z->del ();
          $z->insert ( $z->c, $z->c, 'l' );
          $lengthen ();
        } elseif ( ! $C () )
          $ok = FALSE;
        elseif ( $s === 'de' )
          $z->del ();
        elseif ( $s === 'ene' ) {
          $z->del ();
          $z->insert ( $z->c, $z->c, 'en' );
          $lengthen ();
        } else
          $z->from ( 'ief' );

        $stemmed = $stemmed || $ok;

      }

      // Step_3: -atie, -iteit, -heid, -sel, -ster, -rder, -ing, -isme, -erij (not of
      // schilderij), -arij, -fie, -gie, -tst, -dst.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s3 );

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $g      = $groups ['s3'] [$s];
        $ok     = TRUE;

        if ( $g == 4 )
          $z->from ( 'r' );
        elseif ( $g == 5 and $z->eqB ( 'ild' ) )
          $z->from ( 'er' );
        elseif ( ( $g == 7 or $g == 8 ) ? ! $R2 () : ! $R1 () )
          $ok = FALSE;
        elseif ( $g == 1 )
          $z->from ( 'eer' );
        elseif ( $g == 2 or $g == 5 ) {
          $z->del ();
          $lengthen ();
        } elseif ( $g == 3 )
          $z->del ();
        elseif ( $g == 7 or $g == 8 ) {
          $z->del ();
          $z->insert ( $z->c, $z->c, $g == 7 ? 'f' : 'g' );
          $lengthen ();
        } elseif ( ! $C () )
          $ok = FALSE;
        else
          $z->from ( [ 6 => 'aar', 9 => 't', 10 => 'd' ] [$g] );

        $stemmed = $stemmed || $ok;

      }

      // Step_4: -ioneel, -atief, -baar, -naar, -laar, -raar, -tant, -lijker, -lijkst, -achtig
      // ..., -erig ..., -end; else -ig, -iger, -igst (not of innig).

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s4 );
      $ok     = FALSE;

      if ( $s !== NULL ) {

        $z->bra = $z->c;
        $g      = $groups ['s4'] [$s];

        if ( $R1 () ) {
          $ok = TRUE;
          if     ( $g == 1 ) $z->from ( 'ie' );
          elseif ( $g == 2 ) $z->from ( 'eer' );
          elseif ( $g == 3 ) $z->del ();
          elseif ( $g == 7 ) $z->from ( 'teer' );
          elseif ( $g == 8 ) $z->from ( 'lijk' );
          elseif ( $g == 9 ) {
            $ok = $C ();
            if ( $ok ) {
              $z->del ();
              $lengthen ();
            }
          } else {
            $ok = $V ();
            if ( $ok )
              $z->from ( [ 4 => 'n', 5 => 'l', 6 => 'r' ] [$g] );
          }
        }

      }

      if ( ! $ok ) {

        $z->c   = $z->l;
        $z->ket = $z->c;

        if ( $z->amongB ( $s4b ) !== NULL ) {
          $z->bra = $z->c;
          $at     = $z->l - $z->c;
          if ( $R1 () and ! ( $z->eqB ( 'inn' ) and $z->c <= $z->lb ) and $z->to ( $z->l - $at ) and $C () ) {
            $z->del ();
            $lengthen ();
            $ok = TRUE;
          }
        }

      }

      $stemmed = $stemmed || $ok;

      // Step_1c: after a ge- taken off, a d or t of the participle - not after an n or h in
      // R1, geïnd is ind, and geënt keeps its t.

      $step1c = function () use ( $z, $s1c, $R1, $C ) {
        $z->c   = $z->l;
        $z->ket = $z->c;
        $s      = $z->amongB ( $s1c );
        if ( $s === NULL )
          return;
        $z->bra = $z->c;
        if ( ! $R1 () or ! $C () )
          return;
        $at = $z->l - $z->c;
        if ( $s === 'd' ) {
          if ( $z->eqB ( 'n' ) and $R1 () )
            return;
          $z->c = $z->l - $at;
          if ( $z->eqB ( 'in' ) and $z->c <= $z->lb )
            $z->from ( 'n' );
          else
            $z->del ();
        } else {
          if ( $z->eqB ( 'h' ) and $R1 () )
            return;
          $z->c = $z->l - $at;
          if ( $z->eqB ( 'en' ) and $z->c <= $z->lb )
            return;
          $z->del ();
        }
      };

      // The ge- after it was taken off: an ë or ï after it loses its trema.

      $untrema = function () use ( $z, $trema ) {
        $z->bra = $z->c;
        $s      = $z->among ( $trema );
        if ( $s !== NULL ) {
          $z->ket = $z->c;
          $z->from ( $s === 'ë' ? 'e' : 'i' );
        }
      };

      // A ge- is a prefix when three letters at the least follow, with a vowel and a letter
      // after the vowels.

      $geFollows = function () use ( $z, $v ) {
        $at = $z->c;
        if ( $at + 3 > $z->l )
          return FALSE;
        while ( ! ( $z->eq ( 'ij' ) or $z->in ( $v ) ) ) {
          if ( $z->c >= $z->l )
            return FALSE;
          $z->c++;
        }
        while ( $z->eq ( 'ij' ) or $z->in ( $v ) ) ;
        $ok   = $z->c < $z->l;
        $z->c = $at;
        return $ok;
      };

      // Lose_prefix: a ge- at the start - not of geeft, geval or gevaar.

      $z->c = 0;

      if ( $z->eq ( 'ge' ) and $geFollows () ) {

        $z->bra = 0;
        $z->ket = 2;
        $not    = $z->among ( $geNot );

        if ( $not === '' or $not === 'vali' ) {
          $z->del ();
          $untrema ();
          $measure ();
          $z->lb = 0;
          $stemmed = TRUE;
          $step1c ();
        }

      }

      // Lose_infix: a ge- inside the word, after its first letter - uitgelopen.

      $z->c = 0;

      if ( $z->l > 0 ) {

        $z->c  = 1;
        $found = FALSE;

        while ( TRUE ) {
          $z->bra = $z->c;
          if ( $z->eq ( 'ge' ) ) {
            $z->ket = $z->c;
            $found  = TRUE;
            break;
          }
          if ( $z->c >= $z->l )
            break;
          $z->c++;
        }

        if ( $found and $geFollows () ) {
          $z->del ();
          $untrema ();
          $measure ();
          $z->lb = 0;
          $stemmed = TRUE;
          $step1c ();
        }

      }

      // Step_7: -kt, -ft, -pt lose the t.

      $z->c   = $z->l;
      $z->ket = $z->c;
      $s      = $z->amongB ( $s7 );

      if ( $s !== NULL ) {
        $z->bra = $z->c;
        $z->from ( $s [0] );
        $stemmed = TRUE;
      }

      // Step_6: a double consonant single (not the nn of inn), a final v f and z s - only when
      // a step before took something.

      if ( $stemmed ) {

        $z->c   = $z->l;
        $z->ket = $z->c;
        $s      = $z->amongB ( $s6 );

        if ( $s !== NULL ) {
          $z->bra = $z->c;
          if ( $s === 'v' )
            $z->from ( 'f' );
          elseif ( $s === 'z' )
            $z->from ( 's' );
          elseif ( ! ( $s === 'nn' and $z->at ( $z->c - 1 ) === 'i' and $z->c - 1 == $z->lb ) )
            $z->from ( $s [0] );
        }

      }

      return $z->word ();

    }

  ];

?>
