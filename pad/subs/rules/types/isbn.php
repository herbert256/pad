<?php
  return [
    'check' => function ( $value ) {
      $value = str_replace ( [ ' ', '-' ], '', $value );
      if ( preg_match ( '/^[0-9]{9}[0-9X]$/D', $value ) ) {
        $sum = 0;
        for ( $i = 0; $i < 10; $i++ )
          $sum += ( 10 - $i ) * ( $value [$i] === 'X' ? 10 : (int) $value [$i] );
        return $sum % 11 === 0;
      }
      if ( ! preg_match ( '/^97[89][0-9]{10}$/D', $value ) ) return FALSE;
      $sum = 0;
      for ( $i = 0; $i < 13; $i++ ) $sum += (int) $value [$i] * ( $i % 2 ? 3 : 1 );
      return $sum % 10 === 0;
    },
    'message' => ':label must be a valid ISBN-10 or ISBN-13'
  ];
?>
