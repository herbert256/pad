<?php

  // ru - Russian: Snowball's stop words and its Russian stemmer as Snowball 3.1 has it
  // (snowballstem.org/algorithms/russian), step for step after russian.sbl - ё read as е,
  // then in RV a perfective gerund, else a reflexive ending and an adjectival, verb or noun
  // ending, a final и, -ост and -ость in R2, and the superlative -ейш(е), a double н and a
  // soft sign: книги is книг, красивыми красив, беднейшие бедн, берёза берез.
  //
  // Snowball's stop words are written with е for ё, as the stemmer reads a word: letters
  // gives the letters a word is read with before its stop word is looked up, so её, ещё
  // and всё are stop words as ее, еще and все are.
  //
  // The stop words and the algorithm are Snowball's (BSD licence, Martin Porter and Richard
  // Boulton); subs/search/_lib/snowball.php is the runtime the steps are written in.

  return [

    'name' => 'Русский',

    'letters' => [ 'ё' => 'е' ],

    'stopwords' => 'и в во не что он на я с со как а то все она так его но да ты к у же вы за бы по только ее
      мне было вот от меня еще нет о из ему теперь когда даже ну вдруг ли если уже или ни быть был
      него до вас нибудь опять уж вам сказал ведь там потом себя ничего ей может они тут где есть
      надо ней для мы тебя их чем была сам чтоб без будто человек чего раз тоже себе под жизнь
      будет ж тогда кто этот говорил того потому этого какой совсем ним здесь этом один почти мой
      тем чтобы нее кажется сейчас были куда зачем сказать всех никогда сегодня можно при наконец
      два об другой хоть после над больше тот через эти нас про всего них какая много разве
      сказала три эту моя впрочем хорошо свою этой перед иногда лучше чуть том нельзя такой им
      более всегда конечно всю между',

    'stem' => function ( $word ) {

      static $v, $gerund, $gerundA, $adjective, $participle, $participleA, $reflexive, $verb, $verbA, $noun, $derivational, $tidy;

      if ( ! $v ) {

        $v            = padSnowballGroup ( 'аеиоуыэюя' );
        $gerundA      = [ 'в', 'вши', 'вшись' ];
        $gerund       = padSnowballSet ( array_merge ( $gerundA, [ 'ив', 'ивши', 'ившись', 'ыв', 'ывши', 'ывшись' ] ) );
        $adjective    = padSnowballSet ( preg_split ( '/\s+/', 'ее ие ые ое ими ыми ей ий ый ой ем им ым ом его ого ему ому их ых
          ую юю ая яя ою ею' ) );
        $participleA  = [ 'ем', 'нн', 'вш', 'ющ', 'щ' ];
        $participle   = padSnowballSet ( array_merge ( $participleA, [ 'ивш', 'ывш', 'ующ' ] ) );
        $reflexive    = padSnowballSet ( [ 'ся', 'сь' ] );
        $verbA        = preg_split ( '/\s+/', 'ла на ете йте ли й л ем н ло но ет ют ны ть ешь нно' );
        $verb         = padSnowballSet ( array_merge ( $verbA, preg_split ( '/\s+/', 'ила ыла ена ейте уйте ите или ыли ей уй ил ыл им
          ым ен ило ыло ено ят ует уют ит ыт ены ить ыть ишь ую ю' ) ) );
        $noun         = padSnowballSet ( preg_split ( '/\s+/', 'а ев ов ие ье е иями ями ами еи ии и ией ей ой ий й иям ям ием ем ам
          ом о у ах иях ях ы ь ию ью ю ия ья я' ) );
        $derivational = padSnowballSet ( [ 'ост', 'ость' ] );
        $tidy         = padSnowballSet ( [ 'ейш', 'ейше', 'н', 'ь' ] );

      }

      // ё read as е; then mark_regions: RV after the first vowel, R2 as in every Snowball
      // stemmer.

      $z = new PadSnowball ( str_replace ( 'ё', 'е', $word ) );

      $pV = $p2 = $z->l;
      $z->c = 0;

      if ( $z->past ( $v ) ) {
        $pV = $z->c;
        if ( $z->pastNon ( $v ) and $z->past ( $v ) and $z->pastNon ( $v ) )
          $p2 = $z->c;
      }

      // The steps work in RV: what lies before it is never read.

      $z->lb = $pV;

      // An ending of a set at the end of the word, the slice set around it, NULL when none -
      // the cursor at the end before, before the ending after.

      $ending = function ( $set ) use ( $z ) {
        $z->c   = $z->l;
        $z->ket = $z->c;
        $s      = $z->amongB ( $set );
        $z->bra = $z->c;
        return $s;
      };

      // An ending of the a-group deleted only after an а or a я.

      $afterA = fn () => ( $z->c > $z->lb and ( $z->at ( $z->c - 1 ) === 'а' or $z->at ( $z->c - 1 ) === 'я' ) );

      // A perfective gerund - -в, -вши, -вшись after а or я, -ив, -ывшись ... - else a
      // reflexive -ся or -сь and then an adjective (and a participle before it), a verb or a
      // noun ending.

      $s    = $ending ( $gerund );
      $done = ( $s !== NULL and ( ! in_array ( $s, $gerundA, TRUE ) or $afterA () ) );

      if ( $done )
        $z->del ();
      else {

        if ( $ending ( $reflexive ) !== NULL )
          $z->del ();

        $end = $z->l;

        // adjectival: an adjective, and a participle before it.

        $z->ket = $z->c = $end;

        if ( $z->amongB ( $adjective ) !== NULL ) {
          $z->bra = $z->c;
          $z->del ();
          $z->ket = $z->c;
          $s      = $z->amongB ( $participle );
          if ( $s !== NULL ) {
            $z->bra = $z->c;
            if ( ! in_array ( $s, $participleA, TRUE ) or $afterA () )
              $z->del ();
          }
        } else {

          // verb, else noun.

          $z->ket = $z->c = $end;
          $s      = $z->amongB ( $verb );

          if ( $s !== NULL and ( ! in_array ( $s, $verbA, TRUE ) or $afterA () ) ) {
            $z->bra = $z->c;
            $z->del ();
          } elseif ( $ending ( $noun ) !== NULL )
            $z->del ();

        }

      }

      // A final и.

      $z->c   = $z->l;
      $z->ket = $z->c;

      if ( $z->eqB ( 'и' ) ) {
        $z->bra = $z->c;
        $z->del ();
      }

      // derivational: -ост and -ость in R2.

      if ( $ending ( $derivational ) !== NULL and $p2 <= $z->c )
        $z->del ();

      // tidy_up: -ейш and -ейше deleted, and the last н of a double н then; the last н of
      // a double н; a soft sign.

      $s = $ending ( $tidy );

      if ( $s === 'н' ) {
        if ( $z->eqB ( 'н' ) )
          $z->del ();
      } elseif ( $s === 'ь' )
        $z->del ();
      elseif ( $s !== NULL ) {
        $z->del ();
        $z->ket = $z->c;
        if ( $z->eqB ( 'н' ) ) {
          $z->bra = $z->c;
          if ( $z->eqB ( 'н' ) )
            $z->del ();
        }
      }

      return $z->word ();

    }

  ];

?>
