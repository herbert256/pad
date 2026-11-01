<?php

  // The symbologies are the files of pad/subs/codes/types/, as the engine reads them (padKinds).

  $tagAbout   = 'Draws a barcode as inline SVG, encoded on the server - ' . count ( padKinds ( 'codes' ) ) . ' symbologies, from EAN-13 and Code 128 to QR and Data Matrix.';
  $tagGroup   = 'graphics';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{barcode 'number'/}
{barcode $code, type='code128', height=60, scale=2, title='text', plain, color='#000', background='#fff'/}
{barcode:ean13 'number'/}
{code39 'TEXT', check/}
{barcode $text, type='datamatrix', size=160/}
{gs1128 '(01)09501101530003(10)LOT1'/}
PAD;

  $tagParms   = [
    'code' => 'The number or text. An EAN or UPC number may leave its check digit off - it is added; one given is checked.' ];

  $tagOptions = [
    'type'       => 'The symbology, a file of <code>pad/subs/codes/types/</code>: '
                      . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padKinds ( 'codes' ) ) )
                      . '. Left out, twelve or thirteen digits are an EAN-13 and anything else Code 128. The barcode type names it as well - <code>{barcode:upce \'0425261\'/}</code>, or bare, <code>{itf14 \'1540014128876\'/}</code>, when nothing else has the name.',
    'height'     => 'The height of the bars in pixels, default 60.',
    'scale'      => 'The width of one module - the thinnest bar - in pixels, default 2 (1 to 10).',
    'title'      => 'The accessible name, by default the code.',
    'plain'      => 'Bare option: the bars only, no digits under them.',
    'color'      => 'The colour of the bars, default <code>#000</code>.',
    'background' => 'The colour behind them, default <code>#fff</code>.',
    'addon'      => '<code>ean13</code>, <code>upca</code>, <code>upce</code> and <code>bookland</code>: the 2 or 5 digits of an add-on, a small symbol right of the bars - the issue of a magazine, the price of a book.',
    'check'      => 'Bare option, <code>code39</code>: the check symbol of modulo 43; <code>itf</code>: the check digit of weights 3 and 1. For <code>msi</code> it names the check digits: <code>mod10</code> (default), <code>mod1010</code> or <code>none</code>.',
    'gs1'        => 'Bare option, <code>datamatrix</code>: GS1 DataMatrix - the text GS1 element strings, <code>(01)09501101530003(17)261231(10)AB-123</code>, checked as <code>gs1128</code> checks them, FNC1 first and as the separator.',
    'size'       => '<code>qr</code>, <code>datamatrix</code> and <code>aztec</code>: the width and height in pixels, default 160 - they take no height, scale or plain.',
    'ecc'        => '<code>aztec</code>: the error correction in percent of the codewords, 3 more always added - 23 (the standard\'s recommendation) unless given, 5 to 90.',
    'level'      => '<code>qr</code>: the error correction, <code>L</code>, <code>M</code> (default), <code>Q</code> or <code>H</code>. <code>pdf417</code>: 0 to 8, by default the standard\'s recommendation for the size of the data.',
    'columns'    => '<code>pdf417</code>: the data columns, 1 to 30 - by default those that make the symbol about three times as wide as high. Its rows are three modules high, <code>scale=</code> the width of a module.' ];

  $tagSee     = [ 'qr' ];

?>
