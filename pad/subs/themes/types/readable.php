<?php

  // readable - a page set for readers with dyslexia, after the British Dyslexia Association's
  // Dyslexia Style Guide: dark text on cream rather than black on white, a plain, evenly spaced
  // sans-serif (Atkinson Hyperlegible where it is installed, else the guide's Verdana, Tahoma or
  // Trebuchet), 16 pixels, the lines 1.6 apart, the letters .06em and the words .16em further
  // apart - within what WCAG 1.4.12 asks a page to bear - paragraphs no wider than 70
  // characters, bold instead of italics, no block capitals in the side's headings, and the
  // messages in Okabe and Ito's blue, orange and vermilion, darkened to AA: no green and no
  // red, which the guide asks to avoid for colour-blind readers.

  return [ 'name'   => 'Readable',
           'scheme' => 'light',
           'colors' => [ 'bg' => '#f7f3e8', 'panel' => '#fcfaf3', 'text' => '#24221f', 'soft' => '#3d3a35', 'dim' => '#57534b', 'line' => '#ddd6c6',
                         'accent' => '#1f4f99', 'accent-text' => '#fcfaf3', 'accent-soft' => '#e2e6ee', 'head' => '#ece6d6', 'code' => '#eee9dc', 'side' => '#f1ecdf',
                         'side-text' => '#2e2b26', 'side-dim' => '#5a554c', 'side-hover' => '#e6dfcf', 'side-line' => '#ddd6c6', 'side-strong' => '#1a1814', 'menu' => '#fcfaf3',
                         'ok' => '#005a8c', 'ok-bg' => '#deebf3', 'warn' => '#7a5000', 'warn-bg' => '#f6e8c8', 'bad' => '#a43c00', 'bad-bg' => '#f8e3d6' ],
           'sans'   => '"Atkinson Hyperlegible", Verdana, Tahoma, "Trebuchet MS", Arial, sans-serif',
           'hero'   => 'linear-gradient(180deg, #efe8d6, #f7f3e8)',
           'body'   => 'font-size:16px;line-height:1.6;letter-spacing:.06em;word-spacing:.16em',
           'css'    => ':root:root:root pre,:root:root:root code,:root:root:root kbd,:root:root:root samp,:root:root:root svg{letter-spacing:normal;word-spacing:normal}'
                     . ':root:root:root em,:root:root:root i{font-style:normal;font-weight:700}'
                     . ':root:root:root .frame-body p{max-width:70ch}:root:root:root .frame-head{text-transform:none}' ];

?>
