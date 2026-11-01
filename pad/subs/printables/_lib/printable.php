<?php

  // The printables subsystem: pages made for paper - sheets of labels, name badges, business
  // cards, tickets with a stub, a certificate, envelopes, letters and invoices for a window
  // envelope, index cards and folded place cards.
  //
  //   {printable:labels data='products', sheet='a4-3x8'}
  //     <b>{$name}</b> {barcode:ean13 $ean/}
  //   {/printable:labels}
  //   {printable 'envelope', data='customers', paper='dl', sender='PAD, Main Street 1, Utrecht'}
  //     {$name}<br>{$street}<br>{$postcode} {$city}
  //   {/printable}
  //   {printable:tickets data='seats', pdf}<h2>{$show}</h2><aside>{qr $code/}</aside>{/printable:tickets}
  //
  // The content between the tags is the template of one item - a label, a badge, a letter -
  // rendered once per row of the data and laid out by the kind on sheets of paper. A kind is
  // a file of subs/printables/types/ - the files there are the kinds there are
  // (padPrintableKinds, lib/printable.php) - which reads its options and answers its plan
  // (padPrintablePlan): the paper, the places of the items on a sheet, and what each item
  // draws there - boxes of content, rectangles and lines, in millimetres from the sheet's top
  // left corner. Some kinds take the parts of an item from the elements that say what they
  // are: a ticket's stub is its <aside>, a letter's address its <address>, its information
  // block its <aside>, its letterhead and its foot its <header> and <footer>.
  //
  // One plan, three renderings from the same numbers. On the screen the sheets lie on a grey
  // desk, white paper with a shadow, each at its size in millimetres, the edges of the labels
  // dashed; scale= makes the preview smaller. Printed, the page prints the sheets alone - the
  // frame, the menus and everything else of the page left out - one sheet a page, its @page of
  // its own size and no margin, the paper's colours kept. In a {pdf}, or with pdf on the tag,
  // the PDF writer (tags/_lib/pdf.php) reads the same plan from the data-pdf-* attributes of
  // the markup: a sheet is a page of its size, a box sets its content where the CSS puts it -
  // the text sizes, the alignment and the font kept - and the barcodes and QR codes of the
  // codes subsystem are drawn. What the PDF cannot: CSS of a page's own, pictures other than
  // JPEG, an SVG with curves, a table's own look (its grid is the writer's), letter spacing,
  // and a text set beside a picture; the text of a box is set in the writer's own fonts,
  // Helvetica, Times and Courier.
  //
  // Every item is one region of a sheet: what does not fit is cut at its edge, on the screen,
  // on paper and in the PDF alike - a letter is one page.
  //
  // The positions are no inline styles: every box, rectangle and line takes a class of the
  // printable's own <style> - written with the CSP nonce, as the rest - so a page with a
  // strict Content Security Policy prints the same.
  //
  // padPrintableTag     a kind's items as sheets: the kind's file, copies=, the build
  // padPrintableSplit   the items of a pair's rendering, cut at the markers
  // padPrintableParts   an item cut into the parts of its kind
  // padPrintablePlan    a kind's plan with its defaults
  // padPrintableBuild   the plan and the items as the markup, its style and its bar
  // padPrintableGrid    the grid of a sheet: sheet=, paper=, across=, down=, width=,
  //                     height=, margin=, pitch=, skip=
  // padPrintableBox     the look of a box: padding=, size=, align=
  // padPrintableCrop    the crop marks round a grid
  // padPrintable<Type>  an option read and checked: Number, Int, Choice, Flag, Text, Color,
  //                     Pair, Paper
  // padPrintableStyle   the shared style, assets/printable.css, and the kind's, once a page
  // padPrintableScript  the Print button's script, once a page

  const PAD_PRINTABLE_ITEM = '<!--pad:printable-item-->';

  const PAD_PRINTABLE_PAPERS = [ 'a3' => [ 297, 420 ], 'a4' => [ 210, 297 ], 'a5' => [ 148, 210 ], 'a6' => [ 105, 148 ],
                                 'letter' => [ 215.9, 279.4 ], 'legal' => [ 215.9, 355.6 ] ];

  const PAD_PRINTABLE_ENVELOPES = [ 'dl' => [ 220, 110 ], 'c4' => [ 324, 229 ], 'c5' => [ 229, 162 ], 'c6' => [ 162, 114 ] ];

  const PAD_PRINTABLE_MOST = 5000;

  // ------------------------------------------------------------------------------------
  // The items.
  // ------------------------------------------------------------------------------------

  // The items of a pair's rendering: the text after each marker, trimmed - NULL when there is
  // no marker, as for the @else@ half of a level left without rows.

  function padPrintableSplit ( $content ) {

    if ( ! str_contains ( (string) $content, PAD_PRINTABLE_ITEM ) )
      return NULL;

    $items = explode ( PAD_PRINTABLE_ITEM, (string) $content );

    array_shift ( $items );

    return array_map ( 'trim', $items );

  }

  // An item cut into the parts of its kind, by the HTML elements that say what they are: the
  // first <aside>, <address>, <header> or <footer> of the item - as the kind names them - is
  // that part, its inner HTML, and taken out; '' is what is left. Elements, not markers of
  // the kind of @else@: the strict check takes every @word@ of a template for a construct of
  // the engine.

  function padPrintableParts ( $html, $names ) {

    $parts = [ '' => (string) $html ];

    foreach ( $names as $name ) {
      $parts [$name] = '';
      if ( preg_match ( "#<$name\\b[^>]*>(.*?)</$name\\s*>#is", $parts [''], $match, PREG_OFFSET_CAPTURE ) ) {
        $parts [$name] = trim ( $match [1] [0] );
        $parts ['']    = substr_replace ( $parts [''], '', $match [0] [1], strlen ( $match [0] [0] ) );
      }
    }

    $parts [''] = trim ( $parts [''] );

    return $parts;

  }

  // A kind's items as its sheets: the kind's file answers its plan, every item is written
  // copies= times, and the build lays them out. The kind is at hand for the messages in
  // $padPrintableNow, put back afterwards for a printable inside another.

  function padPrintableTag ( $kind, $items ) {

    global $padPrintableNow, $padCheckSyntax;

    $before           = $padPrintableNow ?? NULL;
    $padPrintableNow  = [ 'kind' => $kind ];

    $plan   = padPrintableRun ( $kind );
    $copies = padPrintableInt ( 'copies', 1, 1, 1000 );
    $all    = [];

    // Counted before they are made, and no more made than the most: 10000 rows of 1000
    // copies were ten million items, 256 MB, before the count that refuses them.

    if ( count ( $items ) * $copies > PAD_PRINTABLE_MOST and $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . ' has ' . count ( $items ) * $copies . ' items - at most ' . PAD_PRINTABLE_MOST . ' a printable' );

    foreach ( $items as $item )
      for ( $i = 0; $i < $copies and count ( $all ) < PAD_PRINTABLE_MOST; $i++ )
        $all [] = $item;

    $html = padPrintableBuild ( $kind, $plan, $all );

    $padPrintableNow = $before;

    return $html;

  }

  // The kind's file, run in a function of its own: its variables are its locals.

  function padPrintableRun ( $padPrintableFile ) {

    return include PAD . "subs/printables/types/$padPrintableFile.php";

  }

  // The tag the printable is written as, for what a message says: {printable:labels} or
  // {printable 'labels'}.

  function padPrintableWhat () {

    global $pad, $padType, $padPrintableNow;

    $kind = $padPrintableNow ['kind'] ?? '';

    return ( ( $padType [$pad] ?? '' ) == 'printable' ) ? "{printable:$kind}" : "{printable '$kind'}";

  }

  // ------------------------------------------------------------------------------------
  // The plan.
  // ------------------------------------------------------------------------------------

  // A kind's plan, its defaults filled in:
  //
  //   name    what the items are, one and more - [ 'label', 'labels' ]
  //   paper   [ width, height ] in mm, and paperName what the bar calls it
  //   cells   the places of the items on a sheet, [ x, y, width, height ] each - the whole
  //           sheet for a kind of one item a sheet
  //   skip    the cells of the first sheet left empty
  //   parts   the elements the items are cut into - [ 'aside' ]
  //   item    fn ( $cell, $parts, $n ) - what an item draws in its cell; for an empty cell
  //           $parts is NULL, and the kind draws what a sheet shows there - an edge
  //   sheet   fn ( $sheet ) - what every sheet draws, the crop marks and the fold marks
  //   font    sans or serif
  //
  // What an item and a sheet draw is a list of:
  //
  //   [ 'box',  x, y, w, h, html, [ pad, size, align, valign, turn, font, fill, color, class ] ]
  //   [ 'rect', x, y, w, h, [ fill, stroke, width, dash, screen ] ]
  //   [ 'line', x1, y1, x2, y2, [ stroke, width, dash, screen ] ]
  //
  // in millimetres; a size in points; screen for what the screen alone shows - an edge to
  // see, the stamp of an envelope - left out on paper and in the PDF.

  function padPrintablePlan ( $plan ) {

    if ( isset ( $plan ['grid'] ) )
      $plan += [ 'paper' => $plan ['grid'] ['paper'], 'paperName' => $plan ['grid'] ['name'],
                 'cells' => $plan ['grid'] ['cells'], 'skip' => $plan ['grid'] ['skip'] ];

    $plan += [ 'name' => [ 'item', 'items' ], 'paper' => PAD_PRINTABLE_PAPERS ['a4'], 'paperName' => 'A4',
               'skip' => 0, 'parts' => [], 'sheet' => NULL, 'font' => 'sans' ];

    $plan ['cells'] ??= [ [ 0, 0, $plan ['paper'] [0], $plan ['paper'] [1] ] ];

    return $plan;

  }

  // ------------------------------------------------------------------------------------
  // The build.
  // ------------------------------------------------------------------------------------

  // The items on their sheets: the first sheet from cell skip= on, a new sheet when the cells
  // are full; every cell drawn by the kind - an empty one too, to show the sheet as it is -
  // and every sheet's own marks. Then the markup - the bar, the desk, a section a sheet - its
  // style, and the script of the Print button; with pdf the links to the document, and the
  // sheets as the document when the page is asked for it.

  function padPrintableBuild ( $kind, $plan, $items ) {

    global $padGuardNested;

    $cells = $plan ['cells'];
    $per   = max ( 1, count ( $cells ) );
    $skip  = min ( $plan ['skip'], $per - 1 );
    $count = count ( $items );
    $total = max ( 1, (int) ceil ( ( $skip + $count ) / $per ) );
    $id    = padWidgetId ( 'printable', [ $kind, $plan ['paper'], $cells, $items ] );
    $page  = 'pp-' . substr ( md5 ( $id ), 0, 8 );
    $css   = [];
    $draw  = '';

    for ( $sheet = 0; $sheet < $total; $sheet++ ) {

      $prims = $plan ['sheet'] ? ( $plan ['sheet'] ) ( $sheet ) : [];

      foreach ( $cells as $c => $cell ) {
        $n     = $sheet * $per + $c - $skip;
        $parts = ( $n >= 0 and $n < $count ) ? padPrintableParts ( $items [$n], $plan ['parts'] ) : NULL;
        foreach ( ( $plan ['item'] ) ( $cell, $parts, $n ) as $prim )
          $prims [] = $prim;
      }

      $draw .= padProtect ( '<section class="pp-sheet" data-pdf-sheet="' . padPrintableMm ( $plan ['paper'] [0] ) . ' ' . padPrintableMm ( $plan ['paper'] [1] )
                          . '" aria-label="Sheet ' . ( $sheet + 1 ) . ' of ' . $total . '">' );

      foreach ( $prims as $prim )
        if ( $prim )
          $draw .= padPrintablePrim ( $css, $prim );

      $draw .= padProtect ( '</section>' );

    }

    // The bar above the desk: how many on how many sheets of what, the Print button - and with
    // pdf the links to the document, which is the sheets themselves when the page is asked
    // for it with &padPdf, as {pdf preview} answers its own.

    list ( $one, $more ) = $plan ['name'];

    $paper = $plan ['paperName'] . ', ' . padPrintableMm ( $plan ['paper'] [0] ) . ' × ' . padPrintableMm ( $plan ['paper'] [1] ) . ' mm';
    $about = $count . ' ' . ( $count == 1 ? $one : $more ) . ' on ' . $total . ' ' . ( $total == 1 ? 'sheet' : 'sheets' ) . " of $paper";
    $links = '';
    $html  = '';

    // Not inside a {page} - a page shown in another, the manual's {example}: only the page
    // the request asked for answers a document, as {pdf} has it, and its links asked the
    // page around it, which answered its HTML. Nor inside a {pdf}, whose document the sheets
    // are a part of already: asked for its own, the printable's sheets came first, under its
    // name, and the {pdf}'s content brought them again. A {cache} section round the links is
    // not kept, as one round a {pdf} is not: on a hit the printable did not run, and the
    // page asked with &padPdf answered its HTML.

    if ( padPrintableFlag ( 'pdf' ) ) {

      $file = padPrintableText ( 'file', $kind );

      if ( ! ( $padGuardNested ?? FALSE ) and ! padPrintableInPdf () ) {
        include_once PAD . 'tags/_lib/pdf.php';
        $links = padPdfLinks ( $file );
        padFragmentKeepNot ();
      }

    }

    $scale = padPrintableNumber ( 'scale', 1, 0.1, 3 );

    $style = padPrintableStyle ( $kind )
           . padProtect ( '<style' . padStyleNonce () . '>'
                        . ".$id .pp-sheet{width:" . padPrintableMm ( $plan ['paper'] [0] ) . 'mm;height:' . padPrintableMm ( $plan ['paper'] [1] ) . "mm;page:$page}"
                        . "@page $page{size:" . padPrintableMm ( $plan ['paper'] [0] ) . 'mm ' . padPrintableMm ( $plan ['paper'] [1] ) . 'mm;margin:0}'
                        . implode ( '', array_map ( fn ( $rule, $class ) => ".$id .$class{" . $rule . '}', array_keys ( $css ), $css ) )
                        . ( $scale != 1 ? "@media screen{.$id .pp-sheets{zoom:$scale}}" : '' )
                        . '</style>' );

    $html = padProtect ( "<div class=\"pad-printable pad-printable-$kind pp-font-{$plan ['font']} $id\" role=\"region\" aria-label=\""
                       . padWidgetAttr ( ucfirst ( $about ) ) . '">'
                       . '<div class="pp-bar" data-pdf-skip><span class="pp-about">' . padWidgetAttr ( ucfirst ( $about ) ) . '</span>'
                       . '<button type="button" class="pp-print">Print</button>' . $links . '</div>'
                       . '<div class="pp-sheets">' )
          . $draw
          . padProtect ( '</div></div>' );

    if ( $links !== '' and isset ( $_GET ['padPdf'] ) and ! ( $padGuardNested ?? FALSE ) )
      padPdfAsk ( $html, [ 'file' => $file, 'download' => ( $_GET ['padPdf'] === 'download' ), 'size' => 'A4', 'landscape' => FALSE,
                           'font' => 'helvetica', 'title' => ucfirst ( $more ) ] );

    return $style . $html . padPrintableScript ();

  }

  // Whether a {pdf} pair stands round the printable: its content - the sheets with it - is
  // that document.

  function padPrintableInPdf () {

    global $pad, $padTag, $padType;

    for ( $i = $pad - 1; $i >= 0; $i-- )
      if ( ( $padTag [$i] ?? '' ) === 'pdf' and ( $padType [$i] ?? '' ) === 'pad' )
        return TRUE;

    return FALSE;

  }

  // One thing drawn: its markup, its position a class of the printable's style, and the same
  // numbers for the PDF in its data-pdf-* attributes. A box holds its html in an inner block,
  // so its text and inline elements flow as in any block.

  function padPrintablePrim ( &$css, $prim ) {

    $mm   = fn ( $n ) => padPrintableMm ( $n ) . 'mm';
    $kind = $prim [0];

    if ( $kind == 'box' ) {

      list ( , $x, $y, $w, $h, $html ) = $prim;

      $o    = ( $prim [6] ?? [] ) + [ 'pad' => 0, 'size' => 10, 'align' => 'left', 'valign' => 'top', 'turn' => 0, 'font' => '', 'fill' => '', 'color' => '', 'class' => '', 'screen' => FALSE ];
      $rule = 'left:' . $mm ( $x ) . ';top:' . $mm ( $y ) . ';width:' . $mm ( $w ) . ';height:' . $mm ( $h ) . ';padding:' . $mm ( $o ['pad'] )
            . ';font-size:' . padPrintableMm ( $o ['size'] ) . 'pt;text-align:' . $o ['align']
            . ( $o ['fill']  !== '' ? ';background:' . $o ['fill'] : '' )
            . ( $o ['color'] !== '' ? ';color:' . $o ['color'] : '' );

      $class = trim ( 'pp-box ' . padPrintableClass ( $css, $rule )
                    . [ 'top' => '', 'middle' => ' pp-mid', 'bottom' => ' pp-end' ] [ $o ['valign'] ]
                    . ( $o ['turn'] ? ' pp-turn' : '' ) . ( $o ['font'] == 'serif' ? ' pp-serif' : '' )
                    . ( $o ['class'] !== '' ? ' ' . $o ['class'] : '' ) . ( $o ['screen'] ? ' pp-screen' : '' ) );

      if ( $o ['screen'] )
        return padProtect ( "<div class=\"$class\" data-pdf-skip aria-hidden=\"true\"><div class=\"pp-in\">" ) . $html . padProtect ( '</div></div>' );

      $pdf = 'pad='. padPrintableMm ( $o ['pad'] ) . ';size=' . padPrintableMm ( $o ['size'] ) . ';align=' . $o ['align'] . ';valign=' . $o ['valign']
           . ( $o ['turn'] ? ';turn=180' : '' ) . ( $o ['font'] == 'serif' ? ';font=times' : '' )
           . ( $o ['fill'] !== '' ? ';fill=' . $o ['fill'] : '' ) . ( $o ['color'] !== '' ? ';color=' . $o ['color'] : '' );

      return padProtect ( "<div class=\"$class\" data-pdf-box=\"" . padPrintableMms ( $x, $y, $w, $h ) . "\" data-pdf-style=\"$pdf\"><div class=\"pp-in\">" )
           . $html
           . padProtect ( '</div></div>' );

    }

    $o = ( $prim [5] ?? [] ) + [ 'fill' => '', 'stroke' => '', 'width' => 0.5, 'dash' => FALSE, 'screen' => FALSE ];

    $border = ( $o ['stroke'] !== '' ) ? padPrintableMm ( $o ['width'] ) . 'pt ' . ( $o ['dash'] ? 'dashed' : 'solid' ) . ' ' . $o ['stroke'] : '';
    $pdf    = ( $o ['fill'] !== '' ? 'fill=' . $o ['fill'] . ';' : '' ) . ( $o ['stroke'] !== '' ? 'stroke=' . $o ['stroke'] . ';' : '' )
            . 'width=' . padPrintableMm ( $o ['width'] ) . ( $o ['dash'] ? ';dash=1' : '' );
    $where  = $o ['screen'] ? 'data-pdf-skip' : '';

    if ( $kind == 'rect' ) {

      list ( , $x, $y, $w, $h ) = $prim;

      $rule = 'left:' . $mm ( $x ) . ';top:' . $mm ( $y ) . ';width:' . $mm ( $w ) . ';height:' . $mm ( $h )
            . ( $o ['fill'] !== '' ? ';background:' . $o ['fill'] : '' ) . ( $border !== '' ? ";border:$border" : '' );

      $attrs = $where ?: 'data-pdf-rect="' . padPrintableMms ( $x, $y, $w, $h ) . "\" data-pdf-style=\"$pdf\"";

    } else {

      list ( , $x1, $y1, $x2, $y2 ) = $prim;

      $rule = ( $y1 == $y2 )
            ? 'left:' . $mm ( min ( $x1, $x2 ) ) . ';top:' . $mm ( $y1 ) . ';width:' . $mm ( abs ( $x2 - $x1 ) ) . ";height:0;border-top:$border"
            : 'left:' . $mm ( $x1 ) . ';top:' . $mm ( min ( $y1, $y2 ) ) . ';width:0;height:' . $mm ( abs ( $y2 - $y1 ) ) . ";border-left:$border";

      $attrs = $where ?: 'data-pdf-line="' . padPrintableMms ( $x1, $y1, $x2, $y2 ) . "\" data-pdf-style=\"$pdf\"";

    }

    $class = 'pp-' . $kind . ' ' . padPrintableClass ( $css, $rule ) . ( $o ['screen'] ? ' pp-screen' : '' );

    return padProtect ( "<div class=\"$class\" $attrs aria-hidden=\"true\"></div>" );

  }

  // The class of a rule of the printable's style: the same rule - the same place, the same
  // look - the same class, pp-g1, pp-g2 ...

  function padPrintableClass ( &$css, $rule ) {

    return $css [$rule] ??= 'pp-g' . ( count ( $css ) + 1 );

  }

  // A length as the markup writes it: millimetres - or points - to a thousandth, no zeros
  // behind.

  function padPrintableMm ( $n ) {

    $text = rtrim ( rtrim ( number_format ( (float) $n, 3, '.', '' ), '0' ), '.' );

    return ( $text === '-0' ) ? '0' : $text;

  }

  function padPrintableMms ( ...$numbers ) {

    return implode ( ' ', array_map ( 'padPrintableMm', $numbers ) );

  }

  // ------------------------------------------------------------------------------------
  // The grid of a sheet.
  // ------------------------------------------------------------------------------------

  // The places of the items on a sheet: a preset - [ paper, across, down, width, height,
  // pitch across, pitch down, top margin, left margin ], named by its paper and its grid,
  // a4-3x8 - and over it the options: sheet= the preset, paper= and landscape, across= and
  // down=, width= and height= of an item, margin= the top and the left margin (one number for
  // both), pitch= the distance from an item to the next across and down; a margin not given
  // centres the grid on the paper. skip= the cells of the first sheet left empty, for a sheet
  // used before. A grid that does not fit its paper is an error under the strict check.

  function padPrintableGrid ( $presets, $default ) {

    global $padCheckSyntax;

    $name   = padPrintableChoice ( 'sheet', $default, array_keys ( $presets ) );
    $preset = $presets [$name];

    list ( $paperName, $across, $down, $width, $height, $hpitch, $vpitch ) = $preset;

    list ( $paperName, $paper ) = padPrintablePaper ( PAD_PRINTABLE_PAPERS, $paperName );

    $across = padPrintableInt ( 'across', $across, 1, 20 );
    $down   = padPrintableInt ( 'down',   $down,   1, 40 );
    $width  = padPrintableNumber ( 'width',  $width,  5, 500 );
    $height = padPrintableNumber ( 'height', $height, 5, 500 );

    $pitch  = padPrintablePair ( 'pitch',  NULL, 1, 500 );
    $margin = padPrintablePair ( 'margin', NULL, 0, 200 );

    $hpitch = $pitch ? $pitch [0] : max ( $hpitch, $width );
    $vpitch = $pitch ? $pitch [1] : max ( $vpitch, $height );

    $spanX  = ( $across - 1 ) * $hpitch + $width;
    $spanY  = ( $down   - 1 ) * $vpitch + $height;

    $top    = $margin ? $margin [0] : ( $preset [7] ?? ( $paper [1] - $spanY ) / 2 );
    $left   = $margin ? $margin [1] : ( $preset [8] ?? ( $paper [0] - $spanX ) / 2 );

    // The message gives the room the grid takes, and where it starts when margin= says so:
    // the far corner it gave read a centred grid too wide as fitting and one too tall by the
    // margin it had made up.

    if ( ( $top < -0.01 or $left < -0.01 or $left + $spanX > $paper [0] + 0.01 or $top + $spanY > $paper [1] + 0.01 ) and $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . " does not fit on its paper - $across across and $down down of "
                 . padPrintableMm ( $width ) . ' by ' . padPrintableMm ( $height ) . ' mm take ' . padPrintableMm ( $spanX ) . ' by '
                 . padPrintableMm ( $spanY ) . ' mm' . ( $margin ? ' from ' . padPrintableMm ( $top ) . ' mm down and ' . padPrintableMm ( $left ) . ' mm across' : '' )
                 . " of $paperName, " . padPrintableMm ( $paper [0] ) . ' by ' . padPrintableMm ( $paper [1] ) . ' mm' );

    $cells = [];

    for ( $r = 0; $r < $down; $r++ )
      for ( $c = 0; $c < $across; $c++ )
        $cells [] = [ round ( $left + $c * $hpitch, 3 ), round ( $top + $r * $vpitch, 3 ), $width, $height ];

    $skip = padPrintableInt ( 'skip', 0, 0, $across * $down - 1 );

    return [ 'name' => $paperName, 'paper' => $paper, 'cells' => $cells, 'skip' => $skip, 'across' => $across, 'down' => $down,
             'width' => $width, 'height' => $height, 'hpitch' => $hpitch, 'vpitch' => $vpitch, 'top' => $top, 'left' => $left ];

  }

  // The paper: paper= one of the sizes given - A4 unless the kind says otherwise - and
  // landscape turns it. [ the name the bar shows, [ width, height ] ].

  function padPrintablePaper ( $sizes, $default ) {

    $name = padPrintableChoice ( 'paper', $default, array_keys ( $sizes ) );
    $size = $sizes [$name];

    if ( padPrintableFlag ( 'landscape' ) and $size [0] < $size [1] )
      $size = [ $size [1], $size [0] ];

    return [ in_array ( $name, [ 'letter', 'legal' ] ) ? ucfirst ( $name ) : strtoupper ( $name ), $size ];

  }

  // The look of a box: padding= in mm round its content, size= the text size in points,
  // align= left, center or right - the kind's defaults for what is not given.

  function padPrintableBox ( $defaults ) {

    return [ 'pad'    => padPrintableNumber ( 'padding', $defaults ['pad'] ?? 2, 0, 40 ),
             'size'   => padPrintableNumber ( 'size', $defaults ['size'] ?? 10, 4, 96 ),
             'align'  => padPrintableChoice ( 'align', $defaults ['align'] ?? 'left', [ 'left', 'center', 'right' ] ),
             'valign' => $defaults ['valign'] ?? 'top' ] + $defaults;

  }

  // The crop marks round a grid: short lines in the margin, in line with every edge of the
  // items - where the cutter goes - kept off the items themselves, as long as the margin has
  // room for them.

  function padPrintableCrop ( $grid, $color = '#000000' ) {

    $marks = [];
    $gap   = 1.5;
    $style = [ 'stroke' => $color, 'width' => 0.3 ];

    $xs = $ys = [];

    for ( $c = 0; $c < $grid ['across']; $c++ ) {
      $xs [] = round ( $grid ['left'] + $c * $grid ['hpitch'], 3 );
      $xs [] = round ( $grid ['left'] + $c * $grid ['hpitch'] + $grid ['width'], 3 );
    }

    for ( $r = 0; $r < $grid ['down']; $r++ ) {
      $ys [] = round ( $grid ['top'] + $r * $grid ['vpitch'], 3 );
      $ys [] = round ( $grid ['top'] + $r * $grid ['vpitch'] + $grid ['height'], 3 );
    }

    $xs = array_values ( array_unique ( $xs ) );
    $ys = array_values ( array_unique ( $ys ) );

    $top    = $grid ['top'];
    $bottom = $grid ['top'] + ( $grid ['down'] - 1 ) * $grid ['vpitch'] + $grid ['height'];
    $left   = $grid ['left'];
    $right  = $grid ['left'] + ( $grid ['across'] - 1 ) * $grid ['hpitch'] + $grid ['width'];

    $long = fn ( $room ) => min ( 5, $room - $gap - 1 );

    if ( ( $len = $long ( $top ) ) > 1 )
      foreach ( $xs as $x ) $marks [] = [ 'line', $x, $top - $gap - $len, $x, $top - $gap, $style ];

    if ( ( $len = $long ( $grid ['paper'] [1] - $bottom ) ) > 1 )
      foreach ( $xs as $x ) $marks [] = [ 'line', $x, $bottom + $gap, $x, $bottom + $gap + $len, $style ];

    if ( ( $len = $long ( $left ) ) > 1 )
      foreach ( $ys as $y ) $marks [] = [ 'line', $left - $gap - $len, $y, $left - $gap, $y, $style ];

    if ( ( $len = $long ( $grid ['paper'] [0] - $right ) ) > 1 )
      foreach ( $ys as $y ) $marks [] = [ 'line', $right + $gap, $y, $right + $gap + $len, $y, $style ];

    return $marks;

  }

  // The edge of a cell: dashed, on the screen alone - or printed too, as the line to cut
  // along, with outline.

  function padPrintableEdge ( $cell, $printed = FALSE ) {

    return [ 'rect', $cell [0], $cell [1], $cell [2], $cell [3],
             [ 'stroke' => $printed ? '#9a9993' : '#b9b8b1', 'width' => $printed ? 0.3 : 0.5, 'dash' => ! $printed, 'screen' => ! $printed ] ];

  }

  // A letter for a window envelope, one to an A4 sheet, laid out as DIN 5008 lays out its
  // form B - what the letter and the invoice share. The item's <address> is the address, in
  // the address field 20 mm from the left and 45 mm from the top, 85 by 45 mm, its lower
  // 27.3 mm, under the return line of sender=; the field stands in the window of a DL
  // envelope when the sheet is folded in three at the fold marks, 105 and 210 mm down, the
  // hole mark between them. Its <aside> is the information block at the right of the field -
  // the date, a reference - its <header> the letterhead above it all, its <footer> the foot of
  // the page, and the rest the body, from 98.46 mm down. size= (10 pt) the body; title= a
  // heading over the body - the invoice's. The screen shows the window of the envelope.

  function padPrintableDin5008 ( $name, $title ) {

    $sender = implode ( ' · ', array_map ( fn ( $line ) => padWidgetAttr ( trim ( $line ) ),
                                           array_filter ( explode ( ',', padPrintableText ( 'sender', '' ) ), fn ( $l ) => trim ( $l ) !== '' ) ) );
    $size   = padPrintableNumber ( 'size', 10, 6, 16 );
    $title  = ( $title !== '' ) ? '<h1>' . padWidgetAttr ( $title ) . '</h1>' : '';
    $mark   = [ 'stroke' => '#8a8984', 'width' => 0.4 ];

    return padPrintablePlan ( [

      'name'      => $name,
      'paper'     => PAD_PRINTABLE_PAPERS ['a4'],
      'paperName' => 'A4',
      'parts'     => [ 'address', 'aside', 'header', 'footer' ],

      'item' => fn ( $cell, $parts ) => [
        [ 'line', 3.5, 105,   8.5,  105,   $mark ],
        [ 'line', 3.5, 210,   8.5,  210,   $mark ],
        [ 'line', 3.5, 148.5, 10.5, 148.5, $mark ],
        [ 'rect', 20, 45, 85, 45, [ 'stroke' => '#9cc0e4', 'width' => 0.6, 'dash' => TRUE, 'screen' => TRUE ] ],
        ( $parts ['header'] ?? '' ) !== '' ? [ 'box', 25, 10, 165, 30, $parts ['header'], [ 'size' => 10, 'align' => 'right' ] ] : NULL,
        $sender !== '' ? [ 'box', 20, 45, 85, 17.7, $sender, [ 'pad' => 0.5, 'size' => 7, 'valign' => 'bottom', 'class' => 'pp-return' ] ] : NULL,
        [ 'box', 20, 62.7, 85, 27.3, $parts ['address'] ?? '', [ 'pad' => 0.5, 'size' => 10 ] ],
        ( $parts ['aside'] ?? '' ) !== '' ? [ 'box', 125, 50, 75, 40, $parts ['aside'], [ 'size' => 8.5 ] ] : NULL,
        [ 'box', 25, 98.46, 165, 168, $title . ( $parts [''] ?? '' ), [ 'size' => $size ] ],
        ( $parts ['footer'] ?? '' ) !== '' ? [ 'box', 25, 272, 165, 17, $parts ['footer'], [ 'size' => 7.5, 'valign' => 'bottom', 'color' => '#555555' ] ] : NULL ],

    ] );

  }

  // A text option of lines - sender='PAD, Main Street 1, 3511 AB Utrecht' - as its lines,
  // escaped, a <br> between them.

  function padPrintableLines ( $option ) {

    $text = padPrintableText ( $option, '' );

    return implode ( '<br>', array_map ( fn ( $line ) => padWidgetAttr ( trim ( $line ) ), array_filter ( explode ( ',', $text ), fn ( $l ) => trim ( $l ) !== '' ) ) );

  }

  // ------------------------------------------------------------------------------------
  // The options.
  // ------------------------------------------------------------------------------------

  function padPrintableShow ( $value ) {

    return padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 );

  }

  // A number, decimals allowed - width=63.5 - held to its bounds: one that is none, or out of
  // them, is named under the strict check and takes the default.

  function padPrintableNumber ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $text  = is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';

    if ( preg_match ( '/^(\d+\.?\d*|\.\d+)$/D', $text ) and (float) $text >= $min and (float) $text <= $max )
      return $text + 0;

    if ( $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . " has no $option '" . padPrintableShow ( $value ) . "' - a number from $min to $max" );

    return $default;

  }

  function padPrintableInt ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $text  = is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';

    if ( preg_match ( '/^\d+$/D', $text ) and (int) $text >= $min and (int) $text <= $max )
      return (int) $text;

    if ( $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . " has no $option '" . padPrintableShow ( $value ) . "' - a whole number from $min to $max" );

    return $default;

  }

  // One or two numbers between spaces - margin='12.9 7.25' - as a pair; one number is both.
  // NULL without the option.

  function padPrintablePair ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, NULL );

    if ( $value === NULL )
      return $default;

    $parts = preg_split ( '/[\s,]+/', is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '' );

    if ( count ( $parts ) == 1 )
      $parts [] = $parts [0];

    if ( count ( $parts ) == 2 and count ( array_filter ( $parts, fn ( $p ) => preg_match ( '/^(\d+\.?\d*|\.\d+)$/D', $p ) and (float) $p >= $min and (float) $p <= $max ) ) == 2 )
      return [ $parts [0] + 0, $parts [1] + 0 ];

    if ( $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . " has no $option '" . padPrintableShow ( $value ) . "' - one or two numbers from $min to $max, in mm" );

    return $default;

  }

  function padPrintableChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_scalar ( $value ) ? strtolower ( trim ( padParmText ( (string) $value ) ) ) : '';

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . " has no $option '" . padMakeSafe ( $value, 20 ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  function padPrintableFlag ( $option ) {

    $value = padTagParm ( $option, FALSE );

    return ( $value !== FALSE and $value !== '' and $value !== 0 and $value !== '0' );

  }

  // A text option, read as the characters it holds.

  function padPrintableText ( $option, $default ) {

    $value = padTagParm ( $option, $default );

    return is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : $default;

  }

  // A colour, #rgb or #rrggbb - what the screen and the PDF both paint.

  function padPrintableColor ( $option, $default ) {

    global $padCheckSyntax;

    $value = strtolower ( padPrintableText ( $option, $default ) );

    if ( preg_match ( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/D', $value ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( 'the ' . padPrintableWhat () . " has no $option '" . padMakeSafe ( $value, 20 ) . "' - a colour written #rgb or #rrggbb" );

    return $default;

  }

  // ------------------------------------------------------------------------------------
  // The style and the script.
  // ------------------------------------------------------------------------------------

  // The style every printable shares - assets/printable.css: the desk, the sheets, the boxes,
  // and how a page with printables prints - and the kind's own, assets/<kind>.css, once per
  // page each.

  function padPrintableStyle ( $kind ) {

    $css = padWidgetStyle ( 'printable', [ 'desk' => [ '#8f8e88', '#3a3a37' ], 'line' => [ '#cfcec8', '#4a4a46' ],
                                            'face' => [ '#ffffff', '#242422' ], 'text' => [ '#1f1f1d', '#ecebe6' ] ],
                            padPrintableAsset ( 'printable.css' ) );

    if ( padWidgetOnce ( "style:printable-$kind" ) and padFileExists ( PAD . "subs/printables/assets/$kind.css" ) )
      $css .= padProtect ( '<style' . padStyleNonce () . '>' . padPrintableAsset ( "$kind.css" ) . '</style>' );

    return $css;

  }

  function padPrintableScript () {

    return padWidgetScript ( 'printable', padPrintableAsset ( 'printable.js' ) );

  }

  // A file of assets/, as the widgets read theirs: a style sheet a rule a line sent as one line.

  function padPrintableAsset ( $file ) {

    $text = (string) padFileGet ( PAD . "subs/printables/assets/$file" );

    return str_ends_with ( $file, '.css' ) ? preg_replace ( '/\R[ \t]*/', '', trim ( $text ) ) : rtrim ( $text, "\r\n" );

  }

?>
