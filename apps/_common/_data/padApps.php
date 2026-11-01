<?php

  // The PAD applications, grouped as the pull-down of the brand shows them - every
  // application's top-left corner (_tags/padBrand) - and the name the brand and the frame
  // (_tags/padFrame) give each: per application its directory under apps/, the name, an
  // icon (a file of pad/subs/icons/types/) and a line about it. An application that is not here
  // is named by its directory.

  return [
    'PAD' => [
      'pad'             => [ 'home',          'home',         'What PAD is, Hello World' ],
      'manual'          => [ 'manual',        'help',         'The manual, a page per subject' ],
      'tags'            => [ 'tags',          'tag',          'Every tag by its prefix, with examples' ],
      'types'           => [ 'types',         'layers',       'Every type of tag - the prefixes' ],
      'reference'       => [ 'reference',     'list',         'Tags, functions and options, cross-referenced' ],
      'sequence'        => [ 'sequence',      'bar-chart',    'The sequence subsystem' ],
    ],
    'Showcases' => [
      'charts'          => [ 'charts',        'chart-pie',    'Every chart type, drawn on the server' ],
      'showcase'        => [ 'showcase',      'star',         'The tags that draw, format and interact' ],
      'games'           => [ 'games',         'trophy',       'Games played in the browser' ],
      'toys'            => [ 'toys',          'sparkle',      'Live simulations to play with' ],
      'inputs'          => [ 'inputs',        'toggle-on',    'Rich form fields to try' ],
    ],
    'Front ends' => [
      'htmx'            => [ 'htmx',          'refresh',      'PAD + htmx, HTML over the wire' ],
      'alpine'          => [ 'alpine',        'mountain',     'PAD + Alpine, state from PHP' ],
      'react'           => [ 'react',         'monitor',      'PAD + React, components on islands' ],
      'islands'         => [ 'islands',       'map',          'Islands of any framework, built by Vite' ],
      'webcomponents'   => [ 'webcomponents', 'package',      'Custom elements with their shadow root' ],
    ],
    'Offline' => [
      'kanban'          => [ 'kanban',        'columns',      'A task board on SQLite' ],
      'ledger'          => [ 'ledger',        'wallet',       'A household budget on SQLite' ],
      'notes'           => [ 'notes',         'file-text',    'A Markdown notebook on SQLite' ],
    ],
    'Examples' => [
      'demo'            => [ 'demo',          'play',         'Guestbook, todo, contact, counter, clock' ],
      'hello'           => [ 'hello',         'message',      'Hello World, the page pairing' ],
      'playground'      => [ 'playground',    'keyboard',     'Type a template, see the result' ],
      'live'            => [ 'live',          'zap',          'Type a page, run it on this server' ],
      'examples'        => [ 'examples',      'code',         'The examples of the manual and the suites' ],
      'classicModels'   => [ 'classicModels', 'database',     'PAD Select over the Classic Models database' ],
      'padSelect'       => [ 'padSelect',     'filter',       'PAD Select, every option with its SQL' ],
      'structure'       => [ 'structure',     'folder',       'The _xxx directories at every level' ],
    ],
    'Tools' => [
      'admin'           => [ 'admin',         'settings',     'The console of this installation' ],
      'edit'            => [ 'edit',          'edit',         'The editor of the applications' ],
      'develop'         => [ 'develop',       'terminal',     'Builds and tools for PAD itself' ],
      'apps'            => [ 'apps',          'grid',         'Every application of this installation' ],
      'regression/main' => [ 'regression',    'check-circle', 'The regression suites' ],
    ],
  ];

?>
