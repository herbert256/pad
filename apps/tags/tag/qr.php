<?php

  $tagAbout   = 'Draws a QR code as inline SVG, encoded on the server - no image library, no remote service.';
  $tagGroup   = 'graphics';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{qr 'text'}
{qr $url, size=160, level='M', title='text', color='#000', background='#fff'}
PAD;

  $tagParms   = [
    'text' => 'The text to encode - an address, a payment or Wi-Fi string, anything up to 2,953 bytes at level L.' ];

  $tagOptions = [
    'size'       => 'Width and height in pixels, the quiet zone of four modules included; default 160, at least 21.',
    'level'      => 'Error correction: <code>L</code> (7% of the symbol may be lost), <code>M</code> (15%, default), <code>Q</code> (25%) or <code>H</code> (30%).',
    'title'      => 'The accessible name, by default the text itself.',
    'color'      => 'The dark colour, default <code>#000</code>.',
    'background' => 'The light colour, default <code>#fff</code>.' ];

  $tagSee     = [ 'barcode', 'chart' ];

?>
