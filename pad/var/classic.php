<?php

  if ( ! padValidVar2 ($padFld))
    return padIgnore ( "Field '$padFld' not a valid name" );
 
  if ( ! in_array('noError', $padOpts) )
    if     ( $padFirst == '!' ) { if ( ! padRawCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '$' ) { if ( ! padFieldCheck ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '#' ) { if ( ! padOptCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '&' ) { if ( ! padTagCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }

  if     ( $padFirst == '!' ) return padRawValue   ($padFld);
  elseif ( $padFirst == '$' ) return padFieldValue ($padFld);
  elseif ( $padFirst == '#' ) return padOptValue   ($padFld);
  elseif ( $padFirst == '&' ) return padTagValue   ($padFld);

?>