<?php

  // chem - chemical formulas and equations, typed as chemists type them, as MathML: H2SO4 with
  // its counts below the line, Fe^3+, SO4^2-, NH4+ and e- with their charges above, 2H2O with its
  // coefficient, states (s) (l) (g) (aq), hydrates CuSO4*5H2O, isotopes ^14C and ^235_92U, and
  // the arrows of an equation -> <- <-> <=> with what stands over them, ->[Delta]. Every
  // element is checked against the periodic table. display='block' sets the equation apart
  // on its own line, alttext= is the text a screen reader without MathML says.
  //
  //   {notation:chem 'CH4 + 2O2 -> CO2 + 2H2O'/}
  //   {notation:chem}[Cu(NH3)4]^2+{/notation:chem}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padChemBlock    = ( padNotationChoice ( 'display', 'inline', [ 'inline', 'block' ] ) == 'block' );
  $padChemProblems = [];
  $padChemHtml     = padChemMl ( $padNotationText, $padChemBlock, padNotationOption ( 'alttext' ), $padChemProblems );

  if ( $padChemProblems and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . ' ' . $padChemProblems [0] );

  return ( $padChemHtml === '' ) ? '' : padNotationWrap ( padNotationStyle ( 'chem', [] ) . $padChemHtml );

?>
