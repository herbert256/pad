<?php

  // es - Spain: the national holidays (fiestas de ámbito nacional, Real Decreto 2001/1983):
  // New Year's Day, Good Friday, Labour Day, Assumption Day, the National Day, All Saints'
  // Day, Constitution Day, the Immaculate Conception and Christmas Day, and Epiphany, which a
  // community may replace but every one keeps. Maundy Thursday, St Joseph's Day, Santiago and
  // the Monday after a holiday on a Sunday are chosen by each community, year by year, in its
  // own decree - they and the communities are left out, as are the local fiestas.

  return [
    'name'  => 'Spain',
    'langs' => [ 'es' ],
    'days'  => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",        'local' => 'Año Nuevo' ],
      [ 'date'   => '01-06', 'name' => 'Epiphany',              'local' => 'Epifanía del Señor' ],
      [ 'easter' => -2,      'name' => 'Good Friday',           'local' => 'Viernes Santo' ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',            'local' => 'Fiesta del Trabajo' ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',        'local' => 'Asunción de la Virgen' ],
      [ 'date'   => '10-12', 'name' => 'National Day of Spain', 'local' => 'Fiesta Nacional de España' ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",       'local' => 'Todos los Santos' ],
      [ 'date'   => '12-06', 'name' => 'Constitution Day',      'local' => 'Día de la Constitución Española', 'from' => 1979 ],
      [ 'date'   => '12-08', 'name' => 'Immaculate Conception', 'local' => 'Inmaculada Concepción' ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',         'local' => 'Natividad del Señor' ]
    ]
  ];

?>
