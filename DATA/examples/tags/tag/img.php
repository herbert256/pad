<?php

  $tagAbout   = 'Writes a picture of the application\'s www directory as a thumbnail of the size the page shows it at, resized once with GD.';
  $tagGroup   = 'pictures';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pad:img 'photos/harbour.jpg', width=400, height=300, alt='text'/}
{pad:img 'photos/dunes.jpg', width=300, height=300, fit='contain', format='webp', quality=70, alt=''/}
{pad:img 'photos/harbour.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='text'/}
{pad:img 'photos/harbour.jpg', width=400, filter='grayscale, dither', alt='text'/}
{pad:img 'photos/harbour.jpg', width=400, filter='blurhash', alt='text'/}
PAD;

  $tagParms   = [
    'file' => 'The picture: a name below <code>www/&lt;application&gt;/</code> - a JPEG, PNG, GIF or WebP.' ];

  $tagOptions = [
    'width'   => 'The width of the box in CSS pixels. With only one of width and height the picture keeps its proportion; with neither it keeps its size.',
    'height'  => 'The height of the box in CSS pixels.',
    'fit'     => '<code>cover</code> (default: fill the box, cut around the centre) or <code>contain</code> (all of the picture inside the box).',
    'format'  => '<code>jpeg</code>, <code>png</code> or <code>webp</code> - default the source\'s, a GIF becoming a PNG.',
    'quality' => '1 to 100 for JPEG and WebP, default 80.',
    'alt'     => 'Required: the text for whoever cannot see the picture. <code>alt=\'\'</code> marks it as decoration.',
    'filter'  => 'A comma list of the filters of <code>pad/subs/filters/types/</code>, applied in the order written: '
               . implode ( ', ', array_map ( fn ( $filter ) => "<code>$filter</code>", padKinds ( 'filters' ) ) )
               . '. halftone and ascii write the picture as SVG dots or text; blurhash, thumbhash, dominant and lqip are placeholders behind the picture while it loads.',
    'colors'     => 'duotone, dither, atkinson, bayer, halftone, threshold: two hex colours, the dark one first - <code>colors=\'#123, #fc6\'</code>; edges: the ground, then the lines.',
    'levels'     => 'posterize: the steps of a channel (4); dither, atkinson, bayer: the greys (2).',
    'cell'       => 'pixelate: the block in pixels (8); halftone: the distance of the dots (8).',
    'radius'     => 'blur: in CSS pixels (6).',
    'amount'     => 'sepia (100), sharpen (50), vignette (50) and edges (100), in percent.',
    'columns'    => 'ascii: the characters across (64).',
    'chars'      => 'ascii: the characters from light to dark (<code>\' .:-=+*#%@\'</code>).',
    'components' => 'blurhash: the terms across and down, <code>\'4,3\'</code>.',
    'threshold'  => 'threshold: the luminance where ink becomes paper, in percent of white (50); solarize: where a channel turns into its negative (50).',
    'cutoff'     => 'autocontrast: the pixels let go at each end of the tones before they are stretched, in percent (1).' ];

  $tagSee     = [ 'placeholder', 'asset', 'video' ];

  $subsystemKindLinks = appSubsystemLinks ( 'filter' );

?>
