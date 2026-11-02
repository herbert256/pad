<?php

  // time - TIME(hour, minute, second) is a time of day as the fraction of a day a spreadsheet keeps
  // it as - 0.5 is noon - shown as hh:mm. Each part is cut to a whole number toward 0 and may run
  // over: 27 hours is 3:00 the next day, of which only the time is kept, 750 minutes 12:30. A part
  // past 32767, or a time below 0 in all - TIME(0, -1, 0) - is #NUM!.
  //
  //   =TIME(16, 48, 10)*1           0.700115741
  //   =TIME(27, 0, 0)               03:00
  //   =TIME(0, 750, 0)              12:30
  //   =TIME(1, -30, 0)              00:30
  //   =TIME(0, -1, 0)               #NUM!

  return [
    'group'  => 'Date and time',
    'args'   => [ 3, 3 ],
    'format' => 'hh:mm',
    'call'   => function ( $args, $env ) {
      $hour   = padFormulaArgInt ( $args [0], $env );
      $minute = padFormulaArgInt ( $args [1], $env );
      $second = padFormulaArgInt ( $args [2], $env );
      if ( $error = padFormulaFirstError ( $hour, $minute, $second ) )
        return $error;
      if ( max ( $hour, $minute, $second ) > 32767 )
        return padFormulaError ( '#NUM!' );
      $total = $hour * 3600 + $minute * 60 + $second;
      return $total < 0 ? padFormulaError ( '#NUM!' ) : ( $total % 86400 ) / 86400;
    }
  ];

?>
