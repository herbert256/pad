<?php
  function padTextAnalysis ( $kind, $textOrRows, $options = [] ) {
    include_once PAD . 'subs/textanalysis/_lib/textanalysis.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'textanalysis', $kind ) || ! is_array ( $options ) ) throw new InvalidArgumentException ( 'unknown kind or invalid options' );
      $definition = include PAD . "subs/textanalysis/types/$kind.php";
      foreach ( $options as $key => $value )
        if ( ! in_array ( $key, $definition ['options'], TRUE ) ) throw new InvalidArgumentException ( 'unknown option: ' . $key );
      $normalization = $options ['normalization'] ?? 'nfc'; $caseFold = $options ['caseFold'] ?? TRUE;
      if ( ! in_array ( $normalization, [ 'none', 'nfc' ], TRUE ) || ! is_bool ( $caseFold ) || ( $options ['locale'] ?? 'und' ) !== 'und' )
        throw new InvalidArgumentException ( 'normalization is nfc or none, caseFold is boolean, locale is und (locale-independent)' );
      if ( $normalization === 'nfc' && ! class_exists ( 'Normalizer' ) ) throw new InvalidArgumentException ( 'NFC normalization requires PHP intl; select normalization=none explicitly otherwise' );
      $meta = [ 'normalization' => $normalization, 'caseFold' => $caseFold, 'locale' => 'und', 'tokenization' => 'Unicode letters and numbers, combining marks and internal apostrophes' ];
      return $definition ['call'] ( $textOrRows, $options ) + [ 'meta' => $meta ];
    } catch ( InvalidArgumentException $e ) { padError ( 'padTextAnalysis: ' . $e->getMessage () ); return NULL; }
  }
?>
