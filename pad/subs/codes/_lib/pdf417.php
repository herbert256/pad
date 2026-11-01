<?php

  // PDF417 (ISO/IEC 15438), the stacked code of boarding passes, driving licences and shipping
  // documents: rows of codewords of 17 modules - four bars and four spaces - between a start
  // and a stop pattern, a row indicator at each end of a row that says how many rows, columns
  // and error correction the symbol has, so a scanner can read it row by row in any order.
  //
  //   {pdf417 'M1DOE/JOHN            EABC123 AMSLHRKL 0641 307Y012A0042 100'/}
  //   {barcode:pdf417 $document, columns=6, level=5/}
  //
  // padPdf417          the SVG, or '' when the text does not fit in a symbol of 928 codewords
  // padPdf417Matrix    the rows of the symbol, TRUE a dark module, each row one module high
  // padPdf417Words     the data codewords of a text: text, byte and numeric compaction
  //
  // The text is cut as the standard suggests: a run of 13 digits or more in numeric compaction
  // (902, 44 digits to 15 codewords), a run of 5 characters or more of printable ASCII, tab,
  // CR and LF in text compaction - its four submodes, alpha, lower, mixed and punctuation,
  // latched and shifted two to a codeword - and the rest in byte compaction (901, or 924 for
  // a multiple of six bytes, six bytes to five codewords), a single byte between two pieces
  // of text shifted in (913). A text of UTF-8 beyond ASCII starts with the ECI designator of
  // UTF-8, 927 26 - without it a reader takes the bytes as code page 437. The error correction
  // is Reed-Solomon over GF(929) with the generator of the roots 3^1 to 3^k, k = 2^(level+1):
  // level 0 to 8, by default the standard's recommendation for the data's size - 2 up to 40
  // data codewords, 3 up to 160, 4 up to 320, 5 beyond. The columns (1 to 30) make the symbol
  // about three times as wide as high unless columns= says how many; rows 3 to 90, each three
  // modules high, a quiet zone of two modules on either side.
  //
  // The codeword patterns - the three clusters of 929 bar and space patterns of the standard's
  // table, cluster 0, 3 and 6 - are taken from pdf417gen 0.8.1 (https://github.com/ihabunek/pdf417-py,
  // pdf417gen/codes.py), under its MIT licence:
  //
  //   Copyright (c) 2016 Ivan Habunek <ivan.habunek@gmail.com>
  //
  //   Permission is hereby granted, free of charge, to any person obtaining a copy of this
  //   software and associated documentation files (the "Software"), to deal in the Software
  //   without restriction, including without limitation the rights to use, copy, modify,
  //   merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
  //   persons to whom the Software is furnished to do so, subject to the following conditions:
  //
  //   The above copyright notice and this permission notice shall be included in all copies or
  //   substantial portions of the Software.
  //
  //   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
  //   INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
  //   PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
  //   FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
  //   OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
  //   DEALINGS IN THE SOFTWARE.
  //
  // Written here as four hex digits a codeword, the 17 modules less the first, which is always
  // a bar; every pattern was checked to be 17 modules of four bars and four spaces of one to six,
  // of the cluster its table stands for, and distinct.

  // The text submodes: the value of a character in each, -1 none. Alpha and lower hold the
  // letters and space, mixed the digits and & CR HT , : # - . $ / + % * = ^ and space,
  // punctuation ; < > @ [ \ ] _ ` ~ ! CR HT , : LF - . $ / " | * ( ) ? { } '.

  const PAD_PDF417_MIXED = "0123456789&\r\t,:#-.$/+%*=^";
  const PAD_PDF417_PUNCT = ";<>@[\\]_`~!\r\t,:\n-.$/\"|*()?{}'";

  function padPdf417 ( $text, $scale = 2, $title = '', $dark = '#000', $light = '#fff', $level = NULL, $columns = NULL ) {

    $rows = padPdf417Matrix ( (string) $text, $level, $columns );

    if ( ! $rows )
      return '';

    $quiet  = 2;
    $high   = 3;
    $wide   = count ( $rows [0] ) + 2 * $quiet;
    $tall   = count ( $rows ) * $high + 2 * $quiet;
    $path   = '';

    foreach ( $rows as $y => $row )
      for ( $x = 0; $x < count ( $row ); $x++ )
        if ( $row [$x] ) {
          $run = 1;
          while ( $x + $run < count ( $row ) and $row [$x + $run] )
            $run++;
          $path .= 'M' . ( $x + $quiet ) . ',' . ( $y * $high + $quiet ) . "h{$run}v{$high}h-{$run}z";
          $x += $run;
        }

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-pdf417" role="img"'
         . ' width="' . padBarcodeNum ( $wide * $scale ) . '" height="' . padBarcodeNum ( $tall * $scale ) . '"'
         . " viewBox=\"0 0 $wide $tall\" shape-rendering=\"crispEdges\">"
         . '<title>' . padQrAttr ( ( $title !== '' ) ? $title : (string) $text ) . '</title>'
         . '<rect width="100%" height="100%" fill="' . padQrAttr ( $light ) . '"/>'
         . '<path fill="' . padQrAttr ( $dark ) . "\" d=\"$path\"/></svg>";

  }

  // The symbol: its rows of modules, or NULL when the text needs more than 928 codewords, or
  // more rows than 90 in the columns asked.

  function padPdf417Matrix ( $text, $level = NULL, $columns = NULL ) {

    $data  = padPdf417Words ( $text );
    $count = count ( $data ) + 1;

    if ( $level === NULL )
      $level = $count <= 40 ? 2 : ( $count <= 160 ? 3 : ( $count <= 320 ? 4 : 5 ) );

    $ecc = 2 << $level;

    if ( $count + $ecc > 928 )
      return NULL;

    // The columns: those asked, else the ones that make the symbol closest to three times as
    // wide as it is high - 17 modules a column and 69 for the patterns and indicators, three
    // modules a row.

    $best = NULL;

    foreach ( $columns ? [ $columns ] : range ( 1, 30 ) as $cols ) {
      $rows = max ( 3, (int) ceil ( ( $count + $ecc ) / $cols ) );
      if ( $rows > 90 or $rows * $cols > 928 )
        continue;
      $ratio = ( 17 * $cols + 69 ) / ( 3 * $rows );
      if ( $best === NULL or abs ( $ratio - 3 ) < abs ( $best [2] - 3 ) )
        $best = [ $cols, $rows, $ratio ];
    }

    if ( $best === NULL )
      return NULL;

    list ( $cols, $rows ) = $best;

    // The length descriptor first - the data codewords with itself and the padding - then the
    // data, padded with 900, then the error correction.

    $length = $rows * $cols - $ecc;

    $words = array_merge ( [ $length ], $data, array_fill ( 0, $length - $count, 900 ) );
    $words = array_merge ( $words, padPdf417Check ( $words, $ecc ) );

    // The rows: start, the left row indicator, the codewords of the row in the cluster of its
    // row - 0, 3, 6 in turn - the right row indicator, stop.

    $codes  = padPdf417Codes ();
    $matrix = [];

    for ( $y = 0; $y < $rows; $y++ ) {

      $cluster = $y % 3;
      $base    = 30 * intdiv ( $y, 3 );
      $tell    = [ $base + intdiv ( $rows - 1, 3 ), $base + $level * 3 + ( $rows - 1 ) % 3, $base + $cols - 1 ];
      $left    = $tell [$cluster];
      $right   = $tell [ ( $cluster + 2 ) % 3 ];

      $bits = '11111111010101000';

      foreach ( array_merge ( [ $left ], array_slice ( $words, $y * $cols, $cols ), [ $right ] ) as $word )
        $bits .= '1' . str_pad ( base_convert ( substr ( $codes [$cluster], 4 * $word, 4 ), 16, 2 ), 16, '0', STR_PAD_LEFT );

      $bits .= '111111101000101001';

      $matrix [] = array_map ( fn ( $bit ) => $bit === '1', str_split ( $bits ) );

    }

    return $matrix;

  }

  // The data codewords of a text, the length descriptor not yet in front: the compactions as
  // the standard suggests choosing them, starting in text compaction's alpha submode.

  function padPdf417Words ( $text ) {

    $words  = [];
    $length = strlen ( $text );
    $mode   = 'text';
    $sub    = 'alpha';
    $isText = fn ( $c ) => ( $c >= 32 and $c <= 126 ) or $c == 9 or $c == 10 or $c == 13;
    $digits = fn ( $at ) => strspn ( $text, '0123456789', $at );

    if ( preg_match ( '/[\x80-\xff]/', $text ) and preg_match ( '//u', $text ) )
      array_push ( $words, 927, 26 );

    // A run of text: printable characters up to a run of 13 digits.

    $textRun = function ( $at ) use ( $text, $length, $isText, $digits ) {
      $i = $at;
      while ( $i < $length ) {
        $run = $digits ( $i );
        if ( $run >= 13 )
          break;
        if ( $run ) {
          $i += $run;
          continue;
        }
        if ( ! $isText ( ord ( $text [$i] ) ) )
          break;
        $i++;
      }
      return $i - $at;
    };

    for ( $i = 0; $i < $length; ) {

      $run = $digits ( $i );

      if ( $run >= 13 ) {
        $words [] = 902;
        array_push ( $words, ...padPdf417Numeric ( substr ( $text, $i, $run ) ) );
        $mode = 'numeric';
        $sub  = 'alpha';
        $i   += $run;
        continue;
      }

      $chars = $textRun ( $i );

      if ( $chars >= 5 or $chars == $length - $i ) {
        if ( $mode != 'text' ) {
          $words [] = 900;
          $mode     = 'text';
          $sub      = 'alpha';
        }
        $sub = padPdf417Text ( substr ( $text, $i, $chars ), $sub, $words );
        $i  += $chars;
        continue;
      }

      // Bytes: up to where a run of 13 digits or of 5 characters of text starts.

      $end = $i + 1;
      while ( $end < $length and $digits ( $end ) < 13 and $textRun ( $end ) < 5 )
        $end++;

      $bytes = substr ( $text, $i, $end - $i );

      if ( strlen ( $bytes ) == 1 and $mode == 'text' )
        array_push ( $words, 913, ord ( $bytes ) );
      else {
        $words [] = strlen ( $bytes ) % 6 ? 901 : 924;
        array_push ( $words, ...padPdf417Bytes ( $bytes ) );
        $mode = 'byte';
        $sub  = 'alpha';
      }

      $i = $end;

    }

    return $words;

  }

  // Text compaction: the values of the characters in their submode, a latch or a shift where
  // the submode changes, two values to a codeword - 30 times the first and the second - the
  // last filled with the punctuation shift. Answers the submode it ends in.

  function padPdf417Text ( $text, $sub, &$words ) {

    $values = [];
    $length = strlen ( $text );
    $upper  = fn ( $c ) => $c === ' ' or ctype_upper ( $c );
    $lower  = fn ( $c ) => $c === ' ' or ctype_lower ( $c );
    $mixed  = fn ( $c ) => $c === ' ' or strpos ( PAD_PDF417_MIXED, $c ) !== FALSE;
    $punct  = fn ( $c ) => strpos ( PAD_PDF417_PUNCT, $c ) !== FALSE;
    $value  = fn ( $c, $set ) => $c === ' ' ? 26 : strpos ( $set, $c );

    for ( $i = 0; $i < $length; ) {

      $c = $text [$i];

      switch ( $sub ) {

        case 'alpha':
          if ( $upper ( $c ) )      { $values [] = $c === ' ' ? 26 : ord ( $c ) - 65; $i++; }
          elseif ( $lower ( $c ) )  { $values [] = 27; $sub = 'lower'; }
          elseif ( $mixed ( $c ) )  { $values [] = 28; $sub = 'mixed'; }
          else                      { array_push ( $values, 29, strpos ( PAD_PDF417_PUNCT, $c ) ); $i++; }
          break;

        case 'lower':
          if ( $lower ( $c ) )      { $values [] = $c === ' ' ? 26 : ord ( $c ) - 97; $i++; }
          elseif ( $upper ( $c ) )  { array_push ( $values, 27, ord ( $c ) - 65 ); $i++; }
          elseif ( $mixed ( $c ) )  { $values [] = 28; $sub = 'mixed'; }
          else                      { array_push ( $values, 29, strpos ( PAD_PDF417_PUNCT, $c ) ); $i++; }
          break;

        case 'mixed':
          if ( $mixed ( $c ) )      { $values [] = $value ( $c, PAD_PDF417_MIXED ); $i++; }
          elseif ( $upper ( $c ) )  { $values [] = 28; $sub = 'alpha'; }
          elseif ( $lower ( $c ) )  { $values [] = 27; $sub = 'lower'; }
          elseif ( $i + 1 < $length and $punct ( $text [$i + 1] ) )
                                    { $values [] = 25; $sub = 'punct'; }
          else                      { array_push ( $values, 29, strpos ( PAD_PDF417_PUNCT, $c ) ); $i++; }
          break;

        default:
          if ( $punct ( $c ) )      { $values [] = strpos ( PAD_PDF417_PUNCT, $c ); $i++; }
          else                      { $values [] = 29; $sub = 'alpha'; }

      }

    }

    if ( count ( $values ) % 2 )
      $values [] = 29;

    for ( $k = 0; $k < count ( $values ); $k += 2 )
      $words [] = 30 * $values [$k] + $values [$k + 1];

    return $sub;

  }

  // Byte compaction: six bytes - a number of 48 bits - as five codewords of base 900, the
  // bytes left over one codeword each.

  function padPdf417Bytes ( $bytes ) {

    $words = [];
    $full  = strlen ( $bytes ) - strlen ( $bytes ) % 6;

    for ( $i = 0; $i < $full; $i += 6 ) {
      $number = 0;
      for ( $k = 0; $k < 6; $k++ )
        $number = $number * 256 + ord ( $bytes [$i + $k] );
      $five = [];
      for ( $k = 0; $k < 5; $k++ ) {
        array_unshift ( $five, $number % 900 );
        $number = intdiv ( $number, 900 );
      }
      array_push ( $words, ...$five );
    }

    for ( $i = $full; $i < strlen ( $bytes ); $i++ )
      $words [] = ord ( $bytes [$i] );

    return $words;

  }

  // Numeric compaction: 44 digits at a time, a 1 put in front, as a number of base 900 - the
  // digits are divided out as text, as the number has up to 45 of them.

  function padPdf417Numeric ( $digits ) {

    $words = [];

    foreach ( str_split ( $digits, 44 ) as $group ) {
      $number = '1' . $group;
      $base   = [];
      while ( $number !== '0' ) {
        $quotient = '';
        $carry    = 0;
        foreach ( str_split ( $number ) as $digit ) {
          $carry     = $carry * 10 + (int) $digit;
          $quotient .= intdiv ( $carry, 900 );
          $carry    %= 900;
        }
        array_unshift ( $base, $carry );
        $number = ltrim ( $quotient, '0' );
        $number = ( $number === '' ) ? '0' : $number;
      }
      array_push ( $words, ...$base );
    }

    return $words;

  }

  // The error correction codewords: the remainder of the data times x^k divided by the
  // product of (x - 3^i), i 1 to k, over the integers modulo 929, negated - as the standard's
  // annex computes them.

  function padPdf417Check ( $data, $count ) {

    $generator = [ 1 ];
    $power     = 1;

    for ( $i = 1; $i <= $count; $i++ ) {
      $power = $power * 3 % 929;
      $next  = array_fill ( 0, count ( $generator ) + 1, 0 );
      foreach ( $generator as $k => $coefficient ) {
        $next [$k]     = ( $next [$k] + $coefficient ) % 929;
        $next [$k + 1] = ( $next [$k + 1] + 929 - $coefficient * $power % 929 ) % 929;
      }
      $generator = $next;
    }

    // The generator highest power first: $generator [0] is 1, $generator [$count] the constant.

    $e = array_fill ( 0, $count, 0 );

    foreach ( $data as $word ) {
      $t = ( $word + $e [$count - 1] ) % 929;
      for ( $j = $count - 1; $j >= 1; $j-- )
        $e [$j] = ( $e [$j - 1] + 929 - $t * $generator [$count - $j] % 929 ) % 929;
      $e [0] = ( 929 - $t * $generator [$count] % 929 ) % 929;
    }

    $check = [];

    for ( $j = $count - 1; $j >= 0; $j-- )
      $check [] = $e [$j] ? 929 - $e [$j] : 0;

    return $check;

  }

  // The codeword patterns of clusters 0, 3 and 6: four hex digits a codeword, the modules after
  // the first bar.

  function padPdf417Codes () {

    return PAD_PDF417_CODES;

  }

  const PAD_PDF417_CODES = [
    // cluster 0
    'd5c0eaf0f57cd4e0ea78f53ea8c0d470a8605040a8305020adc0d6f0eb7cace0d678eb3e58c0ac7058605dc0aef0d77c'
    . '5ce0ae78d73e5c70ae3c5ef0af7c5e78af3e5f7cf5fad2e0e978f4bea4c0d270e93ca460d2384840a430d21c4820a418'
    . '4810a6e0d378e9be4cc0a670d33c4c60a638d31e4c30a61c4ee0a778d3be4e70a73c4e38a71e4f78a7be4f3c4f1ea2c0'
    . 'd170e8bca260d138e89e4440a230d11c4420a2184410440846c0a370d1bc4660a338d19e4630a31c4618460c4770a3bc'
    . '4738a39e471c47bca160d0b8e85e4240a130d09c4220a118d08e4210a10c4208a1064360a1b8d0de4330a19c4318a18e'
    . '430c4306a1de438e4140a0b0d05c4120a098d04e4110a08c4108a086410441b04198418c40a0d02ea04ca0464082cae0'
    . 'e578f2be94c0ca70e53c9460ca38e51e28409430282096e0cb78e5be2cc09670cb3c2c6096382c302c182ee09778cbbe'
    . '2e70973c2e382e1c2f7897be2f3c2fbedac0ed70f6bcda60ed38f69eb440da30ed1cb420da18ed0eb410da0c92c0c970'
    . 'e4bcb6c09260c938e49eb660db38ed9e6c4024209218c90e6c20b6186c1026c09370c9bc6ec026609338c99e6e60b738'
    . 'db9e6e3026186e18277093bc6f702738939e6f38b79e6f1c27bc6fbc279e6f9ed960ecb8f65eb240d930ec9cb220d918'
    . 'ec8eb210d90cb208b2049160c8b8e45eb3609130c89c66402220d99cc88e66202210910c6610b30c91062204236091b8'
    . 'c8de67602330919c6730b39c918e6718230c230623b891de67b8239c679c238e678e67deb140d8b0ec5cb120d898ec4e'
    . 'b110d88cb108d886b104b102214090b0c85c634021209098c84e6320b198d8ce6310210890866308b186630421b090dc'
    . '63b0219890ce6398b1ce638c2186638663dc63ceb0a0d858ec2eb090d84cb088d846b084b08220a09058c82e61a02090'
    . '904c6190b0cc9046618820846184208220d861d861cc61c6d82cd826b042902c204860c860c460c28ac0c570e2bc8a60'
    . 'c53814408a30c51c14208a181410140816c08b70c5bc16608b38c59e16308b1c1618160c17708bbc17388b9e171c17bc'
    . '179ecd60e6b8f35e9a40cd30e69c9a20cd18e68e9a10cd0c9a08cd068960c4b8e25e9b608930c49c36401220cd9cc48e'
    . '36209b18890c361012083608136089b8c4de37601330cdde37309b9c898e3718130c370c13b889de37b8139c379c138e'
    . '13de37dedd40eeb0f75cdd20ee98f74edd10ee8cdd08ee86dd049940ccb0e65cbb409920eedce64ebb20dd98eecebb10'
    . '9908cc86bb08dd869902114088b0c45c334011208898c44e774033209998ccce7720bb98ddce88867710330899867708'
    . '110211b088dc33b0119888ce77b0339899ce7798bbce1186338611dc33dc11ce77dc33cedca0ee58f72edc90ee4cdc88'
    . 'ee46dc84dc8298a0cc58e62eb9a09890ee6eb990dccccc46b9889884b9849882b98210a08858c42e31a01090884c73a0'
    . '319098cc88467390b9cc1084738831841082318210d8886e31d810cc73d831cc10c673cc31c610ee73eedc50ee2cdc48'
    . 'ee26dc44dc429850cc2cb8d09848cc26b8c8dc66b8c49842b8c21050882c30d01048882671d030c8986671c8b8e61042'
    . '71c430c271c230ec71ec71e6ee16dc22cc16982498221028306870e81022306285600a4085300a208518c28e0a10850c'
    . '0a0885060b6085b8c2de0b30859c0b18858e0b0c0b060bb885de0b9c0b8e0bde8d40c6b0e35c8d20c6988d10c68c8d08'
    . 'c6868d04094084b0c25c1b400920c6dcc24e1b208d98c6ce1b10090884861b088d86090209b084dc1bb0099884ce1b98'
    . '8dce1b8c098609dc1bdc09ce1bcecea0e758f3aece90e74cce88e746ce84ce828ca0c6589da08c90c64c9d90ceccc646'
    . '9d888c849d848c829d8208a0845819a00890c66e3ba019908ccc84463b909dcc08843b8819840882198208d8846e19d8'
    . '08cc3bd819cc08c63bcc19c608ee19ee3beeef50f7acef48f7a6ef44ef42ce50e72cded0ef6ce726dec8ef66dec4ce42'
    . 'dec28c50c62c9cd08c48c626bdd09cc8ce66bdc8dee68c42bdc49cc2bdc20850842c18d00848842639d018c88c667bd0'
    . '39c89ce608427bc8bde618c27bc4086c18ec086639ec18e67bec39e67be6ef28f796ef24ef22ce28e716de68ef36de64'
    . 'ce22de628c28c6169c688c24bce89c648c22bce49c62bce20828841618688c3638e81864082279e838e4186279e438e2'
    . '79e2187679f6ef12de34de329c34bc74bc721834387478f478f205400520829805100508050405b00598058c058605dc'
    . '05ce86a08690c34c8688c3468684868204a082580da086d8824c0d9086cc0d8886c60d8404820d8204d8826e0dd886ee'
    . '0dcc04c60dc604ee0deec750c748c744c74286508ed0c76cc3268ec8c7668ec486428ec204500cd0044882261dd00cc8'
    . '04441dc80cc404421dc40cc2046c0cec04661dec0ce61de6e7a8e7a4e7a2c728cf68e7b6cf64c722cf628628c3168e68'
    . 'c7369ee88e6486229ee48e629ee2042882160c6886361ce80c6404223de81ce40c623de41ce204360c761cf63df6f7d4'
    . 'f7d2e794efb4e792efb2c714cf34c712df74cf32df7286148e3486129e748e32bef4',
    // cluster 3
    'f560fab8ea40f530fa9cea20f518fa8eea10f50cea08f506ea04eb60f5b8faded640eb30f59cd620eb18f58ed610eb0c'
    . 'd608eb06d604d760ebb8f5deae40d730eb9cae20d718eb8eae10d70cae08d706ae04af60d7b8ebde5e40af30d79c5e20'
    . 'af18d78e5e10af0c5e08af065f60afb8d7de5f30af9c5f18af8e5f0c5fb8afde5f9c5f8ee940f4b0fa5ce920f498fa4e'
    . 'e910f48ce908f486e904e902d340e9b0f4dcd320e998f4ced310e98cd308e986d304d302a740d3b0e9dca720d398e9ce'
    . 'a710d38ca708d386a704a7024f40a7b0d3dc4f20a798d3ce4f10a78c4f08a7864f044fb0a7dc4f98a7ce4f8c4f864fdc'
    . '4fcee8a0f458fa2ee890f44ce888f446e884e882d1a0e8d8f46ed190e8ccd188e8c6d184d182a3a0d1d8e8eea390d1cc'
    . 'a388d1c6a384a38247a0a3d8d1ee4790a3cc4788a3c64784478247d8a3ee47cc47c647eee850f42ce848f426e844e842'
    . 'd0d0e86cd0c8e866d0c4d0c2a1d0d0eca1c8d0e6a1c4a1c243d0a1ec43c8a1e643c443c243ec43e6e828f416e824e822'
    . 'd068e836d064d062a0e8d076a0e4a0e241e8a0f641e441e2e814e812d034d032a074a072e540f2b0f95ce520f298f94e'
    . 'e510f28ce508f286e504e502cb40e5b0f2dccb20e598f2cecb10e58ccb08e586cb04cb029740cbb0e5dc9720cb98e5ce'
    . '9710cb8c9708cb86970497022f4097b0cbdc2f209798cbce2f10978c2f0897862f042fb097dc2f9897ce2f8c2f862fdc'
    . '2fcef6a0fb586bf0f690fb4c69f8f688fb4668fcf684f682e4a0f258f92eeda0e490fb6eed90f6ccf246ed88e484ed84'
    . 'e482ed82c9a0e4d8f26edba0c990e4ccdb90edcce4c6db88c984db84c982db8293a0c9d8e4eeb7a09390c9ccb790dbcc'
    . 'c9c6b7889384b7849382b78227a093d8c9ee6fa0279093cc6f90b7cc93c66f8827846f84278227d893ee6fd827cc6fcc'
    . '27c66fc627eef650fb2c65f8f648fb2664fcf644647ef642e450f22cecd0e448f226ecc8f666ecc4e442ecc2c8d0e46c'
    . 'd9d0c8c8e466d9c8ece6d9c4c8c2d9c291d0c8ecb3d091c8c8e6b3c8d9e6b3c491c2b3c223d091ec67d023c891e667c8'
    . 'b3e667c423c267c223ec67ec23e667e6f628fb1662fcf624627ef622e428f216ec68f636ec64e422ec62c868e436d8e8'
    . 'c864d8e4c862d8e290e8c876b1e8d8f6b1e490e2b1e221e890f663e821e463e421e263e221f663f6f614617ef612e414'
    . 'ec34e412ec32c834d874c832d8729074b0f49072b0f220f461f420f261f2f60ae40aec1ac81ad83a903ab07ae2a0f158'
    . 'f8aee290f14ce288f146e284e282c5a0e2d8f16ec590e2ccc588e2c6c584c5828ba0c5d8e2ee8b90c5cc8b88c5c68b84'
    . '8b8217a08bd8c5ee17908bcc17888bc61784178217d88bee17cc17c617eef350f9ac35f8f348f9a634fcf344347ef342'
    . 'e250f12ce6d0e248f126e6c8f366e6c4e242e6c2c4d0e26ccdd0c4c8e266cdc8e6e6cdc4c4c2cdc289d0c4ec9bd089c8'
    . 'c4e69bc8cde69bc489c29bc213d089ec37d013c889e637c89be637c413c237c213ec37ec13e637e6fba875f0bafcfba4'
    . '74f8ba7efba2747c743ef328f99632fcf768fbb676fc327ef764f322767ef762e228f116e668e224eee8f776e222eee4'
    . 'e662eee2c468e236cce8c464dde8cce4c462dde4cce2dde288e8c47699e888e4bbe899e488e2bbe499e2bbe211e888f6'
    . '33e811e477e833e411e277e433e277e211f633f6fb9472f8b97efb92727c723ef314317ef734f312737ef732e214e634'
    . 'e212ee74e632ee72c434cc74c432dcf4cc72dcf2887498f48872b9f498f2b9f210f431f410f273f431f273f2fb8a717c'
    . '713ef30af71ae20ae61aee3ac41acc3adc7a883a987ab8fa107a30fa71fa70bee150f0ace148f0a6e144e142c2d0e16c'
    . 'c2c8e166c2c4c2c285d0c2ec85c8c2e685c485c20bd085ec0bc885e60bc40bc20bec0be6f1a8f8d61afcf1a41a7ef1a2'
    . 'e128f096e368e124e364e122e362c268e136c6e8c264c6e4c262c6e284e8c2768de884e48de484e28de209e884f61be8'
    . '09e41be409e21be209f61bf6f9d43af89d7ef9d23a7c3a3ef194197ef3b4f1923b7ef3b2e114e334e112e774e332e772'
    . 'c234c674c232cef4c672cef284748cf484729df48cf29df208f419f408f23bf419f23bf27af0bd7c7a78bd3e7a3c7a1e'
    . 'f9ca397cfbda7b7c393e7b3ef18af39af7bae10ae31ae73aef7ac21ac63ace7adefa843a8c7a9cfabdfa087a18fa39fa'
    . '7978bcbe793c791e38be79be78bc789e785ee0a8e0a4e0a2c168e0b6c164c16282e8c17682e482e205e882f605e405e2'
    . '05f6f0d40d7ef0d2e094e1b4e092e1b2c134c374c132c372827486f4827286f204f40df404f20df2f8ea1d7c1d3ef0ca'
    . 'f1dae08ae19ae3bac11ac33ac77a823a867a8efa047a0cfa1dfa3d789ebe3d3c3d1e1cbe3dbe7d70bebc7d38be9e7d1c'
    . '7d0e3cbc7dbc3c9e7d9e7cb8be5e7c9c7c8e3c5e7cde7c5c7c4e7c2ec0b4c0b28174817202f402f2e0dac09ac1ba813a'
    . '837a027a06fa0ebe1ebc1e9e3eb89f5e3e9c3e8e1e5e3ede7eb0bf5c7e98bf4e7e8c7e863e5c7edc3e4e7ece7e58bf2e'
    . '7e4c7e463e2e7e6e7e2c7e260f5e1f5c1f4e3f589fae3f4c3f461f2e3f6e3f2c3f26',
    // cluster 6
    'abe0d5f853c0a9f0d4fc51e0a8f8d47e50f0a87c5078fad05be0adf8fac859f0acfcfac458f8ac7efac2587cf5d0faec'
    . '5df8f5c8fae65cfcf5c45c7ef5c2ebd0f5ecebc8f5e6ebc4ebc2d7d0ebecd7c8ebe6d7c4d7c2afd0d7ecafc8d7e6afc4'
    . '4bc0a5f0d2fc49e0a4f8d27e48f0a47c4878a43e483cfa684df0a6fcfa644cf8a67efa624c7c4c3ef4e8fa764efcf4e4'
    . '4e7ef4e2e9e8f4f6e9e4e9e2d3e8e9f6d3e4d3e2a7e8d3f6a7e4a7e245e0a2f8d17e44f0a27c4478a23e443c441efa34'
    . '46f8a37efa32467c463ef474477ef472e8f4e8f2d1f4d1f2a3f4a3f242f0a17c4278a13e423c421efa1a437c433ef43a'
    . 'e87ad0fa4178a0be413c411e41be40bc409e2bc095f0cafc29e094f8ca7e28f0947c2878943e283cf9682df096fcf964'
    . '2cf8967ef9622c7c2c3ef2e8f9762efcf2e42e7ef2e2e5e8f2f6e5e4e5e2cbe8e5f6cbe4cbe297e8cbf697e497e2b5e0'
    . 'daf8ed7e69c0b4f0da7c68e0b478da3e6870b43c6838b41e681c25e092f8c97e6de024f0927c6cf0b67c923e6c78243c'
    . '6c3c241e6c1ef93426f8937efb74f9326ef8267cfb726e7c263e6e3ef274277ef6f4f2726f7ef6f2e4f4edf4e4f2edf2'
    . 'c9f4dbf4c9f2dbf293f493f265c0b2f0d97c64e0b278d93e6470b23c6438b21e641c640e22f0917c66f02278913e6678'
    . 'b33e663c221e661ef91a237cfb3a677c233e673ef23af67ae47aecfac8fad9fa91fa62e0b178d8be6270b13c6238b11e'
    . '621c620e217890be6378213c633c211e631e21be63be6170b0bc6138b09e611c610e20bc61bc209e619e60b8b05e609c'
    . '608e205e60de605c604e15e08af8c57e14f08a7c14788a3e143c141ef8b416f88b7ef8b2167c163ef174177ef172e2f4'
    . 'e2f2c5f4c5f28bf48bf235c09af0cd7c34e09a78cd3e34709a3c34389a1e341c340e12f0897c36f01278893e36789b3e'
    . '363c121e361ef89a137cf9ba377c133e373ef13af37ae27ae6fac4facdfa89fabae0dd78eebe74c0ba70dd3c7460ba38'
    . 'dd1e7430ba1c7418ba0e740c32e09978ccbe76e03270993c7670bb3c991e7638321c761c320e760e117888be3378113c'
    . '7778333c111e773c331e771e11be33be77be72c0b970dcbc7260b938dc9e7230b91c7218b90e720c7206317098bc7370'
    . '3138989e7338b99e731c310e730e10bc31bc109e73bc319e739e7160b8b8dc5e7130b89c7118b88e710c710630b8985e'
    . '71b8309c719c308e718e105e30de71de70b0b85c7098b84e708c7086305c70dc304e70ce7058b82e704c7046302e706e'
    . '702c70260af0857c0a78853e0a3c0a1e0b7c0b3ef0bae17ac2fa85fa1ae08d78c6be1a708d3c1a388d1e1a1c1a0e0978'
    . '84be1b78093c1b3c091e1b1e09be1bbe3ac09d70cebc3a609d38ce9e3a309d1c3a189d0e3a0c3a0619708cbc3b701938'
    . '8c9e3b38191c3b1c190e3b0e08bc19bc089e3bbc199e3b9ebd60deb8ef5e7a40bd30de9c7a20bd18de8e7a10bd0c7a08'
    . 'bd067a0439609cb8ce5e7b6039309c9c7b30bd9c9c8e7b18390c7b0c39067b0618b88c5e39b8189c7bb8399c188e7b9c'
    . '398e7b8e085e18de39de7bde7940bcb0de5c7920bc98de4e7910bc8c7908bc867904790238b09c5c79b038989c4e7998'
    . 'bcce798c38867986185c38dc184e79dc38ce79ce78a0bc58de2e7890bc4c7888bc467884788238589c2e78d8384c78cc'
    . '384678c6182e386e78ee7850bc2c7848bc2678447842382c786c382678667828bc167824782238167836057882be053c'
    . '051e05be0d7086bc0d38869e0d1c0d0e04bc0dbc049e0d9e1d608eb8c75e1d308e9c1d188e8e1d0c1d060cb8865e1db8'
    . '0c9c1d9c0c8e1d8e045e0cde1dde3d409eb0cf5c3d209e98cf4e3d109e8c3d089e863d043d021cb08e5c3db01c988e4e'
    . '3d989ece3d8c1c863d860c5c1cdc0c4e3ddc1cce3dcebea0df58efaebe90df4cbe88df46be84be823ca09e58cf2e7da0'
    . '3c909e4c7d90becc9e467d883c847d843c827d821c588e2e3cd81c4c7dd83ccc1c467dcc3cc67dc60c2e1c6e3cee7dee'
    . 'be50df2cbe48df26be44be423c509e2c7cd03c489e267cc8be667cc43c427cc21c2c3c6c1c267cec3c667ce6be28df16'
    . 'be24be223c289e167c683c247c643c227c621c163c367c76be14be123c147c343c127c3202bc029e06b8835e069c068e'
    . '025e06de0eb0875c0e98874e0e8c0e86065c0edc064e0ece1ea08f58c7ae1e908f4c1e888f461e841e820e58872e1ed8'
    . '8f6e1ecc0e461ec6062e0e6e1eee9f50cfac9f48cfa69f449f421e508f2c3ed09f6c8f263ec81e443ec41e423ec20e2c'
    . '1e6c0e263eec1e663ee6dfa8efd6dfa4dfa29f28cf96bf689f24bf649f22bf621e288f163e681e247ee83e641e227ee4'
    . '3e627ee20e161e363e767ef6df94df929f14bf349f12bf321e143e341e127e743e327e72df8a9f0abf1a1e0a3e1a7e3a'
    . '035c034e075883ae074c0746032e076e0f5087ac0f4887a60f440f42072c0f6c07260f668fa8c7d68fa48fa20f288796'
    . '1f688fb61f640f221f6207160f361f76cfd4cfd28f949fb48f929fb20f141f340f123f741f323f72cfca8f8a9f9a0f0a'
    . '1f1a3f3a03ac03a607a883d607a407a2039607b687d487d207940fb407920fb2c7ea',
  ];

?>
