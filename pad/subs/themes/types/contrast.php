<?php

  // contrast - black on white, a dark blue for the links: every text of the frame at 7 to 1 or
  // more, WCAG AAA, the links underlined and the focus ringed thick, so colour is never all that
  // tells them apart.

  return [ 'name'   => 'Contrast',
           'scheme' => 'light',
           'needs'  => 7,
           'colors' => [ 'bg' => '#ffffff', 'panel' => '#ffffff', 'text' => '#000000', 'soft' => '#1a1a1a', 'dim' => '#3b3b3b', 'line' => '#595959',
                         'accent' => '#0000b8', 'accent-text' => '#ffffff', 'accent-soft' => '#e8e8ff', 'head' => '#f0f0f0', 'code' => '#f0f0f0', 'side' => '#ffffff',
                         'side-text' => '#000000', 'side-dim' => '#3b3b3b', 'side-hover' => '#e8e8e8', 'side-line' => '#000000', 'side-strong' => '#000000', 'menu' => '#ffffff',
                         'ok' => '#0b5a1c', 'ok-bg' => '#e6f4e9', 'warn' => '#6b3d00', 'warn-bg' => '#fff3d6', 'bad' => '#9a0000', 'bad-bg' => '#fde8e8' ],
           'hero'   => '#ffffff',
           'css'    => ':root:root:root a{text-decoration:underline}:root:root:root :focus-visible{outline:3px solid #0000b8;outline-offset:2px}:root:root:root .pad-section{--pad-section-line:#595959}' ];

?>
