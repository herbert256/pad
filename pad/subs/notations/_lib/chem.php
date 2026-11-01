<?php

  // Chemistry as MathML - the chem notation, {chem}. A formula or an equation typed the way
  // chemists type it, and the way mhchem's \ce{...} reads it, without any backslash, drawn by
  // the browser from MathML Core: no script, the same bytes for the same text.
  //
  //   {chem 'H2SO4'/}   {chem '[Cu(NH3)4]^2+'/}   {chem '^{227}_{90}Th'/}
  //   {notation:chem 'CH4 + 2O2 -> CO2 + 2H2O'/}
  //   {chem}CaCO3 ->[Delta] CaO + CO2 ^{/chem}
  //
  // padChemMl  the <math> element of a formula or an equation, with the problems it met
  //
  // What it reads:
  //
  //   elements    H, He, Fe ... - the 118 of the periodic table, and D and T, deuterium and
  //               tritium - upright, <mi mathvariant="normal">; checked against the table,
  //               which holds the letters for a part that is no one element too: A, E, L, M,
  //               R, X, Me, Et, Bu, Ph; a prime after one, R'
  //   counts      digits after an element or a group, subscripts: H2SO4, Ca(OH)2, Fe2(SO4)3;
  //               _2 and _{0.95} written out; n, m, x or y after a group, (CH2)n
  //   groups      ( ) and [ ] - <mo> brackets around an <mrow> - and { }, a group not drawn
  //   charges     Fe^3+, Fe^{3+}, SO4^2-, Cu^2+, a superscript of the last element or group,
  //               stacked with its count as one msubsup; a trailing + or - is a charge of one,
  //               NH4+, Cl-, and Fe3+ is Fe with 3 below and + above, as mhchem reads it;
  //               the minus is U+2212; letters after the caret are an oxidation state, Fe^{II}
  //   bonds       - between two parts of a formula a single bond, = a double, # a triple
  //   coefficients 2H2O, 3 O2, 1/2 O2 - a fraction is an <mfrac>, as mhchem writes it
  //   states      (s), (l), (g), (aq) straight after a species, on the baseline, upright
  //   adducts     CuSO4*5H2O, CuSO4.5H2O, CuSO4 * 5 H2O - a centred dot U+00B7
  //   nuclides    ^14C, ^{14}C, ^{227}_{90}Th, ^235_92U, _92^235U - the mass number (and the
  //               atomic number) before the element, <mmultiscripts> with <mprescripts/>
  //   particles   e- and e^- (the electron), n (the neutron) and p (the proton), which mhchem
  //               has no need of but a nuclear equation has: ^235_92U + n -> ^141_56Ba + ...;
  //               hv or hν is a photon, hν; Delta or Δ heat, Δ
  //   operators   + and = standing apart, as a - and a * (the adduct dot) do; the arrows ->
  //               <- <-> (resonance) <--> (⇄) <=> (an equilibrium, ⇌) and <=>> <<=> (written
  //               ⇌ too: Unicode has no arrow for an equilibrium that lies to one side), with a
  //               text above and below, ->[heat], ->[H2O], ->[above][below], ->[][below] - a
  //               formula when the text reads as one, else the text as it stands - over which
  //               the arrow stretches; an arrow needs no spaces around it, A->B
  //   marks       ^ standing apart after a species is a gas given off, ↑; v a precipitate, ↓;
  //               a caret ending a species, CO2^, is the gas mark too
  //
  // Spaces separate the items of an equation, several spaces are one; a word of small letters
  // alone (heat, light) is text. What it cannot read it names in $problems, a phrase that
  // completes "the {chem} ..." - has no element 'Xy', has a ( that never closes - and draws
  // as it stands: the element as written, the group without the bracket it lacks.

  const PAD_CHEM_ELEMENTS = 'H He Li Be B C N O F Ne Na Mg Al Si P S Cl Ar K Ca Sc Ti V Cr Mn Fe Co Ni Cu Zn Ga Ge As '
    . 'Se Br Kr Rb Sr Y Zr Nb Mo Tc Ru Rh Pd Ag Cd In Sn Sb Te I Xe Cs Ba La Ce Pr Nd Pm Sm Eu Gd Tb Dy Ho Er Tm Yb '
    . 'Lu Hf Ta W Re Os Ir Pt Au Hg Tl Pb Bi Po At Rn Fr Ra Ac Th Pa U Np Pu Am Cm Bk Cf Es Fm Md No Lr Rf Db Sg Bh '
    . 'Hs Mt Ds Rg Cn Nh Fl Mc Lv Ts Og D T';

  // The letters chemists write for a part that is not one element - a species A, an element E,
  // a ligand L, a metal M, a residue R, a halogen X - and the groups Me, Et, Bu and Ph: read
  // as elements are, so A -> B and R-OH are no problem.

  const PAD_CHEM_GENERIC = 'A E L M R X Me Et Bu Ph';

  // The arrows, the longest first so <=>> is not read as <=> and a >.

  const PAD_CHEM_ARROWS = [
    '<-->' => "\u{21C4}",
    '<=>>' => "\u{21CC}",
    '<<=>' => "\u{21CC}",
    '<=>'  => "\u{21CC}",
    '<->'  => "\u{2194}",
    '->'   => "\u{2192}",
    '<-'   => "\u{2190}" ];

  const PAD_CHEM_STATES = [ 's', 'l', 'g', 'aq' ];

  const PAD_CHEM_CLOSE = [ '(' => ')', '[' => ']', '{' => '}' ];

  const PAD_CHEM_DEEPEST = 20;

  // The <math> element of a text: the items of the equation one after the other.

  function padChemMl ( string $text, bool $block = FALSE, string $alt = '', array &$problems = [] ) : string {

    if ( trim ( $text ) === '' )
      return '';

    $c = padChemChars ( $text );
    $n = count ( $c );
    $i = 0;

    $inner = '';

    while ( $i < $n ) {

      if ( padChemSpace ( $c [$i] ) ) {
        $i++;
        continue;
      }

      $arrow = padChemArrowAt ( $c, $i );

      if ( $arrow !== NULL ) {
        $i += strlen ( $arrow );
        $above  = padChemArrowText ( $c, $i, $problems );
        $below  = padChemArrowText ( $c, $i, $problems );
        $inner .= padChemArrow ( PAD_CHEM_ARROWS [$arrow], $above, $below );
        continue;
      }

      $start = $i;

      while ( $i < $n and ! padChemSpace ( $c [$i] ) and padChemArrowAt ( $c, $i ) === NULL )
        $i++;

      $inner .= padChemWord ( array_slice ( $c, $start, $i - $start ), $problems );

    }

    if ( $alt === '' )
      $alt = trim ( preg_replace ( '/\s+/u', ' ', $text ) ?? $text );

    return '<math class="pad-chem"' . ( $block ? ' display="block"' : '' ) . ' alttext="' . padChemEscape ( $alt ) . '">'
         . $inner . '</math>';

  }

  // The characters of a text; a text that is no UTF-8 is taken byte by byte.

  function padChemChars ( $text ) {

    $chars = preg_split ( '//u', $text, -1, PREG_SPLIT_NO_EMPTY );

    return ( $chars === FALSE ) ? str_split ( $text ) : $chars;

  }

  // What separates the items of an equation: white space, the no-break space included.

  function padChemSpace ( $char ) {

    return $char === ' ' or $char === "\t" or $char === "\n" or $char === "\r" or $char === "\f" or $char === "\u{00A0}";

  }

  // The arrow that starts at $i, or NULL.

  function padChemArrowAt ( $c, $i ) {

    foreach ( PAD_CHEM_ARROWS as $arrow => $glyph )
      if ( implode ( '', array_slice ( $c, $i, strlen ( $arrow ) ) ) === $arrow )
        return $arrow;

    return NULL;

  }

  // The text in [ ] straight after an arrow, or NULL when no [ follows. Brackets inside it
  // pair up, [Pt] included; one that never closes takes the rest of the text.

  function padChemArrowText ( $c, &$i, &$problems ) {

    if ( ( $c [$i] ?? '' ) !== '[' )
      return NULL;

    $depth = 0;
    $text  = '';
    $n     = count ( $c );

    for ( $i++; $i < $n; $i++ ) {
      if ( $c [$i] === '[' )
        $depth++;
      elseif ( $c [$i] === ']' and $depth-- == 0 ) {
        $i++;
        return trim ( $text );
      }
      $text .= $c [$i];
    }

    $problems [] = 'has a [ that never closes';

    return trim ( $text );

  }

  // An arrow with its texts: above it alone an mover, below it an munder, both an munderover,
  // the arrow stretched over the text with a little room on either side.

  function padChemArrow ( $glyph, $above, $below ) {

    $above = ( $above === NULL or $above === '' ) ? '' : padChemArrowLabel ( $above );
    $below = ( $below === NULL or $below === '' ) ? '' : padChemArrowLabel ( $below );

    if ( $above === '' and $below === '' )
      return "<mo>$glyph</mo>";

    $mo = "<mo stretchy=\"true\">$glyph</mo>";

    if ( $below === '' )
      return "<mover>$mo$above</mover>";

    if ( $above === '' )
      return "<munder>$mo$below</munder>";

    return "<munderover>$mo$below$above</munderover>";

  }

  // The text of an arrow: Δ for heat, hν for light, a formula when it reads as one without a
  // problem (H2O, Pt, [Pt]), else the text as it stands (heat, catalyst, 25 °C).

  function padChemArrowLabel ( $text ) {

    $pad   = '<mspace width="0.2em"/>';
    $local = [];

    $markup = padChemSpecial ( $text );

    if ( $markup === NULL and ! preg_match ( '/\s/u', $text ) ) {
      $markup = padChemFormula ( padChemChars ( $text ), $local );
      if ( $local )
        $markup = NULL;
    }

    if ( $markup === NULL )
      $markup = '<mtext>' . padChemEscape ( $text ) . '</mtext>';

    return "<mrow>$pad$markup$pad</mrow>";

  }

  // The words that are no formula: Δ (heat) and hν (a photon).

  function padChemSpecial ( $word ) {

    if ( $word === 'Delta' or $word === "\u{0394}" )
      return padChemMi ( "\u{0394}" );

    if ( $word === 'hv' or $word === "h\u{03BD}" )
      return "<mrow><mi>h</mi><mi>\u{03BD}</mi></mrow>";

    return NULL;

  }

  // A word of an equation: an operator, a mark, a state, a coefficient written apart, a text,
  // or a species.

  function padChemWord ( $c, &$problems ) {

    $word = implode ( '', $c );

    switch ( $word ) {
      case '+':      return '<mo>+</mo>';
      case '=':      return '<mo>=</mo>';
      case '-':      return "<mo>\u{2212}</mo>";
      case '^':      return "<mo stretchy=\"false\">\u{2191}</mo>";
      case 'v':      return "<mo stretchy=\"false\">\u{2193}</mo>";
      case '*':
      case '.':
      case "\u{00B7}": return "<mo>\u{00B7}</mo>";
    }

    $special = padChemSpecial ( $word );

    if ( $special !== NULL )
      return $special;

    if ( preg_match ( '/^\(([a-z]+)\)$/D', $word, $m ) )
      return padChemState ( $m [1], $problems );

    if ( preg_match ( '/^[a-z]{2,}$/D', $word ) )
      return '<mtext>' . padChemEscape ( $word ) . '</mtext>';

    return padChemFormula ( $c, $problems );

  }

  // A state: (s), (l), (g) or (aq), on the baseline. Another word of small letters in brackets
  // is named and drawn the same way.

  function padChemState ( $state, &$problems ) {

    if ( ! in_array ( $state, PAD_CHEM_STATES, TRUE ) )
      $problems [] = "has no state '($state)' - a state is (s), (l), (g) or (aq)";

    return '<mrow>' . padChemFence ( '(' ) . padChemMi ( $state ) . padChemFence ( ')' ) . '</mrow>';

  }

  // A formula - a species with its coefficient, or the parts of an adduct - read from the
  // characters of a word. Groups nested deeper than any formula nests them are no formula: the
  // reading recurses per group, and such a word is named and written as the text it is.

  function padChemFormula ( $c, &$problems ) {

    $depth = $deepest = 0;

    foreach ( $c as $char )
      if ( isset ( PAD_CHEM_CLOSE [$char] ) )
        $deepest = max ( $deepest, ++$depth );
      elseif ( in_array ( $char, PAD_CHEM_CLOSE, TRUE ) )
        $depth--;

    if ( $deepest > PAD_CHEM_DEEPEST ) {
      $problems [] = 'has brackets nested deeper than ' . PAD_CHEM_DEEPEST;
      return '<mtext>' . padChemEscape ( implode ( '', $c ) ) . '</mtext>';
    }

    $i = 0;

    return padChemSequence ( $c, $i, NULL, $problems );

  }

  // The parts of a formula up to the bracket that closes the group it is in ($open, NULL at
  // the top). The element or group read last is held as a unit - its base, count, charge and
  // prescripts - until the next one starts, as a count or a charge after it is still its own.

  function padChemSequence ( $c, &$i, $open, &$problems ) {

    $n     = count ( $c );
    $close = ( $open === NULL ) ? NULL : PAD_CHEM_CLOSE [$open];
    $out   = '';
    $unit  = NULL;
    $part  = TRUE;

    while ( $i < $n ) {

      $ch    = $c [$i];
      $next  = $c [ $i + 1 ] ?? '';
      $fresh = $part;
      $part  = FALSE;

      // The bracket that closes the group, or one that closes nothing.

      if ( $ch === $close ) {
        $i++;
        return $out . padChemUnit ( $unit );
      }

      if ( $ch === ')' or $ch === ']' or $ch === '}' ) {
        if ( $open !== NULL ) {
          $problems [] = "has a $open closed by $ch";
          $i++;
          return $out . padChemUnit ( $unit );
        }
        $problems [] = "has a $ch that was never opened";
        $out .= padChemUnit ( $unit ) . padChemMo ( $ch );
        $unit = NULL;
        $i++;
        continue;
      }

      // A state after a species, (aq).

      if ( $ch === '(' and preg_match ( '/^\(([a-z]+)\)/', implode ( '', array_slice ( $c, $i, 16 ) ), $m )
           and ( $unit !== NULL or $out !== '' ) ) {
        $out .= padChemUnit ( $unit ) . padChemState ( $m [1], $problems );
        $unit = NULL;
        $i   += strlen ( $m [0] );
        continue;
      }

      // A group: what is inside, between its brackets - ( ) and [ ] drawn, { } not.

      if ( $ch === '(' or $ch === '[' or $ch === '{' ) {
        $out .= padChemUnit ( $unit );
        $i++;
        $closed = padChemCloses ( $c, $i, $ch );
        $inner  = padChemSequence ( $c, $i, $ch, $problems );
        if ( $ch === '{' )
          $base = "<mrow>$inner</mrow>";
        else
          $base = '<mrow>' . padChemFence ( $ch ) . "<mrow>$inner</mrow>" . ( $closed ? padChemFence ( PAD_CHEM_CLOSE [$ch] ) : '' ) . '</mrow>';
        $unit = [ 'base' => $base, 'group' => TRUE ];
        continue;
      }

      // A nuclide: ^mass, _atomic or both, before an element or a particle.

      if ( ( $ch === '^' or $ch === '_' ) and $unit === NULL and ( $pre = padChemPrescripts ( $c, $i ) ) !== NULL ) {
        list ( $sub, $sup, $i ) = $pre;
        $unit = padChemAtom ( $c, $i, $problems );
        $unit ['pre'] = [ $sub, $sup ];
        continue;
      }

      // A number with nothing before it is a coefficient: 2H2O, 1/2O2, 0.5O2.

      if ( $unit === NULL and padChemDigit ( $ch ) ) {
        $out .= padChemCoefficient ( $c, $i );
        $part = $fresh;
        continue;
      }

      // A coefficient written as a letter, nH2O.

      if ( $fresh and in_array ( $ch, [ 'n', 'm', 'x', 'y' ], TRUE ) and ( padChemUpper ( $next ) or $next === '(' or $next === '[' ) ) {
        $out .= '<mi>' . $ch . '</mi>';
        $part = TRUE;
        $i++;
        continue;
      }

      // A count of a group written as a letter, (CH2)n.

      if ( ! empty ( $unit ['group'] ) and in_array ( $ch, [ 'n', 'm', 'x', 'y' ], TRUE ) and ! padChemLower ( $next ) ) {
        $unit ['count'] = '<mi>' . $ch . '</mi>';
        $i++;
        continue;
      }

      // An element or a particle.

      if ( padChemLetter ( $ch ) or $ch === "\u{03BD}" ) {
        $out .= padChemUnit ( $unit );
        $unit = padChemAtom ( $c, $i, $problems );
        continue;
      }

      // A count: digits after an element or a group.

      if ( padChemDigit ( $ch ) ) {
        $count = '';
        while ( $i < $n and padChemDigit ( $c [$i] ) )
          $count .= $c [$i++];
        $unit ['count'] = padChemMn ( $count );
        continue;
      }

      // A count written out, _2 or _{0.95}.

      if ( $ch === '_' and $unit !== NULL ) {
        $i++;
        $unit ['count'] = padChemScript ( padChemScriptText ( $c, $i, $problems ) );
        continue;
      }

      // A charge or an oxidation state after a caret, ^2+, ^{3+}, ^{II}; a caret at the end
      // of the word with nothing to raise is the mark of a gas.

      if ( $ch === '^' ) {
        $i++;
        $text = padChemScriptText ( $c, $i, $problems );
        if ( $text === '' and $i >= $n ) {
          $out .= padChemUnit ( $unit ) . "<mo stretchy=\"false\">\u{2191}</mo>";
          $unit = NULL;
        } elseif ( $text === '' )
          $problems [] = 'has a ^ that raises nothing';
        elseif ( $unit === NULL ) {
          $problems [] = "has a ^ with nothing before it to raise";
          $out .= '<msup><mrow></mrow>' . padChemScript ( $text ) . '</msup>';
        } else
          $unit ['charge'] = padChemScript ( $text );
        continue;
      }

      // A bond between two parts, or a charge written without a caret: NH4+, Cl-, Fe3+. A
      // minus is a bond when an element or a group follows it, CH3-CH3, else a charge.

      if ( $ch === '-' and ( padChemUpper ( $next ) or ( ( $next === '(' or $next === '[' ) and ! padChemStateAt ( $c, $i + 1 ) ) ) ) {
        $out .= padChemUnit ( $unit ) . padChemMo ( '-', TRUE );
        $unit = NULL;
        $i++;
        continue;
      }

      if ( $ch === '+' or $ch === '-' ) {
        $sign = '';
        while ( $i < $n and ( $c [$i] === '+' or $c [$i] === '-' ) )
          $sign .= $c [$i++];
        while ( $i < $n and padChemDigit ( $c [$i] ) )
          $sign .= $c [$i++];
        if ( $unit === NULL ) {
          $problems [] = "has a charge $sign with nothing before it";
          $out .= padChemScript ( $sign );
        } else
          $unit ['charge'] = padChemScript ( $sign );
        continue;
      }

      // A prime, R'.

      if ( $ch === "'" ) {
        $out .= padChemUnit ( $unit ) . "<mo lspace=\"0\" rspace=\"0\">\u{2032}</mo>";
        $unit = NULL;
        $i++;
        continue;
      }

      if ( $ch === '=' or $ch === '#' ) {
        $out .= padChemUnit ( $unit ) . padChemMo ( $ch === '=' ? '=' : "\u{2261}", TRUE );
        $unit = NULL;
        $i++;
        continue;
      }

      // The dot of an adduct, after which a coefficient may come again.

      if ( $ch === '*' or $ch === '.' or $ch === "\u{00B7}" ) {
        $out .= padChemUnit ( $unit ) . "<mo>\u{00B7}</mo>";
        $unit = NULL;
        $part = TRUE;
        $i++;
        continue;
      }

      $problems [] = "has a '" . $ch . "' where a formula goes";
      $out .= padChemUnit ( $unit ) . padChemMo ( $ch );
      $unit = NULL;
      $i++;

    }

    if ( $open !== NULL )
      $problems [] = "has a $open that never closes";

    return $out . padChemUnit ( $unit );

  }

  // Whether the group opened at $i (the character after the bracket) is closed by its own
  // bracket, for the closing bracket to be drawn: a group that never closes is drawn open.

  function padChemCloses ( $c, $i, $open ) {

    $depth = 0;
    $n     = count ( $c );

    for ( ; $i < $n; $i++ ) {
      if ( isset ( PAD_CHEM_CLOSE [ $c [$i] ] ) )
        $depth++;
      elseif ( in_array ( $c [$i], PAD_CHEM_CLOSE, TRUE ) ) {
        if ( $depth == 0 )
          return $c [$i] === PAD_CHEM_CLOSE [$open];
        $depth--;
      }
    }

    return FALSE;

  }

  // Whether a state, (aq), starts at $i.

  function padChemStateAt ( $c, $i ) {

    return (bool) preg_match ( '/^\([a-z]+\)/', implode ( '', array_slice ( $c, $i, 16 ) ) );

  }

  // A digit, a capital, a small letter, a letter - of the ASCII alphabet only, whatever the
  // locale says a letter is.

  function padChemDigit ( $char ) {

    return strlen ( $char ) == 1 and $char >= '0' and $char <= '9';

  }

  function padChemUpper ( $char ) {

    return strlen ( $char ) == 1 and $char >= 'A' and $char <= 'Z';

  }

  function padChemLower ( $char ) {

    return strlen ( $char ) == 1 and $char >= 'a' and $char <= 'z';

  }

  function padChemLetter ( $char ) {

    return padChemUpper ( $char ) or padChemLower ( $char );

  }

  // An element - a capital and a small letter when there is one, checked against the table -
  // or a particle: e, n and p, h ν. A symbol that is no element is named and written as it
  // stands.

  function padChemAtom ( $c, &$i, &$problems ) {

    static $elements = NULL;

    $elements ??= array_flip ( explode ( ' ', PAD_CHEM_ELEMENTS . ' ' . PAD_CHEM_GENERIC ) );

    $ch = $c [$i];

    if ( padChemUpper ( $ch ) ) {
      $symbol = $ch;
      $i++;
      if ( isset ( $c [$i] ) and padChemLower ( $c [$i] ) )
        $symbol .= $c [$i++];
      if ( ! isset ( $elements [$symbol] ) )
        $problems [] = "has no element '$symbol' - an element is written H, He, Fe ...";
      return [ 'base' => padChemMi ( $symbol ) ];
    }

    $word = '';

    while ( isset ( $c [$i] ) and ( padChemLower ( $c [$i] ) or $c [$i] === "\u{03BD}" ) )
      $word .= $c [$i++];

    if ( in_array ( $word, [ 'e', 'n', 'p' ], TRUE ) )
      return [ 'base' => padChemMi ( $word ) ];

    if ( $word === 'hv' or $word === "h\u{03BD}" )
      return [ 'base' => padChemSpecial ( 'hv' ) ];

    $problems [] = "has no element '$word' - an element is written H, He, Fe ...";

    return [ 'base' => padChemMi ( $word ) ];

  }

  // The prescripts of a nuclide at $i: ^mass, _atomic, or both in either order, followed by
  // an element or a particle - [ atomic, mass, the place after them ], or NULL when the caret
  // or underscore is something else (a charge, a count).

  function padChemPrescripts ( $c, $i ) {

    $scripts = [ '^' => NULL, '_' => NULL ];

    while ( isset ( $c [$i] ) and ( $c [$i] === '^' or $c [$i] === '_' ) and $scripts [ $c [$i] ] === NULL ) {
      $mark = $c [$i];
      $i++;
      if ( ( $c [$i] ?? '' ) === '{' ) {
        $text = '';
        for ( $i++; isset ( $c [$i] ) and $c [$i] !== '}'; $i++ )
          $text .= $c [$i];
        if ( ! isset ( $c [$i] ) )
          return NULL;
        $i++;
      } else {
        $text = '';
        if ( ( $c [$i] ?? '' ) === '-' or ( $c [$i] ?? '' ) === '+' )
          $text .= $c [$i++];
        while ( isset ( $c [$i] ) and padChemDigit ( $c [$i] ) )
          $text .= $c [$i++];
      }
      if ( ! preg_match ( '/^[+-]?\d+$/D', $text ) )
        return NULL;
      $scripts [$mark] = $text;
    }

    $ch = $c [$i] ?? '';

    if ( ! ( padChemUpper ( $ch ) or ( in_array ( $ch, [ 'e', 'n', 'p' ], TRUE ) and ! padChemLower ( $c [ $i + 1 ] ?? '' ) ) ) )
      return NULL;

    return [ $scripts ['_'] === NULL ? NULL : padChemScript ( $scripts ['_'] ),
             $scripts ['^'] === NULL ? NULL : padChemScript ( $scripts ['^'] ), $i ];

  }

  // The text of a script after ^ or _: what stands in { } (a { that never closes takes the
  // rest of the word), or else digits, then signs, or letters - 2+, 3-, +, II, 0.95.

  function padChemScriptText ( $c, &$i, &$problems ) {

    $n    = count ( $c );
    $text = '';

    if ( ( $c [$i] ?? '' ) === '{' ) {
      for ( $i++; $i < $n and $c [$i] !== '}'; $i++ )
        $text .= $c [$i];
      if ( $i < $n )
        $i++;
      else
        $problems [] = 'has a { that never closes';
      return $text;
    }

    while ( $i < $n and ( padChemDigit ( $c [$i] ) or ( $c [$i] === '.' and $text !== '' and padChemDigit ( $c [ $i + 1 ] ?? '' ) ) ) )
      $text .= $c [$i++];

    while ( $i < $n and ( $c [$i] === '+' or $c [$i] === '-' ) )
      $text .= $c [$i++];

    if ( $text === '' )
      while ( $i < $n and padChemLetter ( $c [$i] ) )
        $text .= $c [$i++];

    return $text;

  }

  // A coefficient: a whole number, a decimal or a fraction, 1/2 an <mfrac>.

  function padChemCoefficient ( $c, &$i ) {

    $n      = count ( $c );
    $number = '';

    while ( $i < $n and padChemDigit ( $c [$i] ) )
      $number .= $c [$i++];

    if ( ( $c [$i] ?? '' ) === '/' and padChemDigit ( $c [ $i + 1 ] ?? '' ) ) {
      $i++;
      $denominator = '';
      while ( $i < $n and padChemDigit ( $c [$i] ) )
        $denominator .= $c [$i++];
      return '<mfrac>' . padChemMn ( $number ) . padChemMn ( $denominator ) . '</mfrac>';
    }

    if ( ( $c [$i] ?? '' ) === '.' and padChemDigit ( $c [ $i + 1 ] ?? '' ) ) {
      $number .= $c [$i++];
      while ( $i < $n and padChemDigit ( $c [$i] ) )
        $number .= $c [$i++];
    }

    return padChemMn ( $number );

  }

  // The content of a script: numbers <mn>, signs <mo> without spacing (the minus U+2212),
  // letters upright - one element, or a row of them.

  function padChemScript ( $text ) {

    preg_match_all ( '/\d+(?:\.\d+)?|[A-Za-z]+|./su', $text, $m );

    $parts = [];

    foreach ( $m [0] as $token )
      if ( padChemDigit ( $token [0] ) )
        $parts [] = padChemMn ( $token );
      elseif ( preg_match ( '/^[A-Za-z]+$/D', $token ) )
        $parts [] = padChemMi ( $token );
      else
        $parts [] = padChemMo ( $token, TRUE );

    if ( count ( $parts ) == 1 )
      return $parts [0];

    return '<mrow>' . implode ( '', $parts ) . '</mrow>';

  }

  // The markup of a unit: the base with its count and charge - msub, msup, or one msubsup -
  // and for a nuclide one mmultiscripts with the mass and atomic number before it (an empty
  // <mrow> where a script is missing, as MathML Core has no <none>).

  function padChemUnit ( $unit ) {

    if ( $unit === NULL )
      return '';

    $base   = $unit ['base'];
    $count  = $unit ['count']  ?? NULL;
    $charge = $unit ['charge'] ?? NULL;

    if ( isset ( $unit ['pre'] ) ) {
      list ( $sub, $sup ) = $unit ['pre'];
      $empty = '<mrow></mrow>';
      $post  = ( $count !== NULL or $charge !== NULL ) ? ( $count ?? $empty ) . ( $charge ?? $empty ) : '';
      return "<mmultiscripts>$base$post<mprescripts/>" . ( $sub ?? $empty ) . ( $sup ?? $empty ) . '</mmultiscripts>';
    }

    if ( $count !== NULL and $charge !== NULL )
      return "<msubsup>$base$count$charge</msubsup>";

    if ( $count !== NULL )
      return "<msub>$base$count</msub>";

    if ( $charge !== NULL )
      return "<msup>$base$charge</msup>";

    return $base;

  }

  // An element, a particle or a state, upright.

  function padChemMi ( $text ) {

    return '<mi mathvariant="normal">' . padChemEscape ( $text ) . '</mi>';

  }

  // A coefficient or a count.

  function padChemMn ( $text ) {

    return '<mn>' . padChemEscape ( $text ) . '</mn>';

  }

  // An operator; a bond or a sign of a charge without the space an operator gets around it.

  function padChemMo ( $text, $tight = FALSE ) {

    $text = ( $text === '-' ) ? "\u{2212}" : $text;

    return '<mo' . ( $tight ? ' lspace="0" rspace="0"' : '' ) . '>' . padChemEscape ( $text ) . '</mo>';

  }

  // A bracket of a group or a state: the height of the letters, not stretched to a count or a
  // charge beside it, and no space of its own - Ca(OH)2, NaCl(s), as mhchem writes them.

  function padChemFence ( $bracket ) {

    return '<mo stretchy="false" lspace="0" rspace="0">' . padChemEscape ( $bracket ) . '</mo>';

  }

  // A text made safe for an element or an attribute.

  function padChemEscape ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
