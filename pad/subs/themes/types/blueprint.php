<?php

  // blueprint - white lines on the deep blue of a cyanotype drawing: a faint grid on the page,
  // a fine one every 20 pixels and a stronger one every 100 as on drawing paper, the headings
  // lettered in capitals as a title block is, the links the yellow of an engineer's marking
  // pencil - for technical pages, demos and diagrams. The grid is light enough that the dim
  // text still reads at 4.71 to 1 where two of its lines cross.

  return [ 'name'   => 'Blueprint',
           'scheme' => 'dark',
           'colors' => [ 'bg' => '#133f6e', 'panel' => '#174878', 'text' => '#f4f8fd', 'soft' => '#d8e6f5', 'dim' => '#b4cde8', 'line' => '#4f7cab',
                         'accent' => '#ffd866', 'accent-text' => '#0d2f52', 'accent-soft' => '#0d3459', 'head' => '#1d5288', 'code' => '#0e3560', 'side' => '#0d3157',
                         'side-text' => '#dce9f7', 'side-dim' => '#a8c6e6', 'side-hover' => '#184a7a', 'side-line' => '#285785', 'side-strong' => '#ffffff', 'menu' => '#133f6e',
                         'ok' => '#9be8b9', 'ok-bg' => '#124a4c', 'warn' => '#ffd866', 'warn-bg' => '#3d4532', 'bad' => '#ffbcb2', 'bad-bg' => '#57304d' ],
           'hero'   => '#174878',
           'body'   => 'background-image:linear-gradient(rgba(255,255,255,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.08) 1px,transparent 1px),'
                     . 'linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);'
                     . 'background-size:100px 100px,100px 100px,20px 20px,20px 20px',
           'css'    => ':root:root:root .frame-body h2,:root:root:root .frame-body h3,:root:root:root .frame-top h1{text-transform:uppercase;letter-spacing:.06em}' ];

?>
