<?php

  // The printables are the files of pad/subs/printables/types/, as the engine reads them (padPrintableKinds).

  $tagAbout   = 'Pages made for paper - ' . count ( padPrintableKinds () ) . ' kinds, from sheets of labels and name badges to a letter for a window envelope - the content between the tags one item, repeated over the data and laid out on sheets that print as they show, and come out of the PDF writer alike.';
  $tagGroup   = 'pictures';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{printable 'name', data='rows', ...} ... {/printable}
{printable:labels data='products', sheet='a4-3x8'}<b>{$name}</b>{/printable:labels}
{printable:tickets data='seats', pdf}<h2>{$show}</h2><aside>{qr $code/}</aside>{/printable:tickets}
{printable:letter data='customers', sender='PAD, Utrecht'}<address>{$name}</address><p>...</p>{/printable:letter}
PAD;

  $tagParms   = [
    'name' => 'The kind, a file of <code>pad/subs/printables/types/</code>: '
            . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padPrintableKinds () ) )
            . '. The printable: type names it as well - <code>{printable:labels}...{/printable:labels}</code>; it is never reached bare.' ];

  $tagOptions = [
    'sheet'     => 'The grid of a sheet, named by its paper and its grid: <code>a4-3x8</code>, <code>a4-2x7</code>, <code>letter-3x10</code> ... - each kind of a grid has its own.',
    'paper'     => 'The paper: a4, a5, a6, letter, legal ...; an envelope dl, c4, c5 or c6.',
    'across'    => 'The items across a sheet; <code>down</code> the items down.',
    'width'     => 'The width of an item in mm; <code>height</code> its height.',
    'margin'    => 'The top and the left margin in mm, one number for both - the grid centred without it.',
    'pitch'     => 'The step from an item to the next in mm, across and down.',
    'skip'      => 'The items of the first sheet left empty - a sheet of labels used before.',
    'copies'    => 'Every item that many times.',
    'padding'   => 'The room round an item\'s text in mm; <code>size</code> its text size in points, <code>align</code> left, center or right.',
    'outline'   => 'Bare option: the edges of the labels printed as well.',
    'scale'     => 'The preview on the screen made smaller, 0.1 to 3 - paper and PDF keep the size.',
    'pdf'       => 'Bare option: links to the sheets as a PDF - the page asked with <code>&amp;padPdf</code> answers them; <code>file=</code> its name.',
    'title'     => 'badges: the event on the band; certificate: its title; invoice: the heading of the body.',
    'sender'    => 'envelope, letter, invoice: the return address, its lines between commas.',
    'color'     => 'badges: the band; certificate: the frame and the title.',
    'signed'    => 'certificate: the names under the lines to sign on, between semicolons, a role after a comma.',
    'number'    => 'tickets: the number of the first ticket, on a stub without an aside.',
    'card'      => 'indexcards: 5x3, 6x4, a6 or a7.',
    'grid'      => 'graphpaper: squares, dots or lines.',
    'spacing'   => 'graphpaper: the step of the grid in mm, 5 (8 for lines).',
    'major'     => 'graphpaper: every that many lines a heavier one, or a bigger dot - millimetre paper is <code>spacing=1, major=10</code>.',
    'year'      => 'calendar: its twelve months, this year the default.',
    'month'     => 'calendar: the month it starts with, <code>2026-09</code>; <code>months</code> how many, 1 to 60.',
    'sunday'    => 'calendar: bare option, the weeks from Sunday instead of Monday; <code>weeks</code> their ISO 8601 numbers before them.',
    'holidays'  => 'calendar: the public holidays of a country of the holidays subsystem marked, <code>nl</code>, <code>de-by</code>.',
    'folds'     => 'foldcard: bare option, the folds printed as light lines, where the screen alone shows them.',
    'rastral'   => 'staffpaper: the size of a staff as engravers number them, 0 (9.2 mm) to 8 (3.7 mm), 3 (7.0 mm) the default.',
    'staves'    => 'staffpaper: how many staves; <code>lines</code> the lines of a staff, 5, 6 for tablature; <code>grand</code> pairs them for the piano.' ];

  $tagSee     = [ 'pdf', 'barcode', 'qr', 'fake' ];

?>
