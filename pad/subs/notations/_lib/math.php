<?php

  // AsciiMath as MathML - the math notation, {math}. A formula typed the way it is said,
  // x^2 + y^2 = r^2, sum_(i=1)^n i, (a+b)/(c+d), drawn by the browser from MathML Core: no
  // script and no font of PAD's own, the same bytes for the same text.
  //
  //   {math}x = (-b +- sqrt(b^2-4ac))/(2a){/math}
  //   {notation:math 'int_0^1 f(x) dx', block/}
  //
  // padMathMl  the <math> element of a formula
  //
  // The grammar is the one of ASCIIMathML.js by Peter Jipsen, the reference implementation
  // that asciimath.org runs - its version 2.5.2 (May 2026), which carries the 2.2 grammar the
  // notation was made with and already writes most of MathML Core (letters of the Unicode
  // mathematical alphabets for bb, cc, fr ..., mspace for quad). Its symbol table, AMgetSymbol,
  // AMparseSexpr, AMparseIexpr, AMparseExpr, AMremoveBrackets, detectMatrix and AMmapChars are
  // ported a function for a function, the state they share (the nesting depth, the kind of the
  // symbol read before) handed along in one array, so a formula is split, bracketed and
  // grouped the way asciimath.org does it - unmatched brackets included: the reference never
  // fails, it draws what it can, and so does this.
  //
  // Where the reference writes what MathML Core has not, this writes what Core draws: the
  // upper-case Greek letters are <mi mathvariant="normal"> (upright, where a lone letter in an
  // <mi> is slanted); the names of functions - sin, log, det, lim, min, dim - are one <mi> each
  // (a name of more than one letter is upright in an <mi>), where the reference's
  // <mo>sin</mo> got its spacing from an operator dictionary Core does not have, so the
  // spacing is written out: the invisible function application U+2061 between a name and its
  // argument, a thin space after the name when the argument has no brackets, and one before a
  // name that follows a letter or a number, 2 sin x; cancel is <mrow class="pad-math-cancel">,
  // which the notation's style strikes through, as menclose is not in Core; bold and italic
  // are the classes pad-math-bold and pad-math-italic in place of an inline style a strict
  // content security policy would drop; a table keeps the reference's columnalign and, when a
  // column has a line, its columnlines (Core ignores both) and carries the classes
  // pad-math-left and pad-math-line for the style to do the same; and a colour, a class or an
  // id given in the formula is only written when it is one - a CSS colour name or #hex, a
  // class or id made of letters, digits, - and _. Brackets nested fifty deep end the formula:
  // the rest is written as text.

  const PAD_MATH_CONST = 0, PAD_MATH_UNARY = 1, PAD_MATH_BINARY = 2, PAD_MATH_INFIX = 3,
        PAD_MATH_LEFTBRACKET = 4, PAD_MATH_RIGHTBRACKET = 5, PAD_MATH_SPACE = 6,
        PAD_MATH_UNDEROVER = 7, PAD_MATH_DEFINITION = 8, PAD_MATH_LEFTRIGHT = 9,
        PAD_MATH_TEXT = 10, PAD_MATH_UNARYUNDEROVER = 15;

  // The symbols, as the reference lists them: what is typed, the element it becomes, what that
  // element holds, its kind, the LaTeX name it is known by as well, and its particulars - an
  // accent (acc), a function name (func), a bracket that is not drawn (invisible), the bars a
  // function is written between (lr), the alphabet a font command maps to (codes), an upright
  // capital (normal) and the quote that starts a text (quote).

  const PAD_MATH_SYMBOLS = [

    // Greek letters - the capitals upright, as <mi mathvariant="normal">.

    [ 'alpha',      'mi', "\u{03B1}", PAD_MATH_CONST ],
    [ 'beta',       'mi', "\u{03B2}", PAD_MATH_CONST ],
    [ 'chi',        'mi', "\u{03C7}", PAD_MATH_CONST ],
    [ 'delta',      'mi', "\u{03B4}", PAD_MATH_CONST ],
    [ 'Delta',      'mi', "\u{0394}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'epsi',       'mi', "\u{03B5}", PAD_MATH_CONST, 'epsilon' ],
    [ 'varepsilon', 'mi', "\u{025B}", PAD_MATH_CONST ],
    [ 'eta',        'mi', "\u{03B7}", PAD_MATH_CONST ],
    [ 'gamma',      'mi', "\u{03B3}", PAD_MATH_CONST ],
    [ 'Gamma',      'mi', "\u{0393}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'iota',       'mi', "\u{03B9}", PAD_MATH_CONST ],
    [ 'kappa',      'mi', "\u{03BA}", PAD_MATH_CONST ],
    [ 'lambda',     'mi', "\u{03BB}", PAD_MATH_CONST ],
    [ 'Lambda',     'mi', "\u{039B}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'lamda',      'mi', "\u{03BB}", PAD_MATH_CONST ],
    [ 'Lamda',      'mi', "\u{039B}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'mu',         'mi', "\u{03BC}", PAD_MATH_CONST ],
    [ 'nu',         'mi', "\u{03BD}", PAD_MATH_CONST ],
    [ 'omega',      'mi', "\u{03C9}", PAD_MATH_CONST ],
    [ 'Omega',      'mi', "\u{03A9}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'phi',        'mi', "\u{03D5}", PAD_MATH_CONST ],
    [ 'varphi',     'mi', "\u{03C6}", PAD_MATH_CONST ],
    [ 'Phi',        'mi', "\u{03A6}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'pi',         'mi', "\u{03C0}", PAD_MATH_CONST ],
    [ 'Pi',         'mi', "\u{03A0}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'psi',        'mi', "\u{03C8}", PAD_MATH_CONST ],
    [ 'Psi',        'mi', "\u{03A8}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'rho',        'mi', "\u{03C1}", PAD_MATH_CONST ],
    [ 'sigma',      'mi', "\u{03C3}", PAD_MATH_CONST ],
    [ 'Sigma',      'mi', "\u{03A3}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'tau',        'mi', "\u{03C4}", PAD_MATH_CONST ],
    [ 'theta',      'mi', "\u{03B8}", PAD_MATH_CONST ],
    [ 'vartheta',   'mi', "\u{03D1}", PAD_MATH_CONST ],
    [ 'Theta',      'mi', "\u{0398}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'upsilon',    'mi', "\u{03C5}", PAD_MATH_CONST ],
    [ 'xi',         'mi', "\u{03BE}", PAD_MATH_CONST ],
    [ 'Xi',         'mi', "\u{039E}", PAD_MATH_CONST, NULL, [ 'normal' => TRUE ] ],
    [ 'zeta',       'mi', "\u{03B6}", PAD_MATH_CONST ],

    // Binary operations.

    [ '*',        'mo', "\u{22C5}", PAD_MATH_CONST, 'cdot' ],
    [ '**',       'mo', "\u{2217}", PAD_MATH_CONST, 'ast' ],
    [ '***',      'mo', "\u{22C6}", PAD_MATH_CONST, 'star' ],
    [ '//',       'mo', '/',        PAD_MATH_CONST ],
    [ '\\\\',     'mo', '\\',       PAD_MATH_CONST, 'backslash' ],
    [ 'setminus', 'mo', '\\',       PAD_MATH_CONST ],
    [ 'xx',       'mo', "\u{00D7}", PAD_MATH_CONST, 'times' ],
    [ '|><',      'mo', "\u{22C9}", PAD_MATH_CONST, 'ltimes' ],
    [ '><|',      'mo', "\u{22CA}", PAD_MATH_CONST, 'rtimes' ],
    [ '|><|',     'mo', "\u{22C8}", PAD_MATH_CONST, 'bowtie' ],
    [ '-:',       'mo', "\u{00F7}", PAD_MATH_CONST, 'div' ],
    [ 'divide',   'mo', '-:',       PAD_MATH_DEFINITION ],
    [ '@',        'mo', "\u{2218}", PAD_MATH_CONST, 'circ' ],
    [ 'o+',       'mo', "\u{2295}", PAD_MATH_CONST, 'oplus' ],
    [ 'o-',       'mo', "\u{2296}", PAD_MATH_CONST, 'ominus' ],
    [ 'ox',       'mo', "\u{2297}", PAD_MATH_CONST, 'otimes' ],
    [ 'o.',       'mo', "\u{2299}", PAD_MATH_CONST, 'odot' ],
    [ 'sum',      'mo', "\u{2211}", PAD_MATH_UNDEROVER ],
    [ 'prod',     'mo', "\u{220F}", PAD_MATH_UNDEROVER ],
    [ '^^',       'mo', "\u{2227}", PAD_MATH_CONST, 'wedge' ],
    [ '^^^',      'mo', "\u{22C0}", PAD_MATH_UNDEROVER, 'bigwedge' ],
    [ 'vv',       'mo', "\u{2228}", PAD_MATH_CONST, 'vee' ],
    [ 'vvv',      'mo', "\u{22C1}", PAD_MATH_UNDEROVER, 'bigvee' ],
    [ 'nn',       'mo', "\u{2229}", PAD_MATH_CONST, 'cap' ],
    [ 'nnn',      'mo', "\u{22C2}", PAD_MATH_UNDEROVER, 'bigcap' ],
    [ 'uu',       'mo', "\u{222A}", PAD_MATH_CONST, 'cup' ],
    [ 'uuu',      'mo', "\u{22C3}", PAD_MATH_UNDEROVER, 'bigcup' ],
    [ 'dag',      'mo', "\u{2020}", PAD_MATH_CONST, 'dagger' ],
    [ 'ddag',     'mo', "\u{2021}", PAD_MATH_CONST, 'ddagger' ],

    // Binary relations.

    [ '!=',          'mo', "\u{2260}", PAD_MATH_CONST, 'ne' ],
    [ ':=',          'mo', ':=',       PAD_MATH_CONST ],
    [ 'lt',          'mo', '<',        PAD_MATH_CONST ],
    [ '<=',          'mo', "\u{2264}", PAD_MATH_CONST, 'le' ],
    [ 'lt=',         'mo', "\u{2264}", PAD_MATH_CONST, 'leq' ],
    [ 'gt',          'mo', '>',        PAD_MATH_CONST ],
    [ 'mlt',         'mo', "\u{226A}", PAD_MATH_CONST, 'll' ],
    [ '>=',          'mo', "\u{2265}", PAD_MATH_CONST, 'ge' ],
    [ 'gt=',         'mo', "\u{2265}", PAD_MATH_CONST, 'geq' ],
    [ 'mgt',         'mo', "\u{226B}", PAD_MATH_CONST, 'gg' ],
    [ '-<',          'mo', "\u{227A}", PAD_MATH_CONST, 'prec' ],
    [ '-lt',         'mo', "\u{227A}", PAD_MATH_CONST ],
    [ '>-',          'mo', "\u{227B}", PAD_MATH_CONST, 'succ' ],
    [ '-<=',         'mo', "\u{2AAF}", PAD_MATH_CONST, 'preceq' ],
    [ '>-=',         'mo', "\u{2AB0}", PAD_MATH_CONST, 'succeq' ],
    [ 'in',          'mo', "\u{2208}", PAD_MATH_CONST ],
    [ '!in',         'mo', "\u{2209}", PAD_MATH_CONST, 'notin' ],
    [ 'sub',         'mo', "\u{2282}", PAD_MATH_CONST, 'subset' ],
    [ '!sub',        'mo', "\u{2284}", PAD_MATH_CONST, 'not\\subset' ],
    [ 'notsubset',   'mo', '!sub',     PAD_MATH_DEFINITION ],
    [ 'sup',         'mo', "\u{2283}", PAD_MATH_CONST, 'supset' ],
    [ '!sup',        'mo', "\u{2285}", PAD_MATH_CONST, 'not\\supset' ],
    [ 'notsupset',   'mo', '!sup',     PAD_MATH_DEFINITION ],
    [ 'sube',        'mo', "\u{2286}", PAD_MATH_CONST, 'subseteq' ],
    [ '!sube',       'mo', "\u{2288}", PAD_MATH_CONST, 'not\\subseteq' ],
    [ 'notsubseteq', 'mo', '!sube',    PAD_MATH_DEFINITION ],
    [ 'supe',        'mo', "\u{2287}", PAD_MATH_CONST, 'supseteq' ],
    [ '!supe',       'mo', "\u{2289}", PAD_MATH_CONST, 'not\\supseteq' ],
    [ 'notsupseteq', 'mo', '!supe',    PAD_MATH_DEFINITION ],
    [ '-=',          'mo', "\u{2261}", PAD_MATH_CONST, 'equiv' ],
    [ '!-=',         'mo', "\u{2262}", PAD_MATH_CONST, 'not\\equiv' ],
    [ 'notequiv',    'mo', '!-=',      PAD_MATH_DEFINITION ],
    [ '~=',          'mo', "\u{2245}", PAD_MATH_CONST, 'cong' ],
    [ '~~',          'mo', "\u{2248}", PAD_MATH_CONST, 'approx' ],
    [ '~',           'mo', "\u{223C}", PAD_MATH_CONST, 'sim' ],
    [ 'prop',        'mo', "\u{221D}", PAD_MATH_CONST, 'propto' ],

    // Logical symbols.

    [ 'and', 'mtext', 'and',      PAD_MATH_SPACE ],
    [ 'or',  'mtext', 'or',       PAD_MATH_SPACE ],
    [ 'not', 'mo',    "\u{00AC}", PAD_MATH_CONST, 'neg' ],
    [ '=>',  'mo',    "\u{21D2}", PAD_MATH_CONST, 'implies' ],
    [ 'if',  'mo',    'if',       PAD_MATH_SPACE ],
    [ '<=>', 'mo',    "\u{21D4}", PAD_MATH_CONST, 'iff' ],
    [ 'AA',  'mo',    "\u{2200}", PAD_MATH_CONST, 'forall' ],
    [ 'EE',  'mo',    "\u{2203}", PAD_MATH_CONST, 'exists' ],
    [ '_|_', 'mo',    "\u{22A5}", PAD_MATH_CONST, 'bot' ],
    [ 'TT',  'mo',    "\u{22A4}", PAD_MATH_CONST, 'top' ],
    [ '|--', 'mo',    "\u{22A2}", PAD_MATH_CONST, 'vdash' ],
    [ '|==', 'mo',    "\u{22A8}", PAD_MATH_CONST, 'models' ],

    // Brackets. A | is a left and a right bar at once: AMparseSexpr decides which; || has no
    // symbol of its own (the reference left it out), so ||v|| is two bars around |v|.

    [ '(',   'mo', '(',        PAD_MATH_LEFTBRACKET,  'left(' ],
    [ ')',   'mo', ')',        PAD_MATH_RIGHTBRACKET, 'right)' ],
    [ '[',   'mo', '[',        PAD_MATH_LEFTBRACKET,  'left[' ],
    [ ']',   'mo', ']',        PAD_MATH_RIGHTBRACKET, 'right]' ],
    [ '{',   'mo', '{',        PAD_MATH_LEFTBRACKET ],
    [ '}',   'mo', '}',        PAD_MATH_RIGHTBRACKET ],
    [ '|',   'mo', '|',        PAD_MATH_LEFTRIGHT ],
    [ ':|:', 'mo', '|',        PAD_MATH_CONST ],
    [ '|:',  'mo', '|',        PAD_MATH_LEFTBRACKET ],
    [ ':|',  'mo', '|',        PAD_MATH_RIGHTBRACKET ],
    [ '(:',  'mo', "\u{2329}", PAD_MATH_LEFTBRACKET,  'langle' ],
    [ ':)',  'mo', "\u{232A}", PAD_MATH_RIGHTBRACKET, 'rangle' ],
    [ '<<',  'mo', "\u{2329}", PAD_MATH_LEFTBRACKET ],
    [ '>>',  'mo', "\u{232A}", PAD_MATH_RIGHTBRACKET ],
    [ '{:',  'mo', '{:',       PAD_MATH_LEFTBRACKET,  NULL, [ 'invisible' => TRUE ] ],
    [ ':}',  'mo', ':}',       PAD_MATH_RIGHTBRACKET, NULL, [ 'invisible' => TRUE ] ],

    // Miscellaneous symbols. quad and its kin are an mspace of so many em.

    [ 'int',       'mo',     "\u{222B}", PAD_MATH_CONST ],
    [ 'dx',        'mi',     '{:d x:}',  PAD_MATH_DEFINITION ],
    [ 'dy',        'mi',     '{:d y:}',  PAD_MATH_DEFINITION ],
    [ 'dz',        'mi',     '{:d z:}',  PAD_MATH_DEFINITION ],
    [ 'dt',        'mi',     '{:d t:}',  PAD_MATH_DEFINITION ],
    [ 'oint',      'mo',     "\u{222E}", PAD_MATH_CONST ],
    [ 'del',       'mo',     "\u{2202}", PAD_MATH_CONST, 'partial' ],
    [ 'grad',      'mo',     "\u{2207}", PAD_MATH_CONST, 'nabla' ],
    [ '+-',        'mo',     "\u{00B1}", PAD_MATH_CONST, 'pm' ],
    [ '-+',        'mo',     "\u{2213}", PAD_MATH_CONST, 'mp' ],
    [ 'O/',        'mo',     "\u{2205}", PAD_MATH_CONST, 'emptyset' ],
    [ 'oo',        'mo',     "\u{221E}", PAD_MATH_CONST, 'infty' ],
    [ 'aleph',     'mo',     "\u{2135}", PAD_MATH_CONST ],
    [ '...',       'mo',     '...',      PAD_MATH_CONST, 'ldots' ],
    [ ':.',        'mo',     "\u{2234}", PAD_MATH_CONST, 'therefore' ],
    [ ":'",        'mo',     "\u{2235}", PAD_MATH_CONST, 'because' ],
    [ '/_',        'mo',     "\u{2220}", PAD_MATH_CONST, 'angle' ],
    [ '/_\\',      'mo',     "\u{25B3}", PAD_MATH_CONST, 'triangle' ],
    [ "'",         'mo',     "\u{2032}", PAD_MATH_CONST, 'prime' ],
    [ 'tilde',     'mover',  '~',        PAD_MATH_UNARY, NULL, [ 'acc' => TRUE ] ],
    [ '\\ ',       'mtext',  "\u{00A0}", PAD_MATH_CONST ],
    [ 'frown',     'mo',     "\u{2322}", PAD_MATH_CONST ],
    [ 'quad',      'mspace', '1',        PAD_MATH_CONST ],
    [ 'qquad',     'mspace', '2',        PAD_MATH_CONST ],
    [ 'enspace',   'mspace', '0.5',      PAD_MATH_CONST ],
    [ 'thinspace', 'mspace', '0.17',     PAD_MATH_CONST ],
    [ 'mspace',    'mspace', 'mspace',   PAD_MATH_TEXT ],
    [ 'cdots',     'mo',     "\u{22EF}", PAD_MATH_CONST ],
    [ 'vdots',     'mo',     "\u{22EE}", PAD_MATH_CONST ],
    [ 'ddots',     'mo',     "\u{22F1}", PAD_MATH_CONST ],
    [ 'diamond',   'mo',     "\u{22C4}", PAD_MATH_CONST ],
    [ 'square',    'mo',     "\u{25A1}", PAD_MATH_CONST ],
    [ '|__',       'mo',     "\u{230A}", PAD_MATH_CONST, 'lfloor' ],
    [ '__|',       'mo',     "\u{230B}", PAD_MATH_CONST, 'rfloor' ],
    [ '|~',        'mo',     "\u{2308}", PAD_MATH_CONST, 'lceiling' ],
    [ '~|',        'mo',     "\u{2309}", PAD_MATH_CONST, 'rceiling' ],
    [ 'CC',        'mo',     "\u{2102}", PAD_MATH_CONST ],
    [ 'NN',        'mo',     "\u{2115}", PAD_MATH_CONST ],
    [ 'QQ',        'mo',     "\u{211A}", PAD_MATH_CONST ],
    [ 'RR',        'mo',     "\u{211D}", PAD_MATH_CONST ],
    [ 'ZZ',        'mo',     "\u{2124}", PAD_MATH_CONST ],
    [ 'f',         'mi',     'f',        PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'g',         'mi',     'g',        PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'hbar',      'mo',     "\u{210F}", PAD_MATH_CONST ],

    // Standard functions - one <mi> each, where the reference has an <mo>. abs, norm, floor
    // and ceil are written between their bars; mod stays the operator it is.

    [ 'lim',    'mi', 'lim',    PAD_MATH_UNDEROVER ],
    [ 'Lim',    'mi', 'Lim',    PAD_MATH_UNDEROVER ],
    [ 'sin',    'mi', 'sin',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'cos',    'mi', 'cos',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'tan',    'mi', 'tan',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'sinh',   'mi', 'sinh',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'cosh',   'mi', 'cosh',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'tanh',   'mi', 'tanh',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'cot',    'mi', 'cot',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'sec',    'mi', 'sec',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'csc',    'mi', 'csc',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'arcsin', 'mi', 'arcsin', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'arccos', 'mi', 'arccos', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'arctan', 'mi', 'arctan', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'arcsec', 'mi', 'arcsec', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'arccsc', 'mi', 'arccsc', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'arccot', 'mi', 'arccot', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'coth',   'mi', 'coth',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'sech',   'mi', 'sech',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'csch',   'mi', 'csch',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'exp',    'mi', 'exp',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'abs',    'mi', 'abs',    PAD_MATH_UNARY, NULL, [ 'lr' => [ '|', '|' ] ] ],
    [ 'norm',   'mi', 'norm',   PAD_MATH_UNARY, NULL, [ 'lr' => [ "\u{2016}", "\u{2016}" ] ] ],
    [ 'floor',  'mi', 'floor',  PAD_MATH_UNARY, NULL, [ 'lr' => [ "\u{230A}", "\u{230B}" ] ] ],
    [ 'ceil',   'mi', 'ceil',   PAD_MATH_UNARY, NULL, [ 'lr' => [ "\u{2308}", "\u{2309}" ] ] ],
    [ 'log',    'mi', 'log',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'ln',     'mi', 'ln',     PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'det',    'mi', 'det',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'dim',    'mi', 'dim',    PAD_MATH_CONST ],
    [ 'mod',    'mo', 'mod',    PAD_MATH_CONST ],
    [ 'gcd',    'mi', 'gcd',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'lcm',    'mi', 'lcm',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'lub',    'mi', 'lub',    PAD_MATH_CONST ],
    [ 'glb',    'mi', 'glb',    PAD_MATH_CONST ],
    [ 'min',    'mi', 'min',    PAD_MATH_UNDEROVER ],
    [ 'max',    'mi', 'max',    PAD_MATH_UNDEROVER ],
    [ 'Sin',    'mi', 'Sin',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Cos',    'mi', 'Cos',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Tan',    'mi', 'Tan',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Arcsin', 'mi', 'Arcsin', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Arccos', 'mi', 'Arccos', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Arctan', 'mi', 'Arctan', PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Sinh',   'mi', 'Sinh',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Cosh',   'mi', 'Cosh',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Tanh',   'mi', 'Tanh',   PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Cot',    'mi', 'Cot',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Sec',    'mi', 'Sec',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Csc',    'mi', 'Csc',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Log',    'mi', 'Log',    PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Ln',     'mi', 'Ln',     PAD_MATH_UNARY, NULL, [ 'func' => TRUE ] ],
    [ 'Abs',    'mi', 'abs',    PAD_MATH_UNARY, NULL, [ 'lr' => [ '|', '|' ] ] ],

    // Arrows.

    [ 'uarr',              'mo', "\u{2191}", PAD_MATH_CONST, 'uparrow' ],
    [ 'darr',              'mo', "\u{2193}", PAD_MATH_CONST, 'downarrow' ],
    [ 'rarr',              'mo', "\u{2192}", PAD_MATH_CONST, 'rightarrow' ],
    [ '->',                'mo', "\u{2192}", PAD_MATH_CONST, 'to' ],
    [ '>->',               'mo', "\u{21A3}", PAD_MATH_CONST, 'rightarrowtail' ],
    [ '->>',               'mo', "\u{21A0}", PAD_MATH_CONST, 'twoheadrightarrow' ],
    [ '>->>',              'mo', "\u{2916}", PAD_MATH_CONST, 'twoheadrightarrowtail' ],
    [ '|->',               'mo', "\u{21A6}", PAD_MATH_CONST, 'mapsto' ],
    [ 'larr',              'mo', "\u{2190}", PAD_MATH_CONST, 'leftarrow' ],
    [ 'harr',              'mo', "\u{2194}", PAD_MATH_CONST, 'leftrightarrow' ],
    [ 'rArr',              'mo', "\u{21D2}", PAD_MATH_CONST, 'Rightarrow' ],
    [ 'lArr',              'mo', "\u{21D0}", PAD_MATH_CONST, 'Leftarrow' ],
    [ 'dArr',              'mo', "\u{21D3}", PAD_MATH_CONST, 'Downarrow' ],
    [ 'hArr',              'mo', "\u{21D4}", PAD_MATH_CONST, 'Leftrightarrow' ],
    [ 'rightleftharpoons', 'mo', "\u{21CC}", PAD_MATH_CONST ],

    // Commands with arguments: roots, fractions, scripts, accents, texts, colours and the font
    // commands, which map the letters to a mathematical alphabet.

    [ 'sqrt',     'msqrt',    'sqrt',     PAD_MATH_UNARY ],
    [ 'root',     'mroot',    'root',     PAD_MATH_BINARY ],
    [ 'frac',     'mfrac',    '/',        PAD_MATH_BINARY ],
    [ '/',        'mfrac',    '/',        PAD_MATH_INFIX ],
    [ 'stackrel', 'mover',    'stackrel', PAD_MATH_BINARY ],
    [ 'overset',  'mover',    'stackrel', PAD_MATH_BINARY ],
    [ 'underset', 'munder',   'stackrel', PAD_MATH_BINARY ],
    [ '_',        'msub',     '_',        PAD_MATH_INFIX ],
    [ '^',        'msup',     '^',        PAD_MATH_INFIX ],
    [ 'hat',      'mover',    "\u{0302}", PAD_MATH_UNARY,          NULL,         [ 'acc' => TRUE ] ],
    [ 'bar',      'mover',    "\u{00AF}", PAD_MATH_UNARY,          'overline',   [ 'acc' => TRUE ] ],
    [ 'vec',      'mover',    "\u{2192}", PAD_MATH_UNARY,          NULL,         [ 'acc' => TRUE ] ],
    [ 'dot',      'mover',    '.',        PAD_MATH_UNARY,          NULL,         [ 'acc' => TRUE ] ],
    [ 'ddot',     'mover',    '..',       PAD_MATH_UNARY,          NULL,         [ 'acc' => TRUE ] ],
    [ 'overarc',  'mover',    "\u{23DC}", PAD_MATH_UNARY,          'overparen',  [ 'acc' => TRUE ] ],
    [ 'ul',       'munder',   "\u{0332}", PAD_MATH_UNARY,          'underline',  [ 'acc' => TRUE ] ],
    [ 'ubrace',   'munder',   "\u{23DF}", PAD_MATH_UNARYUNDEROVER, 'underbrace', [ 'acc' => TRUE ] ],
    [ 'obrace',   'mover',    "\u{23DE}", PAD_MATH_UNARYUNDEROVER, 'overbrace',  [ 'acc' => TRUE ] ],
    [ 'text',     'mtext',    'text',     PAD_MATH_TEXT ],
    [ 'mbox',     'mtext',    'mbox',     PAD_MATH_TEXT ],
    [ 'color',    'mstyle',   NULL,       PAD_MATH_BINARY ],
    [ 'id',       'mrow',     NULL,       PAD_MATH_BINARY ],
    [ 'class',    'mrow',     NULL,       PAD_MATH_BINARY ],
    [ 'cancel',   'menclose', 'cancel',   PAD_MATH_UNARY ],
    [ '"',        'mtext',    'mbox',     PAD_MATH_TEXT,           NULL,         [ 'quote' => TRUE ] ],
    [ 'bb',       NULL,       NULL,       PAD_MATH_UNARY,          'mathbf',     [ 'codes' => 'bold' ] ],
    [ 'sf',       NULL,       NULL,       PAD_MATH_UNARY,          'mathsf',     [ 'codes' => 'sans-serif' ] ],
    [ 'sfit',     NULL,       NULL,       PAD_MATH_UNARY,          NULL,         [ 'codes' => 'sans-serif-italic' ] ],
    [ 'bbsf',     NULL,       NULL,       PAD_MATH_UNARY,          NULL,         [ 'codes' => 'bold-sans-serif' ] ],
    [ 'bbb',      NULL,       NULL,       PAD_MATH_UNARY,          'mathbb',     [ 'codes' => 'double-struck' ] ],
    [ 'cc',       NULL,       NULL,       PAD_MATH_UNARY,          'mathcal',    [ 'codes' => 'script' ] ],
    [ 'bbcc',     NULL,       NULL,       PAD_MATH_UNARY,          NULL,         [ 'codes' => 'bold-script' ] ],
    [ 'tt',       NULL,       NULL,       PAD_MATH_UNARY,          'mathtt',     [ 'codes' => 'monospace' ] ],
    [ 'fr',       NULL,       NULL,       PAD_MATH_UNARY,          'mathfrak',   [ 'codes' => 'fraktur' ] ],
    [ 'bbfr',     NULL,       NULL,       PAD_MATH_UNARY,          NULL,         [ 'codes' => 'bold-fraktur' ] ],
    [ 'bbit',     NULL,       NULL,       PAD_MATH_UNARY,          NULL,         [ 'codes' => 'bold-italic' ] ],
    [ 'bbsfit',   NULL,       NULL,       PAD_MATH_UNARY,          NULL,         [ 'codes' => 'sans-serif-bold-italic' ] ],
    [ 'bold',     NULL,       NULL,       PAD_MATH_UNARY ],
    [ 'italic',   NULL,       NULL,       PAD_MATH_UNARY,          'mathit',     [ 'codes' => 'italic' ] ] ];

  // The mathematical alphabets of the font commands, as the reference has them: where in the
  // Mathematical Alphanumeric Symbols block the capitals, the small letters, the digits, the
  // Greek capitals and the small Greek letters of an alphabet begin (NULL: it has none), and
  // the letters that stand in the Letterlike Symbols block instead - the double-struck R is
  // U+211D, not a place in the block, which is left empty there.

  const PAD_MATH_CODEMAPS = [
    'script'                 => [ 0x1D49C, 0x1D4B6, NULL, NULL, NULL,
                                  [ 0x42 => 0x212C, 0x45 => 0x2130, 0x46 => 0x2131, 0x48 => 0x210B, 0x49 => 0x2110, 0x4C => 0x2112,
                                    0x4D => 0x2133, 0x52 => 0x211B, 0x65 => 0x212F, 0x67 => 0x210A, 0x6F => 0x2134 ] ],
    'bold-script'            => [ 0x1D4D0, 0x1D4EA, NULL, NULL, NULL ],
    'fraktur'                => [ 0x1D504, 0x1D51E, NULL, NULL, NULL,
                                  [ 0x43 => 0x212D, 0x48 => 0x210C, 0x49 => 0x2111, 0x52 => 0x211C, 0x5A => 0x2128 ] ],
    'bold-fraktur'           => [ 0x1D56C, 0x1D586, NULL, NULL, NULL ],
    'double-struck'          => [ 0x1D538, 0x1D552, 0x1D7D8, NULL, NULL,
                                  [ 0x43 => 0x2102, 0x48 => 0x210D, 0x4E => 0x2115, 0x50 => 0x2119, 0x51 => 0x211A, 0x52 => 0x211D,
                                    0x5A => 0x2124, 0x393 => 0x213E, 0x3A0 => 0x213F, 0x3B3 => 0x213D, 0x3C0 => 0x213C ] ],
    'bold'                   => [ 0x1D400, 0x1D41A, 0x1D7CE, 0x1D6A8, 0x1D6C2 ],
    'italic'                 => [ 0x1D434, 0x1D44E, NULL, 0x1D6E2, 0x1D6FC, [ 0x68 => 0x210E ] ],
    'bold-italic'            => [ 0x1D468, 0x1D482, NULL, 0x1D71C, 0x1D736 ],
    'sans-serif'             => [ 0x1D5A0, 0x1D5BA, 0x1D7E2, NULL, NULL ],
    'sans-serif-italic'      => [ 0x1D608, 0x1D622, 0x1D7E2, NULL, NULL ],
    'bold-sans-serif'        => [ 0x1D5D4, 0x1D5EE, 0x1D7EC, 0x1D756, 0x1D770 ],
    'sans-serif-bold-italic' => [ 0x1D63C, 0x1D656, 0x1D7EC, 0x1D790, 0x1D7AA ],
    'monospace'              => [ 0x1D670, 0x1D68A, 0x1D7F6, NULL, NULL ] ];

  // The ranges those alphabets map: A-Z, a-z, 0-9, the Greek capitals and the small Greek
  // letters, each with the symbols that follow it in the alphabets (theta symbol and nabla
  // after the capitals, the partial sign and the variant letters after the small ones).

  const PAD_MATH_CODERANGES = [
    [ 0x41,  0x5A ],
    [ 0x61,  0x7A ],
    [ 0x30,  0x39 ],
    [ 0x391, 0x3A9, [ 0x3F4 => 0x3A2, 0x2207 => 0x3AA ] ],
    [ 0x3B1, 0x3C9, [ 0x2202 => 0x3CA, 0x3F5 => 0x3CB, 0x3D1 => 0x3CC, 0x3F0 => 0x3CD, 0x3D5 => 0x3CE, 0x3F1 => 0x3CF, 0x3D6 => 0x3D0 ] ] ];

  // The colour names of CSS, the only words color(...) takes besides a #hex.

  const PAD_MATH_COLORS = 'aliceblue antiquewhite aqua aquamarine azure beige bisque black blanchedalmond blue blueviolet '
    . 'brown burlywood cadetblue chartreuse chocolate coral cornflowerblue cornsilk crimson currentcolor cyan darkblue '
    . 'darkcyan darkgoldenrod darkgray darkgreen darkgrey darkkhaki darkmagenta darkolivegreen darkorange darkorchid '
    . 'darkred darksalmon darkseagreen darkslateblue darkslategray darkslategrey darkturquoise darkviolet deeppink '
    . 'deepskyblue dimgray dimgrey dodgerblue firebrick floralwhite forestgreen fuchsia gainsboro ghostwhite gold '
    . 'goldenrod gray green greenyellow grey honeydew hotpink indianred indigo ivory khaki lavender lavenderblush '
    . 'lawngreen lemonchiffon lightblue lightcoral lightcyan lightgoldenrodyellow lightgray lightgreen lightgrey '
    . 'lightpink lightsalmon lightseagreen lightskyblue lightslategray lightslategrey lightsteelblue lightyellow lime '
    . 'limegreen linen magenta maroon mediumaquamarine mediumblue mediumorchid mediumpurple mediumseagreen '
    . 'mediumslateblue mediumspringgreen mediumturquoise mediumvioletred midnightblue mintcream mistyrose moccasin '
    . 'navajowhite navy oldlace olive olivedrab orange orangered orchid palegoldenrod palegreen paleturquoise '
    . 'palevioletred papayawhip peachpuff peru pink plum powderblue purple rebeccapurple red rosybrown royalblue '
    . 'saddlebrown salmon sandybrown seagreen seashell sienna silver skyblue slateblue slategray slategrey snow '
    . 'springgreen steelblue tan teal thistle tomato transparent turquoise violet wheat white whitesmoke yellow '
    . 'yellowgreen';

  // How deep brackets and commands may nest before the rest of a formula is written as the text
  // it is: a formula of fifty levels is no formula, and the parser recurses per level.

  const PAD_MATH_DEEPEST = 50;

  // How long a formula may be, in bytes: the text is read as what is left of it after every
  // token, so the time grows with the square of the length - 10 000 bytes, some 150 lines of
  // formulas, in a tenth of a second (types/math.php names a longer one).

  const PAD_MATH_LONGEST = 10000;

  // The <math> element of a formula: display="block" for a formula on a line of its own, the
  // alttext the text itself with its white space squeezed (or $alt when given). A blank text
  // is no formula and answers ''.

  function padMathMl ( string $text, bool $block = FALSE, string $alt = '' ) : string {

    if ( trim ( $text ) === '' )
      return '';

    // The cleaning up the reference does first, for what editors put in a text.

    $str = str_replace ( '&nbsp;', '', $text );
    $str = str_replace ( '&gt;',   '>', $str );
    $str = str_replace ( '&lt;',   '<', $str );
    $str = preg_replace ( '/^\s+/u', '', $str ) ?? ltrim ( $str );

    if ( trim ( $str ) === '' )
      return '';

    $state = [ 'depth' => 0, 'prev' => PAD_MATH_CONST, 'cur' => PAD_MATH_CONST, 'level' => 0, 'memo' => [] ];

    list ( $frag ) = padMathParseExpr ( $str, FALSE, $state );

    if ( $alt === '' )
      $alt = trim ( preg_replace ( '/\s+/u', ' ', $text ) ?? $text );

    return '<math class="pad-math"' . ( $block ? ' display="block"' : '' ) . ' alttext="' . padMathEscape ( $alt ) . '">'
         . padMathXml ( $frag ) . '</math>';

  }

  // The table sorted by what is typed, with the LaTeX names added as symbols of their own the
  // way the reference's initSymbols adds them (they keep the element, the text, the kind, the
  // accent and the alphabet, not the rest), and the list of the names AMgetSymbol searches.

  function padMathSymbols () {

    static $table = NULL;

    if ( $table !== NULL )
      return $table;

    $symbols = $alias = [];

    foreach ( PAD_MATH_SYMBOLS as $row ) {
      $symbol = [ 'input' => $row [0], 'tag' => $row [1], 'output' => $row [2], 'ttype' => $row [3] ] + ( $row [5] ?? [] );
      $symbols [] = $symbol;
      if ( isset ( $row [4] ) )
        $alias [] = [ 'input' => $row [4], 'tag' => $row [1], 'output' => $row [2], 'ttype' => $row [3] ]
                  + array_intersect_key ( $row [5] ?? [], [ 'acc' => 1, 'codes' => 1 ] );
    }

    $symbols = array_merge ( $symbols, $alias );

    usort ( $symbols, fn ( $a, $b ) => strcmp ( $a ['input'], $b ['input'] ) );

    return $table = [ $symbols, array_column ( $symbols, 'input' ) ];

  }

  // AMremoveCharsAndBlanks: the text after its first $n bytes and the blanks behind them. A
  // backslash there is dropped too, unless a second one or a space follows it, so \alpha is
  // read as alpha.

  function padMathRemoveChars ( $str, $n ) {

    if ( substr ( $str, $n, 1 ) === '\\' and substr ( $str, $n + 1, 1 ) !== '\\' and substr ( $str, $n + 1, 1 ) !== ' ' )
      $str = (string) substr ( $str, $n + 1 );
    else
      $str = (string) substr ( $str, $n );

    $i = 0;
    $length = strlen ( $str );

    while ( $i < $length and ord ( $str [$i] ) <= 32 )
      $i++;

    return (string) substr ( $str, $i );

  }

  // The reference's position: the first place at or after $n in the sorted names where $str
  // stands or would be put - a binary search from the start, a walk from elsewhere.

  function padMathPosition ( $names, $str, $n ) {

    $count = count ( $names );

    if ( $n == 0 ) {
      $n = -1;
      $h = $count;
      while ( $n + 1 < $h ) {
        $m = ( $n + $h ) >> 1;
        if ( strcmp ( $names [$m], $str ) < 0 )
          $n = $m;
        else
          $h = $m;
      }
      return $h;
    }

    for ( $i = $n; $i < $count and strcmp ( $names [$i], $str ) < 0; $i++ );

    return $i;

  }

  // A digit, one byte of 0-9: the reference's "0" <= st && st <= "9".

  function padMathDigit ( $char ) {

    return strlen ( $char ) == 1 and $char >= '0' and $char <= '9';

  }

  // AMgetSymbol: the longest symbol the text starts with, else a number (digits with at most
  // one decimal point), else its first character - a letter an <mi>, anything else an <mo>.
  // The kind of the symbol read before is kept, for one trick of the reference: a minus right
  // after ^, _ or / is a sign that takes what follows (x^-1), not an operator.

  function padMathGetSymbol ( $str, &$state ) {

    list ( $symbols, $names ) = padMathSymbols ();

    $count  = count ( $names );
    $length = strlen ( $str );
    $k      = 0;
    $mk     = -1;
    $match  = '';
    $more   = TRUE;

    for ( $i = 1; $i <= $length and $more; $i++ ) {
      $st = substr ( $str, 0, $i );
      $k  = padMathPosition ( $names, $st, $k );
      if ( $k < $count and substr ( $str, 0, strlen ( $names [$k] ) ) === $names [$k] ) {
        $match = $names [$k];
        $mk    = $k;
        $i     = strlen ( $match );
      }
      $more = ( $k < $count and strcmp ( substr ( $str, 0, strlen ( $names [$k] ) ), $names [$k] ) >= 0 );
    }

    $state ['prev'] = $state ['cur'];

    if ( $match !== '' ) {
      $state ['cur'] = $symbols [$mk] ['ttype'];
      return $symbols [$mk];
    }

    $state ['cur'] = PAD_MATH_CONST;

    $k     = 1;
    $st    = substr ( $str, 0, 1 );
    $integ = TRUE;
    $used  = FALSE;

    while ( padMathDigit ( $st ) and $k <= $length ) {
      $st = substr ( $str, $k, 1 );
      $k++;
    }

    if ( $st === '.' ) {
      $st = substr ( $str, $k, 1 );
      if ( $k > 1 and $k < $length ) {
        $k++;
        $used = TRUE;
      }
      if ( padMathDigit ( $st ) ) {
        $integ = FALSE;
        if ( ! $used )
          $k++;
        while ( padMathDigit ( $st ) and $k <= $length ) {
          $st = substr ( $str, $k, 1 );
          $k++;
        }
      }
    }

    if ( ( $integ and $k > 1 ) or $k > 2 ) {
      $st  = substr ( $str, 0, $k - 1 );
      $tag = 'mn';
    } else {
      $st  = preg_match ( '/^./su', $str, $m ) ? $m [0] : substr ( $str, 0, 1 );
      $tag = preg_match ( '/^[A-Za-z]$/D', $st ) ? 'mi' : 'mo';
    }

    $output = ( $st === '-' ) ? "\u{2212}" : $st;

    if ( $st === '-' and substr ( $str, 1, 1 ) !== ' ' and $state ['prev'] == PAD_MATH_INFIX ) {
      $state ['cur'] = PAD_MATH_INFIX;
      return [ 'input' => $st, 'tag' => $tag, 'output' => $output, 'ttype' => PAD_MATH_UNARY, 'func' => TRUE ];
    }

    return [ 'input' => $st, 'tag' => $tag, 'output' => $output, 'ttype' => PAD_MATH_CONST ];

  }

  // The nodes. An element is [ 't' => tag, 'a' => attributes, 'c' => children ], a child an
  // element or a string (a text node); a fragment is an element with the tag '', spliced in
  // where it is appended, as a DOM DocumentFragment is.

  function padMathNode ( $tag, $child = NULL ) {

    $node = [ 't' => $tag, 'a' => [], 'c' => [] ];

    if ( $child !== NULL )
      padMathAppend ( $node, $child );

    return $node;

  }

  // appendChild: a child added at the end, the children of a fragment one by one.

  function padMathAppend ( &$node, $child ) {

    if ( is_array ( $child ) and $child ['t'] === '' )
      foreach ( $child ['c'] as $kid )
        $node ['c'] [] = $kid;
    else
      $node ['c'] [] = $child;

  }

  // A symbol as its element: the text it stands for, an upright capital marked so, the name of
  // a function (an <mi> of more than one letter: sin, lim, det) marked as a word for the
  // spacing padMathXml gives it.

  function padMathToken ( $symbol ) {

    $node = padMathNode ( $symbol ['tag'], (string) $symbol ['output'] );

    if ( ! empty ( $symbol ['normal'] ) )
      $node ['a'] ['mathvariant'] = 'normal';

    if ( $symbol ['tag'] === 'mi' and preg_match ( '/^[A-Za-z]{2,}$/D', (string) $symbol ['output'] ) )
      $node ['word'] = TRUE;

    return $node;

  }

  // The DOM's node.firstChild.nodeValue: the text of a token element, NULL for anything else -
  // and NULL where the reference would stop on an element without children.

  function padMathValue ( $node ) {

    return ( is_array ( $node ) and isset ( $node ['c'] [0] ) and is_string ( $node ['c'] [0] ) ) ? $node ['c'] [0] : NULL;

  }

  // The DOM's textContent: all the text inside a node.

  function padMathTextContent ( $node ) {

    if ( is_string ( $node ) )
      return $node;

    $text = '';

    foreach ( $node ['c'] as $kid )
      $text .= padMathTextContent ( $kid );

    return $text;

  }

  // JavaScript's trim: white space at both ends, the no-break space included.

  function padMathTrim ( $text ) {

    return preg_replace ( '/^[\s\p{Zs}\x{2028}\x{2029}\x{FEFF}]+|[\s\p{Zs}\x{2028}\x{2029}\x{FEFF}]+$/u', '', $text ) ?? trim ( $text );

  }

  // The length of a text as JavaScript counts it, in UTF-16 units: a letter of the
  // mathematical alphabets is two.

  function padMathUnits ( $text ) {

    return mb_strlen ( $text, 'UTF-8' ) + preg_match_all ( '/[\x{10000}-\x{10FFFF}]/u', $text );

  }

  // AMremoveBrackets: the brackets ( [ { and ) ] } of a group taken off - the brackets of
  // a/b, of the argument of sqrt, of a script - unless a table stands right inside them.

  function padMathRemoveBrackets ( &$node ) {

    if ( ! is_array ( $node ) or ! $node ['c'] or $node ['t'] != 'mrow' )
      return;

    $first = $node ['c'] [0];

    if ( is_array ( $first ) and $first ['c'] ) {
      if ( isset ( $node ['c'] [1] ) and is_array ( $node ['c'] [1] ) and $node ['c'] [1] ['t'] == 'mtable' )
        return;
      if ( in_array ( padMathValue ( $first ), [ '(', '[', '{' ], TRUE ) )
        array_shift ( $node ['c'] );
    }

    if ( ! $node ['c'] )
      return;

    $last = $node ['c'] [ count ( $node ['c'] ) - 1 ];

    if ( is_array ( $last ) and $last ['c'] and in_array ( padMathValue ( $last ), [ ')', ']', '}' ], TRUE ) )
      array_pop ( $node ['c'] );

  }

  // AMparseSexpr: a simple expression - a symbol, a bracketed expression, a text, or a command
  // with its arguments - and the text after it. NULL when a right bracket closes the group.

  function padMathParseSexpr ( $str, &$state ) {

    $str    = padMathRemoveChars ( $str, 0 );
    $symbol = padMathGetSymbol ( $str, $state );

    if ( $symbol ['ttype'] == PAD_MATH_RIGHTBRACKET and $state ['depth'] > 0 )
      return [ NULL, $str ];

    // Nested beyond reason: the rest is written as the text it is.

    if ( $state ['level'] >= PAD_MATH_DEEPEST )
      return [ padMathNode ( 'mtext', $str ), '' ];

    $state ['level']++;
    $result = padMathSexpr ( $str, $symbol, $state );
    $state ['level']--;

    return $result;

  }

  // The body of AMparseSexpr, a case per kind of symbol; a defined symbol (dx, divide) is
  // replaced by what it stands for and read again.

  function padMathSexpr ( $str, $symbol, &$state ) {

    $frag = padMathNode ( '' );

    if ( $symbol ['ttype'] == PAD_MATH_DEFINITION ) {
      $str    = $symbol ['output'] . padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );
      $symbol = padMathGetSymbol ( $str, $state );
    }

    switch ( $symbol ['ttype'] ) {

      case PAD_MATH_UNDEROVER:
      case PAD_MATH_CONST:

        $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

        if ( $symbol ['tag'] === 'mspace' ) {
          $node = padMathNode ( 'mspace' );
          $node ['a'] ['width'] = $symbol ['output'] . 'em';
          return [ $node, $str ];
        }

        return [ padMathToken ( $symbol ), $str ];

      case PAD_MATH_LEFTBRACKET:

        $state ['depth']++;
        $result = padMathParseExpr ( padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) ), TRUE, $state );
        $state ['depth']--;

        if ( ! empty ( $symbol ['invisible'] ) )
          $node = padMathNode ( 'mrow', $result [0] );
        else {
          $node = padMathNode ( 'mrow', padMathToken ( $symbol ) );
          padMathAppend ( $node, $result [0] );
        }

        return [ $node, $result [1] ];

      case PAD_MATH_TEXT:

        return padMathText ( $str, $symbol );

      case PAD_MATH_UNARYUNDEROVER:
      case PAD_MATH_UNARY:

        return padMathUnary ( $str, $symbol, $state );

      case PAD_MATH_BINARY:

        return padMathBinary ( $str, $symbol, $state );

      case PAD_MATH_INFIX:

        $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

        return [ padMathNode ( 'mo', $symbol ['output'] ), $str ];

      case PAD_MATH_SPACE:

        $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

        $space = padMathNode ( 'mspace' );
        $space ['a'] ['width'] = '1ex';

        $frag ['c'] = [ $space, padMathToken ( $symbol ), $space ];

        return [ padMathNode ( 'mrow', $frag ), $str ];

      case PAD_MATH_LEFTRIGHT:

        return padMathLeftRight ( $str, $symbol, $state );

      default:

        $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

        return [ padMathToken ( $symbol ), $str ];

    }

  }

  // A text - text(...), mbox(...), "..." - is an <mtext> of what stands between the brackets or
  // the quotes, with a space of 1ex before and after it when the text starts or ends with one;
  // mspace(...) is a space of the width given, a number alone counted in 1/16 em as the
  // reference counts it. A text whose bracket never closes runs to the end; one without a
  // bracket is empty and takes the character after the command along, as in the reference.

  function padMathText ( $str, $symbol ) {

    if ( empty ( $symbol ['quote'] ) )
      $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

    $first = substr ( $str, 0, 1 );

    if ( $first === '{' )
      $i = strpos ( $str, '}' );
    elseif ( $first === '(' )
      $i = strpos ( $str, ')' );
    elseif ( $first === '[' )
      $i = strpos ( $str, ']' );
    elseif ( ! empty ( $symbol ['quote'] ) ) {
      $i = strpos ( (string) substr ( $str, 1 ), '"' );
      $i = ( $i === FALSE ) ? 0 : $i + 1;
    } else
      $i = 0;

    if ( $i === FALSE )
      $i = strlen ( $str );

    $st = ( $i > 1 ) ? substr ( $str, 1, $i - 1 ) : '';

    if ( $symbol ['input'] === 'mspace' ) {
      if ( ! preg_match ( '/^(-?[\d.]+)\s*(em|mu)?$/D', $st, $m ) )
        $st = '0em';
      elseif ( ( ( $m [2] ?? '' ) === '' or $m [2] === 'mu' ) and preg_match ( '/^-?(\d+\.?\d*|\.\d+)/', $m [1], $n ) )
        $st = padMathNumber ( (float) $n [0] / 16 ) . 'em';
      $node = padMathNode ( 'mspace' );
      $node ['a'] ['width'] = $st;
      return [ $node, padMathRemoveChars ( $str, $i + 1 ) ];
    }

    $space = padMathNode ( 'mspace' );
    $space ['a'] ['width'] = '1ex';

    $frag = padMathNode ( '' );

    if ( substr ( $st, 0, 1 ) === ' ' )
      $frag ['c'] [] = $space;

    $frag ['c'] [] = padMathNode ( $symbol ['tag'], $st );

    if ( substr ( $st, -1 ) === ' ' )
      $frag ['c'] [] = $space;

    return [ padMathNode ( 'mrow', $frag ), padMathRemoveChars ( $str, $i + 1 ) ];

  }

  // A number as JavaScript writes it, for a width: no trailing zeros, no exponent for the
  // sizes a space has.

  function padMathNumber ( $number ) {

    $text = rtrim ( rtrim ( number_format ( $number, 10, '.', '' ), '0' ), '.' );

    return ( $text === '' or $text === '-0' ) ? '0' : $text;

  }

  // A command of one argument: a function name (sin x - the name alone when ^, _, / or | comes
  // next, so sin^2 x is (sin^2) x), a root, the bars of abs and norm, a strike through, an
  // accent, or a font. The argument loses its brackets, sqrt(x) being the root of x.

  function padMathUnary ( $str, $symbol, &$state ) {

    $str    = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );
    $result = padMathParseSexpr ( $str, $state );

    if ( $result [0] === NULL ) {
      if ( $symbol ['tag'] === 'mi' or $symbol ['tag'] === 'mo' )
        return [ padMathToken ( $symbol ), $str ];
      $result [0] = padMathNode ( 'mi' );
    }

    if ( ! empty ( $symbol ['func'] ) ) {
      $st = substr ( $str, 0, 1 );
      if ( in_array ( $st, [ '^', '_', '/', '|', ',' ], TRUE )
           or ( strlen ( $symbol ['input'] ) == 1 and preg_match ( '/\w/', $symbol ['input'] ) and $st !== '(' ) )
        return [ padMathToken ( $symbol ), $str ];
      $node = padMathNode ( 'mrow', padMathToken ( $symbol ) );
      padMathAppend ( $node, $result [0] );
      if ( $symbol ['input'] !== '-' )
        $node ['apply'] = TRUE;
      return [ $node, $result [1] ];
    }

    padMathRemoveBrackets ( $result [0] );

    if ( $symbol ['input'] === 'sqrt' )
      return [ padMathNode ( 'msqrt', $result [0] ), $result [1] ];

    if ( isset ( $symbol ['lr'] ) ) {
      $node = padMathNode ( 'mrow', padMathNode ( 'mo', $symbol ['lr'] [0] ) );
      padMathAppend ( $node, $result [0] );
      padMathAppend ( $node, padMathNode ( 'mo', $symbol ['lr'] [1] ) );
      return [ $node, $result [1] ];
    }

    // cancel: menclose notation="updiagonalstrike" in the reference, which MathML Core does
    // not have - written as <mrow class="pad-math-cancel">, struck through by the style.

    if ( $symbol ['input'] === 'cancel' )
      return [ padMathNode ( 'menclose', $result [0] ), $result [1] ];

    if ( ! empty ( $symbol ['acc'] ) ) {
      $node = padMathNode ( $symbol ['tag'], $result [0] );
      if ( $symbol ['tag'] == 'mover' and $symbol ['ttype'] == PAD_MATH_UNARY )
        $node ['a'] ['accent'] = 'true';
      elseif ( $symbol ['tag'] == 'munder' and $symbol ['ttype'] == PAD_MATH_UNARY )
        $node ['a'] ['accentunder'] = 'true';
      $accent = padMathNode ( 'mo', $symbol ['output'] );
      $accent ['a'] ['stretchy'] = ( $symbol ['input'] === 'vec' and padMathOneChar ( $result [0] ) ) ? 'false' : 'true';
      $node ['c'] [] = $accent;
      return [ $node, $result [1] ];
    }

    if ( $symbol ['input'] === 'bold' or $symbol ['input'] === 'italic' ) {
      $class = 'pad-math-' . $symbol ['input'];
      $result [0] ['a'] ['class'] = isset ( $result [0] ['a'] ['class'] ) ? $result [0] ['a'] ['class'] . " $class" : $class;
      return $result;
    }

    if ( isset ( $symbol ['codes'] ) and is_string ( $symbol ['codes'] ) )
      padMathMapChars ( $result [0], $symbol ['codes'], $symbol ['input'] );

    return $result;

  }

  // Whether the base of vec is one character, for which the reference keeps the arrow from
  // stretching: a token of one, or a row holding just such a token.

  function padMathOneChar ( $node ) {

    if ( $node ['t'] == 'mrow' and count ( $node ['c'] ) == 1 and padMathValue ( $node ['c'] [0] ) !== NULL
         and padMathUnits ( padMathValue ( $node ['c'] [0] ) ) == 1 )
      return TRUE;

    return padMathValue ( $node ) !== NULL and padMathUnits ( padMathValue ( $node ) ) == 1;

  }

  // A command of two arguments: frac, root (the index first), stackrel and overset (the part
  // above first), underset, and color, class and id, whose first argument is read as the text
  // it is. A missing argument leaves the command's name standing as an operator.

  function padMathBinary ( $str, $symbol, &$state ) {

    $str    = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );
    $result = padMathParseSexpr ( $str, $state );

    if ( $result [0] === NULL )
      return [ padMathNode ( 'mo', $symbol ['input'] ), $str ];

    padMathRemoveBrackets ( $result [0] );

    $result2 = padMathParseSexpr ( $result [1], $state );

    if ( $result2 [0] === NULL )
      return [ padMathNode ( 'mo', $symbol ['input'] ), $str ];

    padMathRemoveBrackets ( $result2 [0] );

    if ( in_array ( $symbol ['input'], [ 'color', 'class', 'id' ], TRUE ) ) {

      $first = substr ( $str, 0, 1 );
      $close = [ '{' => '}', '(' => ')', '[' => ']' ] [$first] ?? NULL;

      if ( $close === NULL )
        $st = (string) substr ( $str, 1 );
      elseif ( ( $i = strpos ( $str, $close ) ) === FALSE )
        $st = (string) substr ( $str, 1, -1 );
      else
        $st = (string) substr ( $str, 1, $i - 1 );

      $node = padMathNode ( $symbol ['tag'], $result2 [0] );
      $st   = trim ( $st );

      if ( $symbol ['input'] === 'color' and padMathColor ( $st ) )
        $node ['a'] ['mathcolor'] = $st;
      elseif ( $symbol ['input'] === 'class' and preg_match ( '/^[\w-]+( +[\w-]+)*$/D', $st ) )
        $node ['a'] ['class'] = $st;
      elseif ( $symbol ['input'] === 'id' and preg_match ( '/^[A-Za-z][\w-]*$/D', $st ) )
        $node ['a'] ['id'] = $st;

      return [ $node, $result2 [1] ];

    }

    $frag = padMathNode ( '' );

    if ( $symbol ['input'] === 'root' or $symbol ['output'] === 'stackrel' )
      $frag ['c'] [] = $result2 [0];

    $frag ['c'] [] = $result [0];

    if ( $symbol ['input'] === 'frac' )
      $frag ['c'] [] = $result2 [0];

    return [ padMathNode ( $symbol ['tag'], $frag ), $result2 [1] ];

  }

  // A colour CSS knows by name, or a #hex of 3, 4, 6 or 8 digits.

  function padMathColor ( $color ) {

    return preg_match ( '/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/Di', $color )
        or in_array ( strtolower ( $color ), explode ( ' ', PAD_MATH_COLORS ), TRUE );

  }

  // A bar: the left of |x| when a bar closes what follows it, else a \mid - a divides sign
  // U+2223, standing alone, as in {x | x > 0} - and the text is read again from after it.

  function padMathLeftRight ( $str, $symbol, &$state ) {

    $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

    $state ['depth']++;
    $result = padMathParseExpr ( $str, FALSE, $state );
    $state ['depth']--;

    $last = $result [0] ['c'] ? $result [0] ['c'] [ count ( $result [0] ['c'] ) - 1 ] : NULL;
    $st   = ( $last !== NULL ) ? padMathValue ( $last ) : '';

    if ( $st === '|' and substr ( $str, 0, 1 ) !== ',' ) {
      $node = padMathNode ( 'mrow', padMathToken ( $symbol ) );
      padMathAppend ( $node, $result [0] );
      return [ $node, $result [1] ];
    }

    return [ padMathNode ( 'mrow', padMathNode ( 'mo', "\u{2223}" ) ), $str ];

  }

  // AMmapChars: the letters (and the digits and Greek letters the alphabet has) of every token
  // inside a node written in a mathematical alphabet - bbb R is the double-struck R, U+211D. A
  // bold alphabet without digits of its own takes the bold digits.

  function padMathMapChars ( &$node, $variant, $input ) {

    $codemap = PAD_MATH_CODEMAPS [$variant];

    if ( ! $codemap [2] and substr ( $input, 0, 2 ) == 'bb' )
      $codemap [2] = PAD_MATH_CODEMAPS ['bold'] [2];

    padMathMapNode ( $node, $codemap );

  }

  // The walk of AMmapChars: the text of a token mapped a character at a time, the children of
  // anything else walked.

  function padMathMapNode ( &$node, $codemap ) {

    if ( ! is_array ( $node ) )
      return;

    if ( in_array ( $node ['t'], [ 'mi', 'mo', 'mn', 'mtext' ], TRUE ) ) {

      if ( padMathValue ( $node ) === NULL )
        return;

      $remap = $codemap [5] ?? [];
      $text  = '';
      $chars = preg_split ( '//u', $node ['c'] [0], -1, PREG_SPLIT_NO_EMPTY );

      if ( $chars === FALSE )
        return;

      foreach ( $chars as $char ) {
        $code   = mb_ord ( $char, 'UTF-8' );
        $mapped = NULL;
        for ( $k = 0; $k < 5 and $mapped === NULL; $k++ ) {
          if ( ! $codemap [$k] )
            continue;
          $range = PAD_MATH_CODERANGES [$k];
          if ( isset ( $range [2] [$code] ) )
            $mapped = $range [2] [$code] - $range [0] + $codemap [$k];
          elseif ( $code >= $range [0] and $code <= $range [1] )
            $mapped = $remap [$code] ?? ( $code - $range [0] + $codemap [$k] );
        }
        $text .= ( $mapped === NULL ) ? $char : mb_chr ( $mapped, 'UTF-8' );
      }

      $node ['c'] [0] = $text;
      return;

    }

    foreach ( array_keys ( $node ['c'] ) as $i )
      padMathMapNode ( $node ['c'] [$i], $codemap );

  }

  // AMparseIexpr: an intermediate expression - a simple one with its scripts, x_i, x^2,
  // x_i^2 (one msubsup), sum_(i=1)^n (under and over, for sum, prod, lim and the big
  // operators). A missing script is a square; a function name with scripts takes the next
  // expression as its argument, sin^2 x.

  function padMathParseIexpr ( $str, &$state ) {

    $str    = padMathRemoveChars ( $str, 0 );
    $sym1   = padMathGetSymbol ( $str, $state );
    $result = padMathParseSexpr ( $str, $state );
    $node   = $result [0];
    $str    = $result [1];
    $symbol = padMathGetSymbol ( $str, $state );

    if ( $symbol ['ttype'] != PAD_MATH_INFIX or $symbol ['input'] === '/' )
      return [ $node, $str ];

    $str    = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );
    $result = padMathParseSexpr ( $str, $state );

    if ( $result [0] === NULL )
      $result [0] = padMathNode ( 'mo', "\u{25A1}" );
    else
      padMathRemoveBrackets ( $result [0] );

    $str       = $result [1];
    $underover = ( $sym1 ['ttype'] == PAD_MATH_UNDEROVER or $sym1 ['ttype'] == PAD_MATH_UNARYUNDEROVER );

    if ( $symbol ['input'] === '_' ) {
      $sym2 = padMathGetSymbol ( $str, $state );
      if ( $sym2 ['input'] === '^' ) {
        $str  = padMathRemoveChars ( $str, strlen ( $sym2 ['input'] ) );
        $res2 = padMathParseSexpr ( $str, $state );
        if ( $res2 [0] === NULL )
          $res2 [0] = padMathNode ( 'mo', "\u{25A1}" );
        else
          padMathRemoveBrackets ( $res2 [0] );
        $str  = $res2 [1];
        $node = padMathNode ( $underover ? 'munderover' : 'msubsup', $node );
        $node ['c'] [] = $result [0];
        $node ['c'] [] = $res2 [0];
        $node = padMathNode ( 'mrow', $node );
      } else {
        $node = padMathNode ( $underover ? 'munder' : 'msub', $node );
        $node ['c'] [] = $result [0];
      }
    } elseif ( $symbol ['input'] === '^' and $underover ) {
      $node = padMathNode ( 'mover', $node );
      $node ['c'] [] = $result [0];
    } else {
      $node = padMathNode ( $symbol ['tag'], $node );
      $node ['c'] [] = $result [0];
    }

    if ( ! empty ( $sym1 ['func'] ) ) {
      $sym2 = padMathGetSymbol ( $str, $state );
      if ( $sym2 ['ttype'] != PAD_MATH_INFIX and $sym2 ['ttype'] != PAD_MATH_RIGHTBRACKET
           and ( strlen ( $sym1 ['input'] ) > 1 or $sym2 ['ttype'] == PAD_MATH_LEFTBRACKET ) and $state ['level'] < PAD_MATH_DEEPEST ) {
        $state ['level']++;
        $result = padMathParseIexpr ( $str, $state );
        $state ['level']--;
        $node   = padMathNode ( 'mrow', $node );
        $node ['c'] [] = $result [0];
        if ( $sym1 ['input'] !== '-' )
          $node ['apply'] = TRUE;
        $str = $result [1];
      }
    }

    return [ $node, $str ];

  }

  // AMparseExpr: an expression - intermediate expressions one after the other, a/b made a
  // fraction (its two sides without their brackets), up to the right bracket that closes the
  // group, which is written after it (unless it is :}). A group of rows of equal length -
  // [[a,b],[c,d]], ((1,2),(3,4)), {(x, if x > 0),(-x, if x < 0):} - is a table.
  //
  // A bar that turns out to be a \mid reads its text again (padMathLeftRight), so bars inside
  // bars could take time doubling with each; the answer of every expression read is kept for
  // the formula, which is what makes reading it again cost nothing.

  function padMathParseExpr ( $str, $rightbracket, &$state ) {

    $key = ( $rightbracket ? 1 : 0 ) . ',' . $state ['depth'] . ',' . $state ['prev'] . ',' . $state ['cur'] . ',' . $state ['level']
         . ',' . strlen ( $str ) . ',' . hash ( 'xxh128', $str );

    if ( isset ( $state ['memo'] [$key] ) ) {
      list ( $frag, $rest, $state ['prev'], $state ['cur'] ) = $state ['memo'] [$key];
      return [ $frag, is_int ( $rest ) ? (string) substr ( $str, strlen ( $str ) - $rest ) : $rest ];
    }

    $answer = padMathExpr ( $str, $rightbracket, $state );

    // What is left of the text is kept as its length when it is the end of the text read -
    // always, but after a definition spliced in - as each answer kept the rest itself, and a
    // formula of n groups held n copies of its rest: memory with the square of its length.

    $rest = str_ends_with ( $str, $answer [1] ) ? strlen ( $answer [1] ) : $answer [1];

    $state ['memo'] [$key] = [ $answer [0], $rest, $state ['prev'], $state ['cur'] ];

    return $answer;

  }

  // The body of AMparseExpr, read once per text and state.

  function padMathExpr ( $str, $rightbracket, &$state ) {

    $frag = padMathNode ( '' );

    do {

      $str    = padMathRemoveChars ( $str, 0 );
      $result = padMathParseIexpr ( $str, $state );
      $node   = $result [0];
      $str    = $result [1];
      $symbol = padMathGetSymbol ( $str, $state );

      if ( $symbol ['ttype'] == PAD_MATH_INFIX and $symbol ['input'] === '/' ) {
        $str    = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );
        $result = padMathParseIexpr ( $str, $state );
        if ( $result [0] === NULL )
          $result [0] = padMathNode ( 'mo', "\u{25A1}" );
        else
          padMathRemoveBrackets ( $result [0] );
        $str = $result [1];
        padMathRemoveBrackets ( $node );
        $node = padMathNode ( $symbol ['tag'], $node );
        $node ['c'] [] = $result [0];
        $frag ['c'] [] = $node;
        $symbol = padMathGetSymbol ( $str, $state );
      } elseif ( $node !== NULL )
        $frag ['c'] [] = $node;

    } while ( ( ( $symbol ['ttype'] != PAD_MATH_RIGHTBRACKET and ( $symbol ['ttype'] != PAD_MATH_LEFTRIGHT or $rightbracket ) )
                or $state ['depth'] == 0 )
              and $symbol ['output'] !== '' );

    if ( $symbol ['ttype'] == PAD_MATH_RIGHTBRACKET or $symbol ['ttype'] == PAD_MATH_LEFTRIGHT ) {

      $matrix = padMathDetectMatrix ( $frag ['c'], (string) $symbol ['output'] );

      if ( $matrix !== NULL )
        $frag ['c'] = [ padMathTable ( $matrix, ! empty ( $symbol ['invisible'] ) ) ];

      $str = padMathRemoveChars ( $str, strlen ( $symbol ['input'] ) );

      if ( empty ( $symbol ['invisible'] ) )
        $frag ['c'] [] = padMathToken ( $symbol );

    }

    return [ $frag, $str ];

  }

  // detectMatrix: whether a group is a table - rows that are each a bracketed list, ( ) or
  // [ ], all with the same brackets and as many entries, kept apart by commas. Not a table: a
  // single row of one entry (((a)) is double brackets), and rows in ( ) inside { }, a set of
  // pairs. A | between commas in the first row marks a line between two columns, as in the
  // augmented matrix [[1,2,|,3],[4,5,|,6]]. The answer is the cells of the rows and the columns
  // with a line, or NULL.

  function padMathDetectMatrix ( $kids, $end ) {

    $pairs = [ '(' => ')', '[' => ']' ];

    if ( ! $kids )
      return NULL;

    $rows      = [];
    $expecting = 'mrow';

    foreach ( $kids as $node ) {
      if ( $expecting == 'mrow' ) {
        if ( ! is_array ( $node ) or $node ['t'] != 'mrow' )
          return NULL;
        $rows []   = $node;
        $expecting = 'comma';
      } else {
        if ( ! is_array ( $node ) or $node ['t'] != 'mo' or padMathTrim ( padMathTextContent ( $node ) ) !== ',' )
          return NULL;
        $expecting = 'mrow';
      }
    }

    if ( $expecting != 'comma' )
      return NULL;

    $open = $close = $count = NULL;
    $out  = [];
    $lines = [];

    foreach ( $rows as $row ) {

      $cells = $row ['c'];

      if ( count ( $cells ) < 2 )
        return NULL;

      $first = $cells [0];
      $last  = $cells [ count ( $cells ) - 1 ];

      if ( ! is_array ( $first ) or $first ['t'] != 'mo' or ! is_array ( $last ) or $last ['t'] != 'mo' )
        return NULL;

      $rowOpen  = padMathTrim ( padMathTextContent ( $first ) );
      $rowClose = padMathTrim ( padMathTextContent ( $last ) );

      if ( ! isset ( $pairs [$rowOpen] ) or ( $rowOpen == '(' and $end == '}' ) or $rowClose !== $pairs [$rowOpen] )
        return NULL;

      $elements = 1;
      $cellsout = [];
      $current  = [];

      foreach ( array_slice ( $cells, 1, -1 ) as $cell ) {
        if ( is_array ( $cell ) and $cell ['t'] == 'mo' and padMathTrim ( padMathTextContent ( $cell ) ) === ',' ) {
          $at = count ( $cellsout );
          if ( count ( $current ) == 1 and is_array ( $current [0] ) and $current [0] ['t'] == 'mrow' and count ( $current [0] ['c'] ) == 1
               and in_array ( padMathTrim ( padMathTextContent ( $current [0] ) ), [ "\u{2223}", '|' ], TRUE ) ) {
            if ( $open === NULL )
              $lines [$at] = TRUE;
          } else
            unset ( $lines [$at] );
          $elements++;
          $cellsout [] = $current;
          $current     = [];
        } else
          $current [] = $cell;
      }

      $cellsout [] = $current;

      if ( $elements == 1 and $cellsout [0] and ( ( is_array ( $cellsout [0] [0] ) and $cellsout [0] [0] ['t'] == 'mtable' ) or count ( $rows ) == 1 ) )
        return NULL;

      $out [] = $cellsout;

      if ( $open === NULL ) {
        $open  = $rowOpen;
        $close = $rowClose;
        $count = $elements;
      } elseif ( $rowOpen !== $open or $rowClose !== $close or $elements !== $count )
        return NULL;

    }

    return [ $out, $lines ];

  }

  // The table of a group that is one: a cell per entry, the entries marked as a line left out
  // and the line drawn right of the cell before it - columnlines on the table, as the reference
  // writes it, for the browsers that read it, and the class pad-math-line on the cell for the
  // style. The rows of {: :} are aligned left (columnalign, and pad-math-left for the style).

  function padMathTable ( $matrix, $left ) {

    list ( $rows, $lines ) = $matrix;

    $table   = padMathNode ( 'mtable' );
    $columns = [];

    foreach ( $rows as $r => $cells ) {
      $row = padMathNode ( 'mtr' );
      foreach ( $cells as $c => $nodes ) {
        if ( isset ( $lines [$c] ) ) {
          if ( $r == 0 ) {
            array_pop ( $columns );
            $columns [] = 'solid';
          }
          if ( $c > 0 and $row ['c'] )
            $row ['c'] [ count ( $row ['c'] ) - 1 ] ['a'] ['class'] = 'pad-math-line';
        } else {
          $cell = padMathNode ( 'mtd' );
          $cell ['c'] = $nodes;
          $row ['c'] [] = $cell;
          if ( $r == 0 and $c < count ( $cells ) - 1 )
            $columns [] = 'none';
        }
      }
      $table ['c'] [] = $row;
    }

    if ( in_array ( 'solid', $columns, TRUE ) )
      $table ['a'] ['columnlines'] = implode ( ' ', $columns );

    if ( $left ) {
      $table ['a'] ['columnalign'] = 'left';
      $table ['a'] ['class']       = 'pad-math-left';
    }

    return $table;

  }

  // A text made safe for an element or an attribute.

  function padMathEscape ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The markup of a node. The menclose of cancel is the row the style strikes through, an
  // mstyle without a colour a plain row.
  //
  // The spacing of function names, which MathML Core leaves to the markup: a name and its
  // argument - sin x, which the parser grouped, or lim_(x->0) f(x) and dim V, which it leaves
  // side by side - get the invisible function application U+2061 between them and a thin
  // space when the argument has no brackets of its own (sin x, not sinx; sin(x) as it is), and
  // a name after a letter or a number a thin space before it (2 sin x), as TeX spaces them -
  // not inside a script or a fraction, whose children have places, not neighbours.

  function padMathXml ( $node ) {

    if ( is_string ( $node ) )
      return padMathEscape ( $node );

    $kids   = $node ['c'];
    $apply  = ! empty ( $node ['apply'] );
    $row    = in_array ( $node ['t'], [ '', 'mrow', 'menclose', 'mstyle', 'mtd', 'msqrt' ], TRUE );
    $thin   = '<mspace width="0.1667em"/>';
    $inner  = '';
    $spaced = FALSE;

    foreach ( $kids as $i => $kid ) {

      if ( $apply and $i == 1 ) {
        if ( padMathValue ( $kid ) !== '' )
          $inner .= "<mo>\u{2061}</mo>" . ( padMathFenced ( $kid ) ? '' : $thin );
      } elseif ( $row and $i > 0 and ! $spaced and padMathHead ( $kid ) and padMathLoose ( $kids [ $i - 1 ] ) )
        $inner .= $thin;

      $inner .= padMathXml ( $kid );
      $spaced = FALSE;

      if ( $row and ! ( $apply and $i == 0 ) and isset ( $kids [ $i + 1 ] ) and padMathWord ( $kid ) and padMathLoose ( $kids [ $i + 1 ] )
           and padMathValue ( $kids [ $i + 1 ] ) !== '' and ! padMathHead ( $kids [ $i + 1 ] ) ) {
        $inner .= "<mo>\u{2061}</mo>" . ( padMathFenced ( $kids [ $i + 1 ] ) ? '' : $thin );
        $spaced = TRUE;
      }

    }

    $tag   = $node ['t'];
    $attrs = $node ['a'];

    if ( $tag === '' )
      return $inner;

    if ( $tag == 'menclose' ) {
      $tag   = 'mrow';
      $attrs = [ 'class' => trim ( 'pad-math-cancel ' . ( $attrs ['class'] ?? '' ) ) ] + $attrs;
    } elseif ( $tag == 'mstyle' and ! $attrs )
      $tag = 'mrow';

    $open = '<' . $tag;

    foreach ( $attrs as $name => $value )
      $open .= ' ' . $name . '="' . padMathEscape ( $value ) . '"';

    if ( $inner === '' and $tag == 'mspace' )
      return $open . '/>';

    return $open . '>' . $inner . '</' . $tag . '>';

  }

  // A function name, with or without its scripts - sin, lim_(x->0), log_2, sin^2.

  function padMathWord ( $node ) {

    if ( ! is_array ( $node ) )
      return FALSE;

    if ( ! empty ( $node ['word'] ) )
      return TRUE;

    if ( in_array ( $node ['t'], [ 'msub', 'msup', 'msubsup', 'munder', 'mover', 'munderover' ], TRUE )
         or ( $node ['t'] == 'mrow' and count ( $node ['c'] ) == 1 ) )
      return isset ( $node ['c'] [0] ) and padMathWord ( $node ['c'] [0] );

    return FALSE;

  }

  // A function name, alone or as the head of the group of a name and its argument.

  function padMathHead ( $node ) {

    return padMathWord ( $node ) or ( ! empty ( $node ['apply'] ) and padMathWord ( $node ['c'] [0] ?? NULL ) );

  }

  // Whether a neighbour of a function name is spaced from it: not an operator, a bracket or a
  // space, which have spacing of their own.

  function padMathLoose ( $node ) {

    return is_array ( $node ) and ! in_array ( $node ['t'], [ 'mo', 'mspace' ], TRUE );

  }

  // Whether an argument starts with a bracket of its own: sin(x) and lim (1+1/n)^n need no
  // space after the name.

  function padMathFenced ( $node ) {

    if ( is_array ( $node ) and in_array ( $node ['t'], [ 'msub', 'msup', 'msubsup' ], TRUE ) )
      return padMathFenced ( $node ['c'] [0] ?? NULL );

    return is_array ( $node ) and $node ['t'] == 'mrow' and isset ( $node ['c'] [0] ) and is_array ( $node ['c'] [0] )
       and $node ['c'] [0] ['t'] == 'mo'
       and in_array ( padMathValue ( $node ['c'] [0] ), [ '(', '[', '{', '|', "\u{2016}", "\u{2329}", "\u{230A}", "\u{2308}" ], TRUE );

  }

?>
