<?php
  // maxicode - hexagonal MaxiCode modes 2/3 structured and 4/5 unstructured.
  include_once PAD . 'subs/codes/_lib/maxicode.php';
  $maxiText = padCodeText ( FALSE ); $maxiOptions = [ 'mode' => padTagParm ( 'mode', 4 ), 'encoding' => padTagParm ( 'encoding', 'ISO-8859-1' ) ];
  foreach ( [ 'postcode', 'country', 'service' ] as $name ) { $value = padTagParm ( $name, NULL ); if ( $value !== NULL ) $maxiOptions [$name] = $value; }
  foreach ( [ 'mode', 'country', 'service' ] as $name ) if ( isset ( $maxiOptions [$name] ) && is_numeric ( $maxiOptions [$name] ) && is_finite ( (float) $maxiOptions [$name] ) && floor ( (float) $maxiOptions [$name] ) == $maxiOptions [$name] ) $maxiOptions [$name] = (int) $maxiOptions [$name];
  $maxi = padMaxicodeEncode ( $maxiText, $maxiOptions );
  if ( ! is_array ( $maxi ) ) return padCodeFail ( $maxi );
  return padMaxicodeSvg ( $maxi, padTagParm ( 'size', 240 ), padParmText ( padTagParm ( 'title', $maxiText ) ), (string) padTagParm ( 'color', '#000' ), (string) padTagParm ( 'background', '#fff' ) );
?>
