<?php

  $tagAbout   = 'Writes a picture of the application\'s www directory as a thumbnail of the size the page shows it at, resized once with GD.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{img 'photos/harbour.jpg', width=400, height=300, alt='text'}
{img 'photos/dunes.jpg', width=300, height=300, fit='contain', format='webp', quality=70, alt=''}
PAD;

  $tagParms   = [
    'file' => 'The picture: a name below <code>www/&lt;application&gt;/</code> - a JPEG, PNG, GIF or WebP.' ];

  $tagOptions = [
    'width'   => 'The width of the box in CSS pixels. With only one of width and height the picture keeps its proportion; with neither it keeps its size.',
    'height'  => 'The height of the box in CSS pixels.',
    'fit'     => '<code>cover</code> (default: fill the box, cut around the centre) or <code>contain</code> (all of the picture inside the box).',
    'format'  => '<code>jpeg</code>, <code>png</code> or <code>webp</code> - default the source\'s, a GIF becoming a PNG.',
    'quality' => '1 to 100 for JPEG and WebP, default 80.',
    'alt'     => 'Required: the text for whoever cannot see the picture. <code>alt=\'\'</code> marks it as decoration.' ];

  $tagSee     = [ 'placeholder', 'asset', 'video' ];

?>
