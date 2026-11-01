<?php

  // contrast-dark - white on black, yellow for the links, as the high-contrast black scheme of
  // an operating system has them: the dark counterpart of contrast, every text of the frame at
  // 7 to 1 or more, WCAG AAA, the links underlined, the focus ringed thick in yellow and the
  // lines light enough to be seen.

  return [ 'name'   => 'Contrast Dark',
           'scheme' => 'dark',
           'needs'  => 7,
           'colors' => [ 'bg' => '#000000', 'panel' => '#0d0d0d', 'text' => '#ffffff', 'soft' => '#ebebeb', 'dim' => '#c7c7c7', 'line' => '#9e9e9e',
                         'accent' => '#ffe14d', 'accent-text' => '#000000', 'accent-soft' => '#2b2600', 'head' => '#1f1f1f', 'code' => '#171717', 'side' => '#000000',
                         'side-text' => '#ffffff', 'side-dim' => '#c7c7c7', 'side-hover' => '#262626', 'side-line' => '#ffffff', 'side-strong' => '#ffffff', 'menu' => '#0d0d0d',
                         'ok' => '#7cf29a', 'ok-bg' => '#00290f', 'warn' => '#ffd23f', 'warn-bg' => '#2e2300', 'bad' => '#ff9c9c', 'bad-bg' => '#380000' ],
           'hero'   => '#000000',
           'css'    => ':root:root:root a{text-decoration:underline}:root:root:root :focus-visible{outline:3px solid #ffe14d;outline-offset:2px}:root:root:root .pad-section{--pad-section-line:#9e9e9e}' ];

?>
