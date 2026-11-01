<?php

  // GS1 element strings - the data of a logistics label: application identifiers (AIs), each
  // a number of two to four digits that says what follows - (01) a GTIN, (17) an expiry date,
  // (10) a batch - written as GS1 prints them under the bars, the AI in parentheses and its
  // value behind it, or in square brackets where a value holds a parenthesis:
  //
  //   (01)09501101530003(17)250101(10)AB-123
  //   [01]09501101530003[10]LOT(7)
  //
  // padCodeGs1        the elements as [ ai, value ], or the message why the text is none
  // padCodeGs1Tokens  the characters a symbol encodes: the AIs and values, FNC1 (PAD_GS1_FNC1)
  //                   behind every value of no predefined length that is not the last
  // padCodeGs1Text    the text a person reads: (01)09501101530003(17)250101(10)AB-123
  // padCodeGs1128     GS1-128: the bars, or the message why the text is no GS1-128
  //
  // The AIs and the format of their values are those of the GS1 General Specifications
  // (release 24, section 3.2, "GS1 Application Identifiers in numerical order"); the AIs whose
  // element string has a predefined length - no FNC1 behind it - those of its figure 7.8.5-2,
  // by their first two digits. Checked are the digits, the length, the characters of the
  // values - GS1's set of 82 (A-Z a-z 0-9 ! " % & ' ( ) * + , - . / : ; < = > ? _), its set
  // of 39 for a CPID - the check digit of a GTIN, SSCC, GLN and the other keys (weights 3 and
  // 1 from the right) and the dates (YYMMDD, the day 00 for "the end of the month" where GS1
  // allows it). An AI GS1 has not defined, or one of the few this table leaves out, is refused.

  const PAD_GS1_FNC1 = 'FNC1';

  // The element strings of a predefined length, by the first two digits of their AI: the
  // length of the AI and its data together.

  const PAD_GS1_PREDEFINED = [
    '00' => 20, '01' => 16, '02' => 16, '03' => 16, '04' => 18, '11' => 8, '12' => 8, '13' => 8,
    '14' => 8,  '15' => 8,  '16' => 8,  '17' => 8,  '18' => 8,  '19' => 8, '20' => 4, '31' => 10,
    '32' => 10, '33' => 10, '34' => 10, '35' => 10, '36' => 10, '41' => 16 ];

  // GS1's character sets: 82 for most values, 39 for a component/part identifier (8010).

  const PAD_GS1_SET82 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!"%&\'()*+,-./:;<=>?_';
  const PAD_GS1_SET39 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#-/';

  // The AIs: the format of their value as its parts - N digits, X the set of 82, Y the set of
  // 39, a number a fixed length, ..n at most n; c the check digit of a key at the end of the
  // part, d a date YYMMDD, D one whose day may not be 00, t a date and time YYMMDDHHMM; a
  // part in brackets may be left out.

  function padCodeGs1Ais () {

    static $ais = NULL;

    if ( $ais !== NULL )
      return $ais;

    $ais = [
      '00' => 'N18c', '01' => 'N14c', '02' => 'N14c', '03' => 'N14c',
      '10' => 'X..20', '11' => 'N6d', '12' => 'N6d', '13' => 'N6d', '15' => 'N6d', '16' => 'N6d', '17' => 'N6d',
      '20' => 'N2', '21' => 'X..20', '22' => 'X..20', '235' => 'X..28',
      '240' => 'X..30', '241' => 'X..30', '242' => 'N..6', '243' => 'X..20',
      '250' => 'X..30', '251' => 'X..30', '253' => 'N13c [X..17]', '254' => 'X..20', '255' => 'N13c [N..12]',
      '30' => 'N..8', '37' => 'N..8',
      '400' => 'X..30', '401' => 'X..30', '402' => 'N17c', '403' => 'X..30',
      '420' => 'X..20', '421' => 'N3 X..9', '422' => 'N3', '423' => 'N3 [N..12]', '424' => 'N3',
      '425' => 'N3 [N..12]', '426' => 'N3', '427' => 'X..3',
      '7001' => 'N13', '7002' => 'X..30', '7003' => 'N10t', '7004' => 'N..4', '7005' => 'X..12',
      '7006' => 'N6D', '7007' => 'N6D [N6D]', '7008' => 'X..3', '7009' => 'X..10', '7010' => 'X..2',
      '7011' => 'N6D [N4]', '7020' => 'X..20', '7021' => 'X..20', '7022' => 'X..20', '7023' => 'X..30',
      '7240' => 'X..20',
      '8001' => 'N14', '8002' => 'X..20', '8003' => 'N1 N13c [X..16]', '8004' => 'X..30', '8005' => 'N6',
      '8006' => 'N14c N2 N2', '8007' => 'X..34', '8008' => 'N8 [N..4]', '8009' => 'X..50', '8010' => 'Y..30',
      '8011' => 'N..12', '8012' => 'X..20', '8013' => 'X..25', '8017' => 'N18c', '8018' => 'N18c',
      '8019' => 'N..10', '8020' => 'X..25', '8026' => 'N14c N2 N2',
      '8110' => 'X..70', '8111' => 'N4', '8112' => 'X..70', '8200' => 'X..70',
      '90' => 'X..30' ];

    // Trade measures (310n - 369n): six digits, n the place of the decimal point.

    foreach ( array_merge ( range ( 310, 316 ), range ( 320, 337 ), range ( 340, 357 ), range ( 360, 369 ) ) as $ai )
      for ( $n = 0; $n <= 5; $n++ )
        $ais ["$ai$n"] = 'N6';

    // Amounts and prices: 390n and 392n an amount, 391n and 393n an ISO currency first,
    // 394n a percentage off, 395n a price per unit of measure.

    for ( $n = 0; $n <= 9; $n++ ) {
      $ais ["390$n"] = 'N..15';
      $ais ["391$n"] = 'N3 N..15';
      $ais ["392$n"] = 'N..15';
      $ais ["393$n"] = 'N3 N..15';
    }

    for ( $n = 0; $n <= 3; $n++ )
      $ais ["394$n"] = 'N4';

    for ( $n = 0; $n <= 5; $n++ )
      $ais ["395$n"] = 'N6';

    // Location keys (410 - 417): a GLN.

    for ( $ai = 410; $ai <= 417; $ai++ )
      $ais ["$ai"] = 'N13c';

    // Company internal information (91 - 99).

    for ( $ai = 91; $ai <= 99; $ai++ )
      $ais ["$ai"] = 'X..90';

    return $ais;

  }

  // The elements of an element string, or the message why it is none.

  function padCodeGs1 ( $text, $kind = 'gs1128' ) {

    $text = trim ( $text );
    $form = "(01)09501101530003(17)250101(10)AB-123";

    if ( $text === '' or ( $text [0] != '(' and $text [0] != '[' ) )
      return "a $kind code is GS1 element strings, each AI in parentheses before its value - $form";

    $open     = $text [0];
    $close    = $open == '(' ? ')' : ']';
    $ais      = padCodeGs1Ais ();
    $elements = [];
    $at       = 0;
    $length   = strlen ( $text );

    while ( $at < $length ) {

      if ( $text [$at] != $open )
        return "a $kind code is GS1 element strings, each AI in parentheses before its value - $form";

      $end = strpos ( $text, $close, $at );

      if ( $end === FALSE )
        return "the AI at " . padMakeSafe ( substr ( $text, $at, 6 ), 6 ) . " has no closing $close";

      $ai = trim ( substr ( $text, $at + 1, $end - $at - 1 ) );

      if ( ! isset ( $ais [$ai] ) )
        return "($ai) is no GS1 application identifier"
             . ( preg_match ( '/^\d{2,4}$/', $ai ) ? ' PAD knows - the AIs of the GS1 General Specifications, section 3.2' : '' );

      $next  = strpos ( $text, $open, $end + 1 );
      $next  = ( $next === FALSE ) ? $length : $next;
      $value = trim ( substr ( $text, $end + 1, $next - $end - 1 ) );

      $wrong = padCodeGs1Check ( $ai, $value, $ais [$ai] );

      if ( $wrong !== '' )
        return $wrong;

      $elements [] = [ $ai, $value ];
      $at = $next;

    }

    return $elements;

  }

  // Whether the value of an AI is of its format: '' when it is, else the message why not.

  function padCodeGs1Check ( $ai, $value, $format ) {

    $rest = $value;

    foreach ( explode ( ' ', $format ) as $part ) {

      preg_match ( '/^(\[?)([NXY])(\.\.)?(\d+)([cdDt]?)\]?$/', $part, $m );
      list ( , $optional, $set, $upto, $count, $check ) = $m;

      $take  = $upto ? min ( strlen ( $rest ), (int) $count ) : (int) $count;
      $piece = (string) substr ( $rest, 0, $take );
      $rest  = (string) substr ( $rest, $take );

      if ( $piece === '' and $optional )
        continue;

      if ( $piece === '' )
        return "($ai) has no value - it is " . padCodeGs1Format ( $format );

      if ( ! $upto and strlen ( $piece ) < $count )
        return "($ai) is " . padCodeGs1Format ( $format ) . ', not ' . padMakeSafe ( $value, 40 );

      $chars = [ 'N' => '0123456789', 'X' => PAD_GS1_SET82, 'Y' => PAD_GS1_SET39 ] [$set];

      if ( strspn ( $piece, $chars ) != strlen ( $piece ) )
        return ( $set == 'N' )
          ? "($ai) is " . padCodeGs1Format ( $format ) . ', not ' . padMakeSafe ( $value, 40 )
          : "($ai) " . padMakeSafe ( $value, 40 ) . ' has a character GS1 does not take there - '
            . ( $set == 'X' ? "A-Z a-z 0-9 and ! \" % & ' ( ) * + , - . / : ; < = > ? _" : 'A-Z 0-9 and # - /' );

      if ( $check == 'c' ) {
        $digit = padBarcodeCheck ( substr ( $piece, 0, -1 ) );
        if ( substr ( $piece, -1 ) != $digit )
          return "the check digit of ($ai) $piece is $digit, not " . substr ( $piece, -1 );
      }

      if ( $check == 'd' or $check == 'D' or $check == 't' ) {
        $month = (int) substr ( $piece, 2, 2 );
        $day   = (int) substr ( $piece, 4, 2 );
        $days  = ( $month >= 1 and $month <= 12 ) ? padCodeGs1Days ( (int) substr ( $piece, 0, 2 ), $month ) : 0;
        $hour  = $check == 't' ? (int) substr ( $piece, 6, 2 ) : 0;
        $min   = $check == 't' ? (int) substr ( $piece, 8, 2 ) : 0;
        if ( ! $days or $day > $days or ( $day == 0 and $check != 'd' ) or $hour > 23 or $min > 59 )
          return "($ai) $piece is no date YYMMDD" . ( $check == 't' ? 'HHMM' : '' )
               . ( $check == 'd' ? ' - the day 00 is the end of the month' : '' );
      }

    }

    if ( $rest !== '' )
      return "($ai) is " . padCodeGs1Format ( $format ) . ', not ' . padMakeSafe ( $value, 40 );

    return '';

  }

  // The days of a month of a two-digit year - GS1 reads YY within a sliding window around
  // the present (section 7.12); a leap year is one either way, as 00 and the years of this
  // century divisible by four are.

  function padCodeGs1Days ( $year, $month ) {

    return $month == 2 ? ( $year % 4 ? 28 : 29 ) : [ 31, 0, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ] [$month - 1];

  }

  // A format for a message: N14 as "14 digits", X..20 as "up to 20 characters".

  function padCodeGs1Format ( $format ) {

    $words = [];

    foreach ( explode ( ' ', $format ) as $part ) {
      preg_match ( '/^(\[?)([NXY])(\.\.)?(\d+)([cdDt]?)\]?$/', $part, $m );
      $what     = ( $m [2] == 'N' ? 'digit' : 'character' ) . ( $m [4] == 1 ? '' : 's' );
      $words [] = ( $m [1] ? 'perhaps ' : '' ) . ( $m [3] ? "up to {$m[4]} $what" : "{$m[4]} $what" )
                . [ '' => '', 'c' => ' ending in a check digit', 'd' => ', a date YYMMDD', 'D' => ', a date YYMMDD', 't' => ', a date and time YYMMDDHHMM' ] [$m [5]];
    }

    return implode ( ' and then ', $words );

  }

  // The characters a symbol encodes, FNC1 as PAD_GS1_FNC1: behind every element whose length
  // is not predefined by its first two digits, unless it is the last one.

  function padCodeGs1Tokens ( $elements ) {

    $tokens = [];
    $last   = count ( $elements ) - 1;

    foreach ( $elements as $k => list ( $ai, $value ) ) {
      foreach ( str_split ( $ai . $value ) as $char )
        $tokens [] = $char;
      if ( $k < $last and ! isset ( PAD_GS1_PREDEFINED [ substr ( $ai, 0, 2 ) ] ) )
        $tokens [] = PAD_GS1_FNC1;
    }

    return $tokens;

  }

  function padCodeGs1Text ( $elements ) {

    return implode ( '', array_map ( fn ( $e ) => "({$e[0]}){$e[1]}", $elements ) );

  }

  // GS1-128: Code 128 that starts with FNC1, the separators FNC1 as well. GS1 limits it to 48
  // characters - the AIs, their values and the separators - the start, the first FNC1, the
  // check symbol and the stop not counted.

  function padCodeGs1128 ( $text ) {

    $elements = padCodeGs1 ( $text );

    if ( ! is_array ( $elements ) )
      return $elements;

    $tokens = padCodeGs1Tokens ( $elements );

    if ( count ( $tokens ) > 48 )
      return 'a gs1128 barcode holds at most 48 characters, the separators counted - this one has ' . count ( $tokens );

    $bars = padCode128Symbols ( array_merge ( [ PAD_GS1_FNC1 ], $tokens ) );

    return [ $bars, [ [ strlen ( $bars ) / 2, padCodeGs1Text ( $elements ) ] ], [], 10 ];

  }

  // The bars of Code 128 symbols for the characters given - printable ASCII and FNC1 - in the
  // fewest symbols: sets B and C only, as GS1's characters are all in B, chosen by the least
  // cost from each position to the end, a tie staying in the set it is in and starting in C.

  function padCode128Symbols ( $tokens ) {

    $n     = count ( $tokens );
    $digit = fn ( $i ) => $i < $n and $tokens [$i] !== PAD_GS1_FNC1 and ctype_digit ( $tokens [$i] );
    $cost  = [ $n => [ 'B' => 0, 'C' => 0 ] ];
    $step  = [];

    for ( $i = $n - 1; $i >= 0; $i-- ) {

      // Staying in a set: B takes every character, C a pair of digits or FNC1.

      $stay = [ 'B' => 1 + $cost [$i + 1] ['B'], 'C' => INF ];

      if ( $tokens [$i] === PAD_GS1_FNC1 )
        $stay ['C'] = 1 + $cost [$i + 1] ['C'];
      elseif ( $digit ( $i ) and $digit ( $i + 1 ) )
        $stay ['C'] = 1 + $cost [$i + 2] ['C'];

      foreach ( [ 'B' => 'C', 'C' => 'B' ] as $set => $other ) {
        $switch = 1 + $stay [$other];
        $cost [$i] [$set] = min ( $stay [$set], $switch );
        $step [$i] [$set] = ( $stay [$set] <= $switch ) ? $set : $other;
      }

    }

    $set    = ( $cost [0] ['C'] <= $cost [0] ['B'] ) ? 'C' : 'B';
    $values = [ $set == 'C' ? 105 : 104 ];
    $i      = 0;

    while ( $i < $n ) {

      if ( $step [$i] [$set] != $set ) {
        $set       = $step [$i] [$set];
        $values [] = $set == 'C' ? 99 : 100;
      }

      if ( $tokens [$i] === PAD_GS1_FNC1 ) {
        $values [] = 102;
        $i++;
      } elseif ( $set == 'C' ) {
        $values [] = (int) ( $tokens [$i] . $tokens [$i + 1] );
        $i += 2;
      } else {
        $values [] = ord ( $tokens [$i] ) - 32;
        $i++;
      }

    }

    $sum = $values [0];

    foreach ( $values as $k => $value )
      if ( $k )
        $sum += $k * $value;

    $values [] = $sum % 103;
    $values [] = 106;

    $bars = '';

    foreach ( $values as $value )
      foreach ( str_split ( PAD_CODE128 [$value] ) as $k => $w )
        $bars .= str_repeat ( $k % 2 ? '0' : '1', (int) $w );

    return $bars;

  }

?>
