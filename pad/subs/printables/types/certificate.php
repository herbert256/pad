<?php

  // {printable:certificate data='graduates', title='Certificate of Completion'}...{/printable:certificate}
  // - a certificate, one to a sheet, A4 turned on its side: a double frame, the title, the
  // content between the tags in the middle - who, for what, when - and a signature line for
  // every name of signed=, 'Jane Doe, Director; Sam Lee, Teacher', a name and what follows its
  // comma under each line. color= the frame and the title (#1f3a68), paper= a4, letter or a5,
  // portrait keeps it upright; size= (14 pt) the text of the content. Set in a serif.
  //
  //   {printable:certificate data='graduates', title='Certificate of Completion', signed='Jane Doe, Director'}
  //     <p>This certifies that</p><h1>{$name}</h1><p>completed the course {$course} on {$date}.</p>
  //   {/printable:certificate}

  $padCertName  = padPrintableChoice ( 'paper', 'a4', [ 'a4', 'letter', 'a5' ] );
  $padCertPaper = PAD_PRINTABLE_PAPERS [$padCertName];

  if ( ! padPrintableFlag ( 'portrait' ) )
    $padCertPaper = [ $padCertPaper [1], $padCertPaper [0] ];

  $padCertColor = padPrintableColor ( 'color', '#1f3a68' );
  $padCertTitle = padWidgetAttr ( padPrintableText ( 'title', 'Certificate' ) );
  $padCertSize  = padPrintableNumber ( 'size', 14, 6, 48 );
  $padCertSigns = array_values ( array_filter ( array_map ( 'trim', explode ( ';', padPrintableText ( 'signed', '' ) ) ), fn ( $one ) => $one !== '' ) );

  return padPrintablePlan ( [

    'name'      => [ 'certificate', 'certificates' ],
    'paper'     => $padCertPaper,
    'paperName' => ( $padCertName == 'letter' ? 'Letter' : strtoupper ( $padCertName ) ),
    'font'      => 'serif',

    'item' => function ( $cell, $parts ) use ( $padCertPaper, $padCertColor, $padCertTitle, $padCertSize, $padCertSigns ) {

      list ( $w, $h ) = $padCertPaper;

      $draw = [
        [ 'rect', 8, 8, $w - 16, $h - 16, [ 'stroke' => $padCertColor, 'width' => 2.2 ] ],
        [ 'rect', 11, 11, $w - 22, $h - 22, [ 'stroke' => $padCertColor, 'width' => 0.6 ] ],
        [ 'box', 20, round ( $h * 0.11, 3 ), $w - 40, round ( $h * 0.17, 3 ), "<h1>$padCertTitle</h1>",
          [ 'pad' => 0, 'size' => round ( $padCertSize * 1.4, 2 ), 'align' => 'center', 'valign' => 'middle', 'font' => 'serif', 'color' => $padCertColor ] ],
        [ 'box', 25, round ( $h * 0.29, 3 ), $w - 50, round ( $h * 0.42, 3 ), $parts [''] ?? '',
          [ 'pad' => 2, 'size' => $padCertSize, 'align' => 'center', 'valign' => 'middle', 'font' => 'serif' ] ] ];

      // A line to sign on for every name, side by side, the name and its role under it.

      $count = count ( $padCertSigns );
      $slot  = $count ? ( $w - 60 ) / $count : 0;
      $line  = round ( $h * 0.8, 3 );

      foreach ( $padCertSigns as $i => $one ) {

        $x0   = round ( 30 + $i * $slot + max ( 6, $slot * 0.12 ), 3 );
        $x1   = round ( 30 + ( $i + 1 ) * $slot - max ( 6, $slot * 0.12 ), 3 );
        $name = explode ( ',', $one, 2 );

        $draw [] = [ 'line', $x0, $line, $x1, $line, [ 'stroke' => '#555555', 'width' => 0.6 ] ];
        $draw [] = [ 'box', $x0, $line + 1.5, $x1 - $x0, 14,
                     '<b>' . padWidgetAttr ( trim ( $name [0] ) ) . '</b>' . ( isset ( $name [1] ) ? '<br>' . padWidgetAttr ( trim ( $name [1] ) ) : '' ),
                     [ 'pad' => 0, 'size' => 10, 'align' => 'center', 'font' => 'serif' ] ];

      }

      return $draw;

    },

  ] );

?>
