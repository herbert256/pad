<?php

  // Numbers read from text and written as text, as a spreadsheet does.
  //
  //   padFormulaParseValue  a text as a number, or NULL: 42, -1.5, 1,250.75, 1.5E3, 12%,
  //                         $4.50, € 12, (40) for -40, and a date or a time as its serial
  //   padFormulaGeneral     a number as the General format shows it in a column of a
  //                         spreadsheet's standard width: at most 11 characters, written with
  //                         an exponent where that shows more of it - 1/3 is 0.333333333,
  //                         123456789012 is 1.23457E+11
  //   padFormulaFormat      a value through a format code, what TEXT and the formats= of
  //                         {spreadsheet} do - see below

  const padFormulaCurrencies = '$€£¥';

  function padFormulaParseValue ( $text ) {

    $text = trim ( str_replace ( "\u{A0}", ' ', (string) $text ) );

    if ( $text === '' )
      return NULL;

    // A number past what a float holds - 1e400, or 400 digits - is none: Excel keeps such a
    // text a text.

    if ( preg_match ( '/^[+-]?(?:\d+\.?\d*|\.\d+)(?:[eE][+-]?\d+)?$/', $text ) )
      return padFormulaKept ( (float) $text );

    $sign = 1;

    if ( preg_match ( '/^\((.*)\)$/', $text, $match ) ) {
      $sign = -1;
      $text = trim ( $match [1] );
    }

    if ( preg_match ( '/^([+-]?)\s*([' . padFormulaCurrencies . ']?)\s*([+-]?)((?:\d{1,3}(?:,\d{3})+|\d+)(?:\.\d*)?|\.\d+)(?:[eE]([+-]?\d+))?\s*([' . padFormulaCurrencies . ']?)\s*(%?)$/u', $text, $match )
         and ( $match [1] === '' or $match [3] === '' ) and ( $match [2] === '' or $match [6] === '' ) ) {
      $number = (float) str_replace ( ',', '', $match [4] ) * ( ( $match [5] ?? '' ) !== '' ? 10 ** (int) $match [5] : 1 );
      if ( $match [1] == '-' or $match [3] == '-' )
        $number = -$number;
      if ( $match [7] == '%' )
        $number /= 100;
      return padFormulaKept ( $sign * $number );
    }

    if ( $sign == 1 ) {
      $date = padFormulaParseDate ( $text );
      if ( $date !== NULL )
        return (float) $date;
    }

    return NULL;

  }

  function padFormulaGeneral ( $number ) {

    $number = padFormulaRound15 ( (float) $number );

    if ( ! is_finite ( $number ) )
      return '#NUM!';

    if ( $number == 0 )
      return '0';

    $sign  = $number < 0 ? '-' : '';
    $width = 11 - strlen ( $sign );
    $abs   = abs ( $number );

    // The plain form: as many decimals as fit beside the whole part, its zeros taken off -
    // never for 1E+11 and more, which has more digits than fit, so its whole part is counted
    // only below that: a number of 9.22E+18 or more is no int, and the cast ended the request.

    $plain = NULL;

    if ( $abs < 1e11 and ( $whole = strlen ( (string) (int) floor ( $abs ) ) ) <= $width ) {
      $decimals = max ( 0, $width - $whole - 1 );
      $plain    = padFormulaFixed ( $abs, $decimals );
      if ( str_contains ( $plain, '.' ) )
        $plain = rtrim ( rtrim ( $plain, '0' ), '.' );
      if ( strlen ( $plain ) > $width )
        $plain = NULL;
    }

    // The form with an exponent: as many digits as fit before E+nn.

    $exponent = (int) floor ( log10 ( $abs ) );
    $mantissa = $abs / ( 10 ** $exponent );

    if ( $mantissa >= 10 ) {
      $mantissa /= 10;
      $exponent++;
    }

    $tail     = 'E' . ( $exponent < 0 ? '-' : '+' ) . str_pad ( (string) abs ( $exponent ), 2, '0', STR_PAD_LEFT );
    $digits   = max ( 0, $width - strlen ( $tail ) - 2 );
    $front    = padFormulaFixed ( $mantissa, $digits );

    if ( (float) $front >= 10 ) {
      $exponent++;
      $tail  = 'E' . ( $exponent < 0 ? '-' : '+' ) . str_pad ( (string) abs ( $exponent ), 2, '0', STR_PAD_LEFT );
      $front = padFormulaFixed ( $mantissa / 10, $digits );
    }

    if ( str_contains ( $front, '.' ) )
      $front = rtrim ( rtrim ( $front, '0' ), '.' );

    $science = $front . $tail;

    // The plain form unless the other one shows more of the number.

    if ( $plain !== NULL and padFormulaSignificant ( $plain ) >= padFormulaSignificant ( $front ) )
      return $sign . $plain;

    return $sign . $science;

  }

  function padFormulaSignificant ( $text ) {

    return strlen ( rtrim ( ltrim ( str_replace ( '.', '', $text ), '0' ), '0' ) );

  }

  // A number not below 0 with a number of decimals, rounded half away from zero on its 15
  // digits, written without an exponent.

  function padFormulaFixed ( $number, $decimals ) {

    $rounded = padFormulaRoundTo ( $number, $decimals );

    if ( $rounded == 0 )
      return $decimals > 0 ? '0.' . str_repeat ( '0', $decimals ) : '0';

    [ $mantissa, $exponent ] = explode ( 'e', sprintf ( '%.14e', $rounded ) );

    $digits = rtrim ( str_replace ( '.', '', $mantissa ), '0' );
    $text   = padFormulaPlace ( $digits === '' ? '0' : $digits, (int) $exponent );

    if ( $decimals <= 0 )
      return explode ( '.', $text ) [0];

    [ $whole, $fraction ] = explode ( '.', $text . '.' ) + [ 1 => '' ];

    return $whole . '.' . str_pad ( substr ( $fraction, 0, $decimals ), $decimals, '0' );

  }

  // A value through a format code, as TEXT ( value, code ) does and as {spreadsheet}'s
  // formats= shows a column. A code has up to four sections split by ; - positive, negative,
  // zero and text; with two the negative numbers take the second, without their minus. What
  // a section holds:
  //
  //   0 # ?        a digit that always shows, one that shows when it counts, one that shows
  //                as a space - 0.00, #,##0, ?.?
  //   . ,          the decimal point; a comma between digits groups the thousands - #,##0 -
  //                and one after the last digit divides by a thousand - 0.0, is 12.3 for 12345
  //   %            the number times 100, with the sign
  //   E+ E-        an exponent: 0.00E+00
  //   y m d        a date: yyyy yy, mmmm mmm mm m, dddd ddd dd d (an m after an h or before an
  //                s is the minutes); h hh, mm, ss, AM/PM and A/P for a time; [h] the hours
  //                past 24
  //   "text" \c    text as it stands; _x a space as wide as x; *x left out; [Red] [$€-413]
  //                a colour, which is not shown, and a currency, which is
  //   @            the text of a text value
  //   General      the number as the General format writes it
  //
  // Other characters - $ € - + ( ) : / space - stand as they are. Fractions (# ?/?) and the
  // conditions of a section ([>100]) are not read.

  function padFormulaFormat ( $value, $code ) {

    $sections = padFormulaSections ( (string) $code );

    if ( padFormulaIsError ( $value ) )
      return $value ['error'];

    if ( $value === NULL )
      $value = 0.0;

    if ( is_bool ( $value ) )
      return $value ? 'TRUE' : 'FALSE';

    if ( is_string ( $value ) ) {
      if ( isset ( $sections [3] ) )
        return padFormulaFormatText ( $value, $sections [3] );
      if ( count ( $sections ) == 1 and padFormulaHas ( $sections [0], '@' ) )
        return padFormulaFormatText ( $value, $sections [0] );
      return $value;
    }

    $number = padFormulaKept ( (float) $value );

    if ( $number === NULL )
      return '#NUM!';

    if ( count ( $sections ) >= 3 and $number == 0 )
      return padFormulaFormatNumber ( 0.0, $sections [2], FALSE );

    if ( count ( $sections ) >= 2 and $number < 0 )
      return padFormulaFormatNumber ( -$number, $sections [1], FALSE );

    return padFormulaFormatNumber ( $number, $sections [0], TRUE );

  }

  // The sections of a code, split on the ; that stand outside quotes, brackets and escapes.

  function padFormulaSections ( $code ) {

    $sections = [];
    $section  = '';
    $length   = strlen ( $code );

    for ( $at = 0; $at < $length; $at++ ) {

      $char = $code [$at];

      if ( $char == '"' ) {
        $end      = strpos ( $code, '"', $at + 1 );
        $end      = $end === FALSE ? $length : $end;
        $section .= substr ( $code, $at, $end - $at + 1 );
        $at       = $end;
      } elseif ( $char == '\\' or $char == '_' or $char == '*' ) {
        $section .= substr ( $code, $at, 2 );
        $at++;
      } elseif ( $char == '[' ) {
        $end      = strpos ( $code, ']', $at );
        $end      = $end === FALSE ? $length : $end;
        $section .= substr ( $code, $at, $end - $at + 1 );
        $at       = $end;
      } elseif ( $char == ';' ) {
        $sections [] = $section;
        $section     = '';
      } else
        $section .= $char;

    }

    $sections [] = $section;

    return array_slice ( $sections, 0, 4 );

  }

  // The parts of a section: [ kind, text ] - 'lit' text as it shows, 'digit' 0 # or ?,
  // 'point', 'comma', 'percent', 'exp' (E+ or E-), 'at', 'general', and the date and time
  // parts 'y', 'm', 'd', 'h', 's' (text: the letters), 'ampm', 'elapsed' ([h] [m] [s]),
  // 'fraction' (the .00 after seconds).

  function padFormulaFormatParts ( $section ) {

    $parts  = [];
    $length = strlen ( $section );
    $at     = 0;

    while ( $at < $length ) {

      $char = $section [$at];
      $rest = substr ( $section, $at );

      if ( $char == '"' ) {
        $end     = strpos ( $section, '"', $at + 1 );
        $end     = $end === FALSE ? $length : $end;
        $parts[] = [ 'lit', substr ( $section, $at + 1, $end - $at - 1 ) ];
        $at      = $end + 1;
      } elseif ( $char == '\\' ) {
        $parts[] = [ 'lit', mb_substr ( substr ( $section, $at + 1 ), 0, 1 ) ];
        $at     += 1 + strlen ( mb_substr ( substr ( $section, $at + 1 ), 0, 1 ) );
      } elseif ( $char == '_' ) {
        $parts[] = [ 'lit', ' ' ];
        $at     += 1 + strlen ( mb_substr ( substr ( $section, $at + 1 ), 0, 1 ) );
      } elseif ( $char == '*' ) {
        $at     += 1 + strlen ( mb_substr ( substr ( $section, $at + 1 ), 0, 1 ) );
      } elseif ( $char == '[' ) {
        $end  = strpos ( $section, ']', $at );
        $end  = $end === FALSE ? $length : $end;
        $word = substr ( $section, $at + 1, $end - $at - 1 );
        if ( preg_match ( '/^(h+|m+|s+)$/i', $word ) )
          $parts[] = [ 'elapsed', strtolower ( $word [0] ) ];
        elseif ( str_starts_with ( $word, '$' ) )
          $parts[] = [ 'lit', explode ( '-', substr ( $word, 1 ) ) [0] ];
        $at = $end + 1;
      } elseif ( preg_match ( '/^General/i', $rest ) ) {
        $parts[] = [ 'general', '' ];
        $at     += 7;
      } elseif ( preg_match ( '/^(AM\/PM|am\/pm|A\/P|a\/p)/', $rest, $match ) ) {
        $parts[] = [ 'ampm', $match [1] ];
        $at     += strlen ( $match [1] );
      } elseif ( preg_match ( '/^[Ee]([+-])/', $rest, $match ) ) {
        $parts[] = [ 'exp', $match [1] ];
        $at     += 2;
      } elseif ( preg_match ( '/^([yY]+|[mM]+|[dD]+|[hH]+|[sS]+)/', $rest, $match ) ) {
        $parts[] = [ strtolower ( $char ), strtolower ( $match [1] ) ];
        $at     += strlen ( $match [1] );
      } elseif ( $char == '0' or $char == '#' or $char == '?' ) {
        $parts[] = [ 'digit', $char ];
        $at++;
      } elseif ( $char == '.' ) {
        $parts[] = [ 'point', '.' ];
        $at++;
      } elseif ( $char == ',' ) {
        $parts[] = [ 'comma', ',' ];
        $at++;
      } elseif ( $char == '%' ) {
        $parts[] = [ 'percent', '%' ];
        $at++;
      } elseif ( $char == '@' ) {
        $parts[] = [ 'at', '@' ];
        $at++;
      } else {
        $one     = mb_substr ( $rest, 0, 1 );
        $parts[] = [ 'lit', $one ];
        $at     += strlen ( $one );
      }

    }

    return $parts;

  }

  function padFormulaHas ( $section, $kind ) {

    foreach ( padFormulaFormatParts ( $section ) as $part )
      if ( ( $kind == '@' and $part [0] == 'at' ) or $part [0] == $kind )
        return TRUE;

    return FALSE;

  }

  // A text through a section: each @ the text again - a text past 32767 characters is
  // #VALUE!, found as it grows, where a code of many @ wrote a long text that many times.

  function padFormulaFormatText ( $text, $section ) {

    $out   = '';
    $count = 0;
    $size  = mb_strlen ( $text );

    foreach ( padFormulaFormatParts ( $section ) as [ $kind, $part ] ) {
      if ( $kind == 'at' ) {
        $add    = $text;
        $count += $size;
      } else {
        $add    = ( $kind == 'lit' ) ? $part : '';
        $count += mb_strlen ( $add );
      }
      if ( $count > padFormulaMaxText )
        return '#VALUE!';
      $out .= $add;
    }

    return $out;

  }

  // A number through one section; $sign: whether a negative number gets its minus here - the
  // first of two or three sections, or the only one.

  function padFormulaFormatNumber ( $number, $section, $sign ) {

    $parts = padFormulaFormatParts ( $section );

    foreach ( $parts as $part )
      if ( in_array ( $part [0], [ 'y', 'd', 'h', 's', 'ampm', 'elapsed' ], TRUE )
           or ( $part [0] == 'm' and ! padFormulaNumberParts ( $parts ) ) )
        return padFormulaFormatDate ( $number, $parts );

    $minus = ( $sign and $number < 0 ) ? '-' : '';
    $abs   = abs ( $number );

    if ( ! padFormulaNumberParts ( $parts ) ) {
      $out = '';
      foreach ( $parts as [ $kind, $text ] )
        $out .= match ( $kind ) {
          'general' => padFormulaGeneral ( $abs ),
          'lit'     => $text,
          'percent' => '%',
          default   => ''
        };
      return $minus . ( $out === '' ? '' : $out );
    }

    // Where the number's places stand: the digits before the point, those after it, and an
    // exponent's.

    $integer  = [];
    $fraction = [];
    $exponent = [];
    $where    = 'integer';
    $group    = FALSE;
    $scale    = 0;
    $percent  = 0;
    $expSign  = '';
    $count    = count ( $parts );

    foreach ( $parts as $index => [ $kind, $text ] ) {

      if ( $kind == 'point' and $where == 'integer' )
        $where = 'fraction';
      elseif ( $kind == 'exp' ) {
        $where   = 'exponent';
        $expSign = $text;
      } elseif ( $kind == 'digit' ) {
        if     ( $where == 'integer'  ) $integer  [] = $text;
        elseif ( $where == 'fraction' ) $fraction [] = $text;
        else                            $exponent [] = $text;
      } elseif ( $kind == 'percent' )
        $percent++;
      elseif ( $kind == 'comma' and $where != 'exponent' ) {
        $before = ( $integer !== [] or $fraction !== [] );
        $after  = FALSE;
        for ( $next = $index + 1; $next < $count and $parts [$next] [0] != 'exp'; $next++ )
          if ( $parts [$next] [0] == 'digit' ) { $after = TRUE; break; }
        if ( $before and $after and $where == 'integer' )
          $group = TRUE;
        elseif ( $before and ! $after )
          $scale++;
      }

    }

    // A number a code scales past what a float holds - 1E+300 with six % - cannot be written
    // in it: #VALUE!, as TEXT answers for a value its code cannot show.

    if ( $abs != 0 )
      $abs = padFormulaKept ( $abs * ( 100 ** $percent ) / ( 1000 ** $scale ) );

    if ( $abs === NULL )
      return '#VALUE!';

    // The exponent: the number written with as many digits before its point as the code has
    // there - divided by 10^power in steps where that power is past what a float holds,
    // 0.00000000000000000000000000000E+0 for a number near 1E-300.

    $power = 0;

    if ( $expSign !== '' and $abs != 0 ) {
      $before = max ( 1, count ( $integer ) );
      $power  = (int) floor ( log10 ( $abs ) ) - ( $before - 1 );
      $abs    = padFormulaShiftPower ( $abs, $power );
      if ( ! is_finite ( $abs ) )
        return '#VALUE!';
      if ( padFormulaRoundTo ( $abs, count ( $fraction ) ) >= 10 ** $before ) {
        $power++;
        $abs /= 10;
      }
    }

    $fixed = padFormulaFixed ( $abs, count ( $fraction ) );

    [ $whole, $decimals ] = explode ( '.', $fixed . '.' ) + [ 1 => '' ];

    // The whole part: its digits, padded to the places the code fills with 0 or a space, a
    // leading 0 of a number below 1 left out where the code has only #.

    $zeros = 0;
    $first = array_search ( '0', $integer, TRUE );
    $space = array_search ( '?', $integer, TRUE );
    $from  = min ( $first === FALSE ? PHP_INT_MAX : $first, $space === FALSE ? PHP_INT_MAX : $space );

    if ( $from !== PHP_INT_MAX )
      $zeros = count ( $integer ) - $from;

    if ( $whole === '0' )
      $whole = '';

    if ( strlen ( $whole ) < $zeros ) {
      $pad = '';
      for ( $place = count ( $integer ) - $zeros; $place < count ( $integer ) - strlen ( $whole ); $place++ )
        $pad .= $integer [$place] == '?' ? ' ' : '0';
      $whole = $pad . $whole;
    }

    if ( $group and $whole !== '' ) {
      $digits = ltrim ( $whole, ' ' );
      $spaces = strlen ( $whole ) - strlen ( $digits );
      $whole  = str_repeat ( ' ', $spaces ) . strrev ( implode ( ',', str_split ( strrev ( $digits ), 3 ) ) );
    }

    // The decimals: up to the last one that is not 0 as they are, past it what the code has
    // there - 0 a 0, ? a space, # nothing.

    $shown = str_split ( $decimals );
    $last  = -1;

    foreach ( $shown as $place => $digit )
      if ( $digit !== '0' and $digit !== '' )
        $last = $place;

    $tail = [];

    foreach ( $fraction as $place => $kind )
      $tail [] = ( $place <= $last ) ? $shown [$place] : ( $kind == '0' ? '0' : ( $kind == '?' ? ' ' : '' ) );

    // The exponent's digits, at least as many as its places.

    $expText = '';

    if ( $expSign !== '' )
      $expText = 'E' . ( $power < 0 ? '-' : ( $expSign == '+' ? '+' : '' ) )
               . str_pad ( (string) abs ( $power ), max ( 1, count ( $exponent ) ), '0', STR_PAD_LEFT );

    // The parts in their order: the whole part where its first digit stands, the decimals
    // one by one, the rest as they are.

    $out      = '';
    $where    = 'integer';
    $wrote    = FALSE;
    $place    = 0;

    foreach ( $parts as [ $kind, $text ] ) {

      if ( $kind == 'point' and $where == 'integer' ) {
        if ( ! $wrote ) { $out .= $whole; $wrote = TRUE; }
        $out  .= '.';
        $where = 'fraction';
      } elseif ( $kind == 'exp' ) {
        if ( ! $wrote ) { $out .= $whole; $wrote = TRUE; }
        $out  .= $expText;
        $where = 'exponent';
      } elseif ( $kind == 'digit' ) {
        if ( $where == 'integer' and ! $wrote ) { $out .= $whole; $wrote = TRUE; }
        elseif ( $where == 'fraction' ) $out .= $tail [ $place++ ] ?? '';
      } elseif ( $kind == 'lit' )
        $out .= $text;
      elseif ( $kind == 'percent' )
        $out .= '%';
      elseif ( $kind == 'general' )
        $out .= padFormulaGeneral ( $abs );

    }

    if ( ! $wrote )
      $out = $whole . $out;

    return $minus . $out;

  }

  // A number divided by 10^power, in steps of 10^300 where the power is past what a float
  // holds.

  function padFormulaShiftPower ( $number, $power ) {

    while ( $power > 300 ) {
      $number /= 1e300;
      $power  -= 300;
    }

    while ( $power < -300 ) {
      $number *= 1e300;
      $power  += 300;
    }

    return $number / ( 10 ** $power );

  }

  function padFormulaNumberParts ( $parts ) {

    foreach ( $parts as $part )
      if ( $part [0] == 'digit' )
        return TRUE;

    return FALSE;

  }

  // A serial through the date and time parts of a section. The whole time is rounded to the
  // second first, as Excel shows it, and the date, the clock and the hours, minutes and
  // seconds past them ([h] [m] [s]) all come from that one count: 13:45 less 1:45 is
  // 0.49999999999999994 of a day, 12:00 under [h]:mm as under h:mm, where [h] floored the
  // serial unrounded and showed 11:00; a time that rounds to midnight is the next day's
  // 00:00:00, where the clock stopped at 23:59:59 of the day before.

  function padFormulaFormatDate ( $serial, $parts ) {

    if ( $serial < 0 or $serial >= padFormulaLastSerial + 1 )
      return '#VALUE!';

    $total = (int) round ( $serial * 86400 );
    $days  = intdiv ( $total, 86400 );
    $clock = $total % 86400;

    [ $year, $month, $day ] = padFormulaYmd ( $days );
    [ $hour, $minute, $second ] = [ intdiv ( $clock, 3600 ), intdiv ( $clock % 3600, 60 ), $clock % 60 ];

    $twelve = FALSE;

    foreach ( $parts as $part )
      if ( $part [0] == 'ampm' )
        $twelve = TRUE;

    // An m is the minutes after an h or before an s, else the month.

    $kinds = array_column ( $parts, 0 );
    $out   = '';

    foreach ( $parts as $index => [ $kind, $text ] ) {

      if ( $kind == 'm' ) {
        $minutes = FALSE;
        for ( $back = $index - 1; $back >= 0; $back-- )
          if ( in_array ( $kinds [$back], [ 'y', 'm', 'd', 'h', 's', 'elapsed' ], TRUE ) ) {
            $minutes = in_array ( $kinds [$back], [ 'h', 'elapsed' ], TRUE );
            break;
          }
        for ( $next = $index + 1; ! $minutes and $next < count ( $kinds ); $next++ )
          if ( in_array ( $kinds [$next], [ 'y', 'm', 'd', 'h', 's' ], TRUE ) ) {
            $minutes = ( $kinds [$next] == 's' );
            break;
          }
        if ( $minutes ) {
          $out .= strlen ( $text ) >= 2 ? sprintf ( '%02d', $minute ) : $minute;
          continue;
        }
      }

      $out .= match ( $kind ) {
        'y'       => strlen ( $text ) <= 2 ? sprintf ( '%02d', $year % 100 ) : sprintf ( '%04d', $year ),
        'm'       => match ( min ( 5, strlen ( $text ) ) ) {
                       1 => $month, 2 => sprintf ( '%02d', $month ),
                       3 => substr ( padFormulaMonths [ $month - 1 ], 0, 3 ),
                       4 => padFormulaMonths [ $month - 1 ],
                       5 => padFormulaMonths [ $month - 1 ] [0] },
        'd'       => match ( min ( 4, strlen ( $text ) ) ) {
                       1 => $day, 2 => sprintf ( '%02d', $day ),
                       3 => substr ( padFormulaDayNames [ padFormulaWeekday ( $days ) ], 0, 3 ),
                       4 => padFormulaDayNames [ padFormulaWeekday ( $days ) ] },
        'h'       => padFormulaHour ( $hour, $twelve, strlen ( $text ) ),
        's'       => strlen ( $text ) >= 2 ? sprintf ( '%02d', $second ) : $second,
        'elapsed' => match ( $text ) {
                       'h' => intdiv ( $total, 3600 ),
                       'm' => intdiv ( $total, 60 ),
                       's' => $total },
        'ampm'    => padFormulaHalf ( $hour, $text ),
        'lit'     => $text,
        'point'   => '.',
        'comma'   => ',',
        'percent' => '%',
        'digit'   => $text == '0' ? '0' : '',
        default   => ''
      };

    }

    return $out;

  }

  function padFormulaHour ( $hour, $twelve, $width ) {

    if ( $twelve )
      $hour = ( $hour % 12 == 0 ) ? 12 : $hour % 12;

    return $width >= 2 ? sprintf ( '%02d', $hour ) : (string) $hour;

  }

  function padFormulaHalf ( $hour, $text ) {

    $pm = $hour >= 12;

    return match ( $text ) {
      'AM/PM' => $pm ? 'PM' : 'AM',
      'am/pm' => $pm ? 'pm' : 'am',
      'A/P'   => $pm ? 'P' : 'A',
      default => $pm ? 'p' : 'a'
    };

  }

?>
