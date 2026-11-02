<?php

  // The notations are the files of pad/subs/notations/types/, as the engine reads them (padKinds).

  $tagKinds   = padKinds ( 'notations' );

  $tagAbout   = 'A text written in a notation, drawn on the server - ' . count ( $tagKinds ) . ' of them, from formulas in AsciiMath and chemical equations to a tune in ABC, guitar chords and tabs, braille, morse, signal flags, chess, go and draughts positions, Karnaugh maps, bridge deals, juggling patterns and cube moves.';
  $tagGroup   = 'graphics';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{notation:kind 'text', ...options/}
{notation:kind ...options} text {/notation:kind}
{notation:kind 'text'/}
{notation:math 'a/b'/}  {notation:math display='block'} x^2 + y^2 = r^2 {/notation:math}
{notation:chem 'CH4 + 2O2 -> CO2 + 2H2O'/}
{notation:abc} X:1 ... K:G ... {/notation:abc}
{notation:chord 'Am', instrument='ukulele'/}
{notation:piano 'C4 E4 G4'/}
{notation:braille 'Hello'/}  {notation:morse 'SOS'/}  {notation:semaphore 'PAD'/}  {notation:signals 'PAD'/}
{notation:chess 'fen', flip, highlight='e2, e4', arrow='e2-e4'/}
{notation:go '(;SZ[9]AB[cc];W[gg])', numbers/}
{notation:draughts 'W:W31-50:B1-20', board=8/}
{notation:tablature tuning='dropd'} e|--0--3--| ... {/notation:tablature}
{notation:karnaugh 'm(1, 3, 5) + d(7)', form='pos'/}  {notation:karnaugh}F = A'B + AC{/notation:karnaugh}
{notation:bridge 'N:AKQ2.J9.T87.654 - - -', dealer='N', vulnerable='ns', points/}
{notation:siteswap '531'/}  {notation:siteswap '(4,2x)(2x,4)'/}
{notation:cube "R U R' U'", view='cube', case, layers=3/}
PAD;

  $tagParms   = [
    'kind after colon' => 'The notation, a file of <code>pad/subs/notations/types/</code>: '
            . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", $tagKinds ) )
            . '. The notation: prefix selects it - <code>{notation:math}</code>; <code>{chart:chord}</code> is a chart and <code>{notation:chord}</code> a guitar chord.',
    'text' => 'What is written in the notation - the formula, the tune, the word, the position. Without it the content between the tags is the text, taken as it stands before the level walks it, so the braces of AsciiMath and the brackets of SGF need no ignore.' ];

  $tagOptions = [
    'display'    => 'math, chem: <code>inline</code> (the default) or <code>block</code> - the formula on a line of its own, centred.',
    'alttext'    => 'math, chem: the text a screen reader without MathML says - the formula as written unless given.',
    'title'      => 'abc, chord, piano, semaphore, signals, chess, go, draughts, tablature, karnaugh, bridge, siteswap, cube: the accessible name.',
    'size'       => 'The width in pixels - chord (120), semaphore a figure (64), signals a flag (40), chess, go and draughts the board (360), cube (240) - or the height of the piano (120).',
    'view'       => 'cube: <code>net</code> (the default), the six faces unfolded, or <code>cube</code>, the up, front and right faces seen from above.',
    'case'       => 'Bare option. cube: the cube the moves solve - the moves undone from solved - as an algorithm\'s case is drawn.',
    'layers'     => 'cube: the layers of the cube, 2 to 7 - 3 unless given.',
    'width'      => 'abc: the lines spread over that many pixels; without it each line takes its natural width.',
    'instrument' => 'chord: <code>guitar</code> (the default) or <code>ukulele</code>.',
    'frets'      => 'chord: a shape of your own for the chord named - <code>x32010</code>, <code>8 10 10 9 8 8</code> past the ninth fret.',
    'from'       => 'piano: the lowest key shown, <code>C3</code>; <code>to</code> the highest.',
    'to'         => 'piano: the highest key shown, <code>B5</code>.',
    'cells'      => 'braille: the cells drawn as raised dots instead of characters; <code>cells=12</code> breaks the lines at 12 cells.',
    'plain'      => 'Bare option. morse: the code alone, without the signal drawn; go: no coordinates.',
    'numbers'    => 'Bare option. go: the move number on every stone a move put there; draughts: the number of every playing square; karnaugh: the number of every cell.',
    'move'       => 'go: the position after that move.',
    'flip'       => 'Bare option. chess, draughts: the board turned, Black at the bottom.',
    'highlight'  => 'chess: squares to mark, <code>\'e2, e4\'</code>; draughts: <code>\'32, 28\'</code>.',
    'arrow'      => 'chess: arrows from square to square, <code>\'e2-e4, g1-f3\'</code>.',
    'board'      => 'draughts: <code>10</code> (international, the default) or <code>8</code> (English draughts and checkers).',
    'tuning'     => 'tablature: the tuning of the strings - <code>standard</code>, <code>dropd</code>, <code>dadgad</code>, <code>openg</code>, <code>opend</code>, <code>seven</code>, <code>bass</code>, <code>bass5</code>, <code>ukulele</code>, <code>mandolin</code>, <code>banjo</code> - or their notes from the lowest, <code>\'D A D G B E\'</code>; without it the names before the lines, else the usual tuning of so many strings.',
    'vars'       => 'karnaugh: the variables in order, <code>\'A, B, C\'</code> - a letter, digits after it; without it those of the expression, or A B C D.',
    'dealer'     => 'bridge: the seat that dealt, <code>n</code>, <code>e</code>, <code>s</code> or <code>w</code>, ringed on the compass.',
    'vulnerable' => 'bridge: <code>none</code> (the default), <code>ns</code>, <code>ew</code> or <code>all</code> - as PBN writes it, <code>love</code>, <code>-</code> and <code>both</code> as well - the vulnerable sides red on the compass.',
    'points'     => 'Bare option. bridge: the high-card points of every hand by its seat.',
    'form'       => 'karnaugh: <code>sop</code> (the default), the 1s grouped for a minimal sum of products, or <code>pos</code>, the 0s for a minimal product of sums.' ];

  $tagSee     = [ 'diagram:auto', 'chart:bar', 'game:memory' ];

?>
