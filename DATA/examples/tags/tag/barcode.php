<?php

  $tagAbout   = 'Draws a barcode as inline SVG - EAN-13, EAN-8, UPC-A or Code 128, encoded on the server.';
  $tagGroup   = 'graphics';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{barcode 'number'}
{barcode $code, type='code128', height=60, scale=2, title='text', plain, color='#000', background='#fff'}
PAD;

  $tagParms   = [
    'code' => 'The number or text. An EAN or UPC number may leave its check digit off - it is added; one given is checked.' ];

  $tagOptions = [
    'type'       => '<code>ean13</code>, <code>ean8</code>, <code>upca</code> or <code>code128</code>. Left out, twelve or thirteen digits are an EAN-13 and anything else Code 128.',
    'height'     => 'The height of the bars in pixels, default 60.',
    'scale'      => 'The width of one module - the thinnest bar - in pixels, default 2 (1 to 10).',
    'title'      => 'The accessible name, by default the code.',
    'plain'      => 'Bare option: the bars only, no digits under them.',
    'color'      => 'The colour of the bars, default <code>#000</code>.',
    'background' => 'The colour behind them, default <code>#fff</code>.' ];

  $tagSee     = [ 'qr' ];

?>
