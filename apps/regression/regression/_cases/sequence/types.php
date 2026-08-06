<?php

  // The first eight terms of every deterministic sequence type.
  //
  // These values were checked against independent definitions - exact integer arithmetic,
  // GMP, and published listings - not taken on trust from the engine. Five types were wrong
  // when that check was first made (catalan, lucky, moserdebruijn, emirp and gould), and the
  // precomputed tables agreed with the wrong code, so agreement between the two proves only
  // that they match, never that they are right. That is what these values are for.
  //
  // random and chance are left out: they answer differently every run by design. The types
  // taking a parameter are given one, since without it they describe nothing.

  return [

    [ 'add',             <<<'PAD'
      {sequence add=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '4,5,6,7,8,9,10,11,' ],
    [ 'and',             <<<'PAD'
      {sequence and=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '1,2,3,0,1,2,3,0,' ],
    [ 'antiprime',       <<<'PAD'
      {sequence antiprime, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '1,2,4,6,12,24,36,48,' ],
    [ 'bell',            <<<'PAD'
      {sequence bell, rows=8}
        {$sequence},
      {/sequence}
      PAD,              '1,2,5,15,52,203,877,4140,' ],
    [ 'biquadratic',     <<<'PAD'
      {sequence biquadratic, rows=8}
        {$sequence},
      {/sequence}
      PAD,       '1,16,81,256,625,1296,2401,4096,' ],
    [ 'catalan',         <<<'PAD'
      {sequence catalan, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '1,2,5,14,42,132,429,1430,' ],
    [ 'caterer',         <<<'PAD'
      {sequence caterer, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '2,4,7,11,16,22,29,37,' ],
    [ 'ceil',            <<<'PAD'
      {sequence ceil=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '3,3,3,6,6,6,9,9,' ],
    [ 'composite',       <<<'PAD'
      {sequence composite, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '4,6,8,9,10,12,14,15,' ],
    [ 'cubic',           <<<'PAD'
      {sequence cubic, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '1,8,27,64,125,216,343,512,' ],
    [ 'cullen',          <<<'PAD'
      {sequence cullen, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '3,9,25,65,161,385,897,2049,' ],
    [ 'decagonal',       <<<'PAD'
      {sequence decagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '1,10,27,52,85,126,175,232,' ],
    [ 'divide',          <<<'PAD'
      {sequence divide=4, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '0.25,0.5,0.75,1,1.25,1.5,1.75,2,' ],
    [ 'emirp',           <<<'PAD'
      {sequence emirp, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '13,17,31,37,71,73,79,97,' ],
    [ 'enneadecagonal',  <<<'PAD'
      {sequence enneadecagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,    '1,19,54,106,175,261,364,484,' ],
    [ 'eval',            <<<'PAD'
      {sequence eval='@ * 2', rows=8}
        {$sequence},
      {/sequence}
      PAD,    '2,4,6,8,10,12,14,16,' ],
    [ 'even',            <<<'PAD'
      {sequence even, rows=8}
        {$sequence},
      {/sequence}
      PAD,              '2,4,6,8,10,12,14,16,' ],
    [ 'exponentiation',  <<<'PAD'
      {sequence exponentiation=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,  '1,8,27,64,125,216,343,512,' ],
    [ 'fibonacci',       <<<'PAD'
      {sequence fibonacci, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '0,1,1,2,3,5,8,13,' ],
    [ 'floor',           <<<'PAD'
      {sequence floor=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '0,0,3,3,3,6,6,6,' ],
    [ 'gnomonic',        <<<'PAD'
      {sequence gnomonic, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '1,3,5,7,9,11,13,15,' ],
    [ 'golomb',          <<<'PAD'
      {sequence golomb, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '1,2,2,3,3,4,4,4,' ],
    [ 'gould',           <<<'PAD'
      {sequence gould, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '1,2,2,4,2,4,4,8,' ],
    [ 'happy',           <<<'PAD'
      {sequence happy, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '1,7,10,13,19,23,28,31,' ],
    [ 'harshad',         <<<'PAD'
      {sequence harshad, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '1,2,3,4,5,6,7,8,' ],
    [ 'heptadecagonal',  <<<'PAD'
      {sequence heptadecagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,    '1,17,48,94,155,231,322,428,' ],
    [ 'heptagonal',      <<<'PAD'
      {sequence heptagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '1,7,18,34,55,81,112,148,' ],
    [ 'hexagonal',       <<<'PAD'
      {sequence hexagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '1,6,15,28,45,66,91,120,' ],
    [ 'icosihenagonal',  <<<'PAD'
      {sequence icosihenagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,    '1,21,60,118,195,291,406,540,' ],
    [ 'identity',        <<<'PAD'
      {sequence identity, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '1,2,3,4,5,6,7,8,' ],
    [ 'kaprekar',        <<<'PAD'
      {sequence kaprekar, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '1,9,45,55,99,297,703,999,' ],
    [ 'kolakoski',       <<<'PAD'
      {sequence kolakoski, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '1,2,2,1,1,2,1,2,' ],
    [ 'kynea',           <<<'PAD'
      {sequence kynea, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '7,23,79,287,1087,4223,16639,66047,' ],
    [ 'list',            <<<'PAD'
      {sequence list='1;8;5', rows=8}
        {$sequence},
      {/sequence}
      PAD,    '1,8,5,' ],
    [ 'loop',            <<<'PAD'
      {sequence loop, rows=8}
        {$sequence},
      {/sequence}
      PAD,              '1,2,3,4,5,6,7,8,' ],
    [ 'lucas',           <<<'PAD'
      {sequence lucas, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '1,3,4,7,11,18,29,47,' ],
    [ 'lucky',           <<<'PAD'
      {sequence lucky, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '1,3,7,9,13,15,21,25,' ],
    [ 'mersenne',        <<<'PAD'
      {sequence mersenne, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '3,7,31,127,8191,131071,524287,2147483647,' ],
    [ 'modulo',          <<<'PAD'
      {sequence modulo=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '1,2,0,1,2,0,1,2,' ],
    [ 'moserdebruijn',   <<<'PAD'
      {sequence moserdebruijn, rows=8}
        {$sequence},
      {/sequence}
      PAD,     '1,4,5,16,17,20,21,64,' ],
    [ 'multiple',        <<<'PAD'
      {sequence multiple=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '3,6,9,12,15,18,21,24,' ],
    [ 'multiply',        <<<'PAD'
      {sequence multiply=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '3,6,9,12,15,18,21,24,' ],
    [ 'nand',            <<<'PAD'
      {sequence nand=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '-2,-3,-4,-1,-2,-3,-4,-1,' ],
    [ 'negation',        <<<'PAD'
      {sequence negation, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '-1,-2,-3,-4,-5,-6,-7,-8,' ],
    [ 'newmanConway',    <<<'PAD'
      {sequence newmanConway, rows=8}
        {$sequence},
      {/sequence}
      PAD,      '1,1,2,2,3,4,4,4,' ],
    [ 'nor',             <<<'PAD'
      {sequence nor=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '-4,-4,-4,-8,-8,-8,-8,-12,' ],
    [ 'not',             <<<'PAD'
      {sequence not, rows=8}
        {$sequence},
      {/sequence}
      PAD,               '-2,-3,-4,-5,-6,-7,-8,-9,' ],
    [ 'octagonal',       <<<'PAD'
      {sequence octagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '1,8,21,40,65,96,133,176,' ],
    [ 'octahedral',      <<<'PAD'
      {sequence octahedral, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '1,6,19,44,85,146,231,344,' ],
    [ 'odd',             <<<'PAD'
      {sequence odd, rows=8}
        {$sequence},
      {/sequence}
      PAD,               '1,3,5,7,9,11,13,15,' ],
    [ 'oeis',            <<<'PAD'
      {sequence oeis=45, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '0,1,1,2,3,5,8,13,' ],
    [ 'or',              <<<'PAD'
      {sequence or=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,              '3,3,3,7,7,7,7,11,' ],
    [ 'palindrome',      <<<'PAD'
      {sequence palindrome, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '1,2,3,4,5,6,7,8,' ],
    [ 'pell',            <<<'PAD'
      {sequence pell, rows=8}
        {$sequence},
      {/sequence}
      PAD,              '1,2,5,12,29,70,169,408,' ],
    [ 'pentagonal',      <<<'PAD'
      {sequence pentagonal, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '1,5,12,22,35,51,70,92,' ],
    [ 'perfect',         <<<'PAD'
      {sequence perfect, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '6,28,496,8128,33550336,8589869056,137438691328,2305843008139952128,' ],
    [ 'perrin',          <<<'PAD'
      {sequence perrin, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '3,0,2,3,2,5,5,7,' ],
    [ 'polite',          <<<'PAD'
      {sequence polite, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '3,5,6,7,9,10,11,12,' ],
    [ 'power',           <<<'PAD'
      {sequence power=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '3,9,27,81,243,729,2187,6561,' ],
    [ 'powerful',        <<<'PAD'
      {sequence powerful, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '1,4,8,9,16,25,27,32,' ],
    [ 'prime',           <<<'PAD'
      {sequence prime, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '2,3,5,7,11,13,17,19,' ],
    [ 'pronic',          <<<'PAD'
      {sequence pronic, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '2,6,12,20,30,42,56,72,' ],
    [ 'range',           <<<'PAD'
      {sequence range='1..8', rows=8}
        {$sequence},
      {/sequence}
      PAD,    '1,2,3,4,5,6,7,8,' ],
    [ 'recaman',         <<<'PAD'
      {sequence recaman, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '0,1,3,6,2,7,13,20,' ],
    [ 'repeat',          <<<'PAD'
      {sequence repeat=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '3,3,3,3,3,3,3,3,' ],
    [ 'round',           <<<'PAD'
      {sequence round=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,           '0,3,3,3,6,6,6,9,' ],
    [ 'semiprime',       <<<'PAD'
      {sequence semiprime, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '4,6,9,10,14,15,21,22,' ],
    [ 'square',          <<<'PAD'
      {sequence square, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '1,4,9,16,25,36,49,64,' ],
    [ 'step',            <<<'PAD'
      {sequence step=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '1,4,7,10,13,16,19,22,' ],
    [ 'strong',          <<<'PAD'
      {sequence strong, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '1,2,145,' ],
    [ 'subtract',        <<<'PAD'
      {sequence subtract=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '-2,-1,0,1,2,3,4,5,' ],
    [ 'sylvester',       <<<'PAD'
      {sequence sylvester, rows=8}
        {$sequence},
      {/sequence}
      PAD,         '2,3,7,43,1807,3263443,10650056950807,' ],
    [ 'tetrahedral',     <<<'PAD'
      {sequence tetrahedral, rows=8}
        {$sequence},
      {/sequence}
      PAD,       '1,4,10,20,35,56,84,120,' ],
    [ 'triangular',      <<<'PAD'
      {sequence triangular, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '1,3,6,10,15,21,28,36,' ],
    [ 'tribonacci',      <<<'PAD'
      {sequence tribonacci, rows=8}
        {$sequence},
      {/sequence}
      PAD,        '0,0,1,1,2,4,7,13,' ],
    [ 'xnor',            <<<'PAD'
      {sequence xnor=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,            '-3,-2,-1,-8,-7,-6,-5,-12,' ],
    [ 'xor',             <<<'PAD'
      {sequence xor=3, rows=8}
        {$sequence},
      {/sequence}
      PAD,             '2,1,0,7,6,5,4,11,' ],
    [ 'xpadovan',        <<<'PAD'
      {sequence xpadovan, rows=8}
        {$sequence},
      {/sequence}
      PAD,          '1,1,1,2,2,3,4,5,' ],

  ];

?>