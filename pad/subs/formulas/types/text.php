<?php

  // text - TEXT(value, format) is a number written with a format code - #,##0.00, 0%, 0.00E+00,
  // yyyy-mm-dd, dddd d mmmm, h:mm AM/PM - as the formats= of {pad:spreadsheet} writes a column; a text
  // that reads as a number is that number.
  //
  //   =TEXT(1234.5, "#,##0.00")       1,234.50
  //   =TEXT(0.256, "0.0%")            25.6%
  //   =TEXT(D2, "dddd d mmmm yyyy")   Monday 15 January 2024
  //   =TEXT(0.75, "h:mm AM/PM")       6:00 PM

  return [
    'group' => 'Text',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $value  = padFormulaScalar ( $args [0], $env );
      $format = padFormulaArgText ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $value, $format ) )
        return $error;
      if ( is_string ( $value ) and ( $number = padFormulaParseValue ( $value ) ) !== NULL )
        $value = $number;
      $text = padFormulaFormat ( $value, $format );
      return $text === '#VALUE!' ? padFormulaError ( '#VALUE!' ) : $text;
    }
  ];

?>
