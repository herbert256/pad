<?php

  // Timed text - WebVTT (W3C, WebVTT: The Web Video Text Tracks Format, the parser of its
  // section 6) and SubRip (SRT, as the Library of Congress describes it, fdd000569) - the
  // readers and the writers of types/vtt.php and types/srt.php. Both read to the same row, so
  // the cues of one are written as the other and a list of chapters made in PHP as either:
  //
  //   id        the cue's identifier: the line above its timing in WebVTT, the number in SRT
  //   start     when it shows, 00:01:02.500 - hours, minutes, seconds and milliseconds
  //   end       when it goes
  //   startms   start and end as whole milliseconds, to sort and count with
  //   endms
  //   text      the cue's text as written, its lines joined by a newline and its markup kept:
  //             <i>, <b>, <u>, <c.loud>, <v Ann>, <lang nl>, <ruby>, <rt>, a timestamp
  //             <00:01.000> and the character references (&amp;) of WebVTT; <b>, <i>, <u>,
  //             <font> and an {\an8} position of SRT
  //   plain     the text without its markup - the tags and an {\an8} taken out, WebVTT's
  //             character references decoded (SRT has none)
  //   voice     who speaks: the annotation of the cue's first <v Ann>, '' for none
  //   settings  what stands after the end time - align:start line:0 of WebVTT, X1:... of SRT
  //
  // What a reader passes over: a NOTE (a comment), the STYLE and REGION blocks of WebVTT -
  // the look of a file, which a row has no place for; a cue's region:id stays in its
  // settings. A cue that is malformed - a timing that is none, an end before its start - and
  // a block that is no cue are the format's error under the strict check; on the lenient walk
  // they are passed over, as a browser drops such a cue (padFormatFault). A WebVTT text
  // without its WEBVTT line is no WebVTT, and no rows.
  //
  // The writers take a cue's start from start - a timestamp (01:02.5 and 1:02:03,250 too) or
  // a number of seconds, as a media element's currentTime counts - else from startms; its end
  // the same way, else the start of the cue after it, as chapters are written. The cues are
  // written in the order of their starts, as WebVTT has them. The text is text= with the
  // markup of the other format taken out, else plain=, title=, name= or caption= escaped; a
  // blank line of it is left out, as it would end the cue, and no line of it can read as a
  // timing. A row without a start - or without an end and no cue after it, or that ends
  // before it starts - is left out, an error under the strict check (padFormatSkip).

  // The text as lines: the line ends one LF, a byte order mark off, a NUL a U+FFFD - and a
  // text that is no UTF-8 the bytes it holds: as Windows-1252 for SRT, the encoding a legacy
  // SRT file is in, as U+FFFD for each byte that is none for WebVTT, which is UTF-8 always.

  function padCuesLines ( $text, $legacy ) {

    $text = (string) $text;

    if ( ! mb_check_encoding ( $text, 'UTF-8' ) )
      if ( $legacy )
        $text = mb_convert_encoding ( $text, 'UTF-8', 'Windows-1252' );
      else {
        $keep = mb_substitute_character ();
        mb_substitute_character ( 0xFFFD );
        $text = mb_scrub ( $text, 'UTF-8' );
        mb_substitute_character ( $keep );
      }

    if ( str_starts_with ( $text, "\xEF\xBB\xBF" ) )
      $text = substr ( $text, 3 );

    return explode ( "\n", str_replace ( [ "\r\n", "\r", "\0" ], [ "\n", "\n", "\u{FFFD}" ], $text ) );

  }

  function padCuesTime ( $ms, $separator = '.' ) {

    return sprintf ( '%02d:%02d:%02d%s%03d', intdiv ( $ms, 3600000 ), intdiv ( $ms, 60000 ) % 60, intdiv ( $ms, 1000 ) % 60, $separator, $ms % 1000 );

  }

  function padCuesRow ( $id, $start, $end, $text, $settings, $format ) {

    $plain = ( $format == 'vtt' )
           ? html_entity_decode ( preg_replace ( '/<[^>]*>?/', '', $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' )
           : preg_replace ( [ '#</?(?:b|i|u|s|font)\b[^>]*>#i', '/\{\\\\[^}]*\}/' ], '', $text );

    return [
      'id'       => $id,
      'start'    => padCuesTime ( $start ),
      'end'      => padCuesTime ( $end ),
      'startms'  => $start,
      'endms'    => $end,
      'text'     => $text,
      'plain'    => $plain,
      'voice'    => ( $format == 'vtt' and preg_match ( '/<v(?:\.[^\s.>]+)*[ \t\f]+([^>]*)>/', $text, $m ) ) ? trim ( $m [1] ) : '',
      'settings' => $settings
    ];

  }

  // WebVTT, read as its parser reads it: the WEBVTT line, a header up to the first blank line,
  // then blocks apart by blank lines - a cue the block whose first or second line holds -->,
  // the first line then its identifier.

  function padVttRead ( $text ) {

    $lines = padCuesLines ( $text, FALSE );
    $count = count ( $lines );

    if ( ! preg_match ( '/^WEBVTT(?:[ \t]|$)/D', $lines [0] ) )
      padFormatFail ( 1, "a WebVTT file opens with the line WEBVTT, not '" . padMakeSafe ( $lines [0], 30 ) . "'" );

    $rows = [];
    $seen = FALSE;
    $at   = 1;

    if ( $at < $count and $lines [$at] !== '' )
      padVttBlock ( $lines, $at, TRUE, $seen );

    while ( TRUE ) {

      while ( $at < $count and $lines [$at] === '' )
        $at++;

      if ( $at >= $count )
        break;

      if ( $row = padVttBlock ( $lines, $at, FALSE, $seen ) )
        $rows [] = $row;

    }

    return $rows;

  }

  // One block from line $at on, $at left behind it: a cue's row, or NULL for the header, a
  // NOTE, a STYLE or REGION block - and for a cue or a block that is none, once the strict
  // check has had its say.

  function padVttBlock ( $lines, &$at, $header, &$seen ) {

    $count  = count ( $lines );
    $first  = $at;
    $number = 0;
    $buffer = '';
    $arrow  = FALSE;
    $cue    = NULL;
    $look   = '';

    while ( $at < $count ) {

      $line = $lines [ $at++ ];
      $number++;

      if ( str_contains ( $line, '-->' ) ) {

        if ( $header or ! ( $number == 1 or ( $number == 2 and ! $arrow ) ) ) {
          $at--;
          break;
        }

        $arrow  = TRUE;
        $timing = padVttTiming ( $line );

        if ( $timing === NULL )
          padFormatFault ( $at, "'" . padMakeSafe ( $line, 40 ) . "' is no cue timing - 00:01.000 --> 00:04.000, or with hours 00:00:01.000" );
        elseif ( $timing [1] < $timing [0] )
          padFormatFault ( $at, 'the cue ends at ' . padCuesTime ( $timing [1] ) . ', before it starts at ' . padCuesTime ( $timing [0] ) );
        else {
          $cue    = [ $buffer, $timing ];
          $buffer = '';
          $seen   = TRUE;
        }

        continue;

      }

      if ( $line === '' )
        break;

      if ( ! $header and $number == 2 and ! $seen and preg_match ( '/^(STYLE|REGION)[ \t]*$/D', $buffer, $m ) )
        $look = $m [1];

      $buffer .= ( $buffer === '' ? '' : "\n" ) . $line;

    }

    if ( $cue )
      return padCuesRow ( $cue [0], $cue [1] [0], $cue [1] [1], $buffer, $cue [1] [2], 'vtt' );

    if ( ! $header and ! $arrow and $look === '' and ! preg_match ( '/^(NOTE|STYLE|REGION)(?:[ \t\n]|$)/D', $buffer ) )
      padFormatFault ( $first + 1, "a block is a cue, a NOTE, STYLE or REGION - '" . padMakeSafe ( $lines [$first], 30 )
                                   . "' has no timing on its first or second line" );

    return NULL;

  }

  // A timing line: [ start, end, settings ] in milliseconds - or NULL.

  function padVttTiming ( $line ) {

    $at = strspn ( $line, " \t\f" );

    if ( ( $start = padVttTime ( $line, $at ) ) === NULL )
      return NULL;

    $at += strspn ( $line, " \t\f", $at );

    if ( substr ( $line, $at, 3 ) !== '-->' )
      return NULL;

    $at += 3;
    $at += strspn ( $line, " \t\f", $at );

    if ( ( $end = padVttTime ( $line, $at ) ) === NULL )
      return NULL;

    return [ $start, $end, trim ( substr ( $line, $at ) ) ];

  }

  // A WebVTT timestamp at $at: minutes and seconds of two digits, the hours of any length -
  // there when the first number is not two digits or past 59 - and three digits after the
  // dot, as its parser collects them: 01:02.500, 00:01:02.500, 100:00:00.000.

  function padVttTime ( $line, &$at ) {

    if ( ! preg_match ( '/\G(\d+):(\d+)(?::(\d+))?\.(\d+)/', $line, $m, 0, $at ) )
      return NULL;

    $three = ( ( $m [3] ?? '' ) !== '' );

    if ( strlen ( $m [2] ) != 2 or strlen ( $m [4] ) != 3 or ( $three and strlen ( $m [3] ) != 2 ) or strlen ( $m [1] ) > 9 )
      return NULL;

    if ( ! $three and ( strlen ( $m [1] ) != 2 or $m [1] > 59 ) )
      return NULL;

    [ $hours, $minutes, $seconds ] = $three ? [ $m [1], $m [2], $m [3] ] : [ 0, $m [1], $m [2] ];

    if ( $minutes > 59 or $seconds > 59 )
      return NULL;

    $at += strlen ( $m [0] );

    return ( ( (int) $hours * 60 + (int) $minutes ) * 60 + (int) $seconds ) * 1000 + (int) $m [4];

  }

  // SRT: subtitles apart by blank lines, each its number, its timing 00:00:01,000 -->
  // 00:00:04,000 and its lines of text. A blank line inside a text - no number and timing
  // behind it - goes on with the text, as players read it; a subtitle without its number is
  // read for its timing all the same.

  function padSrtRead ( $text ) {

    $lines = padCuesLines ( $text, TRUE );
    $count = count ( $lines );
    $rows  = [];
    $at    = 0;

    while ( TRUE ) {

      while ( $at < $count and trim ( $lines [$at] ) === '' )
        $at++;

      if ( $at >= $count )
        break;

      $first = $at;
      $id    = '';

      if ( preg_match ( '/^[ \t]*(\d+)[ \t]*$/D', $lines [$at], $m ) and $at + 1 < $count and padSrtTiming ( $lines [ $at + 1 ] ) ) {
        $id = $m [1];
        $at++;
      }

      $timing = padSrtTiming ( $lines [$at] );

      if ( $timing === NULL or $timing [1] < $timing [0] ) {
        $bad = ( preg_match ( '/^[ \t]*\d+[ \t]*$/D', $lines [$at] ) and $at + 1 < $count ) ? $at + 1 : $at;
        padFormatFault ( $bad + 1, $timing ? 'the subtitle ends at ' . padCuesTime ( $timing [1], ',' ) . ', before it starts at ' . padCuesTime ( $timing [0], ',' )
                                           : "a subtitle is its number, then its timing 00:00:01,000 --> 00:00:04,000 - '" . padMakeSafe ( $lines [$bad], 40 ) . "' is none" );
        $at = padSrtNext ( $lines, $at + 1 );
        continue;
      }

      $body = [];

      for ( $at++; $at < $count; $at++ ) {
        if ( trim ( $lines [$at] ) === '' ) {
          $next = $at;
          while ( $next < $count and trim ( $lines [$next] ) === '' )
            $next++;
          if ( $next >= $count or padSrtBegins ( $lines, $next ) )
            break;
        }
        $body [] = $lines [$at];
      }

      $rows [] = padCuesRow ( $id, $timing [0], $timing [1], implode ( "\n", $body ), $timing [2], 'srt' );

    }

    return $rows;

  }

  // An SRT timing: [ start, end, settings ] - two digits of minutes and seconds, a comma (a
  // dot is read too) and three of milliseconds - or NULL.

  function padSrtTiming ( $line ) {

    if ( ! preg_match ( '/^[ \t]*(\d{1,9}):(\d\d):(\d\d)[,.](\d{3})[ \t]*-->[ \t]*(\d{1,9}):(\d\d):(\d\d)[,.](\d{3})(?:[ \t]+(.*))?$/D', $line, $m ) )
      return NULL;

    if ( $m [2] > 59 or $m [3] > 59 or $m [6] > 59 or $m [7] > 59 )
      return NULL;

    $ms = fn ( $h, $i, $s, $f ) => ( ( (int) $h * 60 + (int) $i ) * 60 + (int) $s ) * 1000 + (int) $f;

    return [ $ms ( $m [1], $m [2], $m [3], $m [4] ), $ms ( $m [5], $m [6], $m [7], $m [8] ), trim ( $m [9] ?? '' ) ];

  }

  // Where a subtitle starts: a timing, or a number with a timing under it.

  function padSrtStarts ( $lines, $at ) {

    return padSrtTiming ( $lines [$at] ) !== NULL
        or ( preg_match ( '/^[ \t]*\d+[ \t]*$/D', $lines [$at] ) and isset ( $lines [ $at + 1 ] ) and padSrtTiming ( $lines [ $at + 1 ] ) !== NULL );

  }

  // Where a text ends, behind a blank line: a subtitle starting, or one meant to - a number
  // with a time or an arrow under it, a line with an arrow - which is then read as one, and
  // is the format's error when its timing is none, never the text of the subtitle before it.

  function padSrtBegins ( $lines, $at ) {

    return str_contains ( $lines [$at], '-->' )
        or ( preg_match ( '/^[ \t]*\d+[ \t]*$/D', $lines [$at] ) and preg_match ( '/-->|^[ \t]*\d+:\d\d/', $lines [ $at + 1 ] ?? '' ) );

  }

  // Past a subtitle that is none: on to the next one behind a blank line.

  function padSrtNext ( $lines, $at ) {

    $count = count ( $lines );

    while ( $at < $count and ! ( trim ( $lines [ $at - 1 ] ) === '' and padSrtStarts ( $lines, $at ) ) )
      $at++;

    return $at;

  }

  // The cues of rows: [ start, end, row ] in milliseconds, in the order of their starts.

  function padCuesTimed ( $rows ) {

    $cues = [];

    foreach ( $rows as $index => $row ) {

      $start = padCuesWhen ( $row, 'start' );

      if ( $start === NULL ) {
        padFormatSkip ( 'row ' . ( $index + 1 ) . ' has no start - a timestamp 00:01:02.500, a number of seconds or startms' );
        continue;
      }

      $cues [] = [ $start, padCuesWhen ( $row, 'end' ), $row, $index ];

    }

    usort ( $cues, fn ( $a, $b ) => [ $a [0], $a [3] ] <=> [ $b [0], $b [3] ] );

    $out = [];

    foreach ( $cues as $at => [ $start, $end, $row, $index ] ) {

      if ( $end === NULL )
        for ( $next = $at + 1; $next < count ( $cues ) and $end === NULL; $next++ )
          if ( $cues [$next] [0] > $start )
            $end = $cues [$next] [0];

      if ( $end === NULL )
        padFormatSkip ( 'row ' . ( $index + 1 ) . ' has no end, and no cue starts after it' );
      elseif ( $end < $start )
        padFormatSkip ( 'row ' . ( $index + 1 ) . ' ends at ' . padCuesTime ( $end ) . ', before it starts at ' . padCuesTime ( $start ) );
      else
        $out [] = [ $start, $end, $row ];

    }

    return $out;

  }

  // A start or an end in milliseconds: the field as a timestamp - hours optional, a dot or a
  // comma before the fraction - or as a number of seconds, else the field with ms behind its
  // name; NULL for none, a negative time or one past what a timestamp holds.

  function padCuesWhen ( $row, $name ) {

    $value = padFormatPickValue ( $row, [ $name ] );
    $ms    = NULL;

    if ( is_int ( $value ) or is_float ( $value ) or ( is_string ( $value ) and is_numeric ( trim ( $value ) ) ) )
      $ms = (float) $value * 1000;
    elseif ( is_string ( $value ) and preg_match ( '/^(?:(\d{1,9}):)?(\d{1,9}):(\d\d)(?:[.,](\d{1,3}))?$/D', trim ( $value ), $m ) ) {
      if ( $m [3] > 59 or ( $m [1] !== '' and $m [2] > 59 ) )
        return NULL;
      $ms = ( ( (int) $m [1] * 60 + (int) $m [2] ) * 60 + (int) $m [3] ) * 1000 + (int) str_pad ( $m [4] ?? '', 3, '0' );
    }

    if ( $ms === NULL and is_numeric ( $value = padFormatPickValue ( $row, [ $name . 'ms' ] ) ) )
      $ms = (float) $value;

    if ( $ms === NULL or ! is_finite ( $ms ) or $ms < 0 or $ms >= 1e15 )
      return NULL;

    return (int) round ( $ms );

  }

  // The text of a cue as rows give it: text= as the cue text it is, else a plain field.

  function padCuesText ( $row, $format ) {

    $text = padFormatPick ( $row, [ 'text' ] );

    if ( $text !== '' )
      return ( $format == 'vtt' ) ? padVttCueText ( $text ) : padSrtCueText ( $text );

    $plain = padFormatPick ( $row, [ 'plain', 'title', 'name', 'caption' ] );

    return ( $format == 'vtt' ) ? strtr ( $plain, [ '&' => '&amp;', '<' => '&lt;', '>' => '&gt;' ] ) : $plain;

  }

  // Cue text for WebVTT: its own tags kept - c, i, b, u, ruby, rt, v, lang and a timestamp -
  // any other (SRT's <font>) and an {\an8} taken out, a < or > that is no tag and an & that
  // starts no character reference escaped. A --> left inside a tag - <c.x--> - is written
  // --&gt; by padVttWrite: a line that holds one ends the cue in a WebVTT parser.

  function padVttCueText ( $text ) {

    $text = preg_replace ( '/\{\\\\[^}]*\}/', '', $text );

    return preg_replace_callback ( '/<(\/?)([A-Za-z]+)(?:\.[^\s.<>]+)*(?:[ \t\f][^<>]*)?>|<[0-9][0-9:.]*>|&(?:#[0-9]+|#[xX][0-9A-Fa-f]+|[A-Za-z][A-Za-z0-9]*);|[<>&]/',
      function ( $m ) {
        $token = $m [0];
        if ( $token == '<' ) return '&lt;';
        if ( $token == '>' ) return '&gt;';
        if ( $token == '&' ) return '&amp;';
        if ( $token [0] == '&' )
          return ( html_entity_decode ( $token, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) === $token ) ? '&amp;' . substr ( $token, 1 ) : $token;
        if ( ( $m [2] ?? '' ) === '' ) {
          $at = 1;
          return ( padVttTime ( $token, $at ) !== NULL and $at == strlen ( $token ) - 1 ) ? $token : '';
        }
        return in_array ( strtolower ( $m [2] ), [ 'c', 'i', 'b', 'u', 'ruby', 'rt', 'v', 'lang' ], TRUE ) ? $token : '';
      }, $text );

  }

  // Cue text for SRT: WebVTT's own tags - c, v, lang, ruby, rt, a timestamp - taken out, its
  // character references the characters they stand for: SRT has no escapes, and a < in its
  // text stays one.

  function padSrtCueText ( $text ) {

    $text = preg_replace ( [ '#</?(?:c|v|lang|ruby|rt)\b(?:\.[^\s.<>]+)*(?:[ \t\f][^<>]*)?>#i', '/<[0-9][0-9:.]*>/' ], '', $text );

    return html_entity_decode ( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

  }

  // The lines of a cue's text: never a blank one, which would end it.

  function padCuesTextLines ( $text ) {

    $lines = [];

    foreach ( explode ( "\n", str_replace ( [ "\r\n", "\r" ], "\n", $text ) ) as $line )
      if ( trim ( $line ) !== '' )
        $lines [] = rtrim ( $line );

    return $lines;

  }

  // A line of an identifier or a header: one line, without a -->.

  function padCuesOneLine ( $text ) {

    return trim ( str_replace ( '-->', '->', preg_replace ( '/\s+/', ' ', (string) $text ) ) );

  }

  function padVttWrite ( $rows ) {

    $out = "WEBVTT\n";

    foreach ( padCuesTimed ( $rows ) as [ $start, $end, $row ] ) {

      $id       = padCuesOneLine ( padFormatPick ( $row, [ 'id' ] ) );
      $settings = [];

      foreach ( preg_split ( '/[ \t]+/', padFormatPick ( $row, [ 'settings' ] ), -1, PREG_SPLIT_NO_EMPTY ) as $setting )
        if ( preg_match ( '/^(vertical|line|position|size|align|region):[^\s]+$/D', $setting ) and ! str_contains ( $setting, '-->' ) )
          $settings [] = $setting;

      $out .= "\n" . ( $id !== '' ? "$id\n" : '' )
            . padCuesTime ( $start ) . ' --> ' . padCuesTime ( $end ) . ( $settings ? ' ' . implode ( ' ', $settings ) : '' ) . "\n";

      foreach ( padCuesTextLines ( padCuesText ( $row, 'vtt' ) ) as $line )
        $out .= str_replace ( '-->', '--&gt;', $line ) . "\n";

    }

    return $out;

  }

  // SRT as SubRip wrote it: CRLF line ends, the subtitles numbered from 1 in the order of their
  // starts, a comma before the milliseconds; settings only as SRT's own X1:.. X2:.. Y1:.. Y2:...
  // An arrow in a text - -->, -- >, - > - is written ->: a reader as lenient as most takes
  // a number on a line with a timing under it for the next subtitle, blank line or none, and
  // a line of text that holds one would have been a subtitle of its own.

  function padSrtWrite ( $rows ) {

    $out    = [];
    $number = 0;

    foreach ( padCuesTimed ( $rows ) as [ $start, $end, $row ] ) {

      $settings = padFormatPick ( $row, [ 'settings' ] );
      $lines    = [ ++$number, padCuesTime ( $start, ',' ) . ' --> ' . padCuesTime ( $end, ',' )
                             . ( preg_match ( '/^X1:\d+ X2:\d+ Y1:\d+ Y2:\d+$/iD', $settings ) ? " $settings" : '' ) ];

      foreach ( padCuesTextLines ( padCuesText ( $row, 'srt' ) ) as $line )
        $lines [] = preg_replace ( '/-[ \t-]*>/', '->', $line );

      $out [] = implode ( "\r\n", $lines ) . "\r\n";

    }

    return implode ( "\r\n", $out );

  }

?>
