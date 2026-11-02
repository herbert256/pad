<?php

  // mx - Mexico: the days of obligatory rest of the Ley Federal del Trabajo, art. 74, as the
  // decree of 17 January 2006 made them - Constitution Day on the first Monday of February,
  // Benito Juárez's birthday on the third Monday of March (from 2007, its second transitory
  // article: 21 March in the bicentenary year 2006) and Revolution Day on the third Monday of
  // November - with the transmission of the federal executive power every six years, on
  // 1 December until 2018 and on 1 October since 2024 (the reform of fraction VII, DOF
  // 27 September 2024). The election days of fraction IX, the Sunday of an ordinary federal
  // or local election, and the holidays of the states are left out; Holy Thursday and Good
  // Friday are no days of rest by law. The file knows the years since 2007. Checked against
  // the decree of 2006 as the Orden Jurídico Nacional publishes it, the reform of 2024, and
  // the dates of 2007, 2012, 2018, 2024 and 2026 (2 February, 16 March, 16 November 2026).

  return [
    'name'  => 'Mexico',
    'langs' => [ 'es' ],
    'years' => [ 2007, padHolidaysLast ],
    'days'  => [
      [ 'date'   => '01-01',           'name' => "New Year's Day",          'local' => 'Año Nuevo' ],
      [ 'nth'    => [ 1, 'mon', 2 ],   'name' => 'Constitution Day',        'local' => 'Día de la Constitución' ],
      [ 'nth'    => [ 3, 'mon', 3 ],   'name' => "Benito Juárez's Birthday", 'local' => 'Natalicio de Benito Juárez' ],
      [ 'date'   => '05-01',           'name' => 'Labour Day',              'local' => 'Día del Trabajo' ],
      [ 'date'   => '09-16',           'name' => 'Independence Day',        'local' => 'Día de la Independencia' ],
      [ 'calc'   => fn ( $year ) => ( $year - 2024 ) % 6 == 0 ? "$year-10-01" : NULL, 'from' => 2024,
                                       'name' => 'Inauguration Day',        'local' => 'Transmisión del Poder Ejecutivo Federal' ],
      [ 'nth'    => [ 3, 'mon', 11 ],  'name' => 'Revolution Day',          'local' => 'Día de la Revolución' ],
      [ 'calc'   => fn ( $year ) => ( $year - 2018 ) % 6 == 0 ? "$year-12-01" : NULL, 'to' => 2018,
                                       'name' => 'Inauguration Day',        'local' => 'Transmisión del Poder Ejecutivo Federal' ],
      [ 'date'   => '12-25',           'name' => 'Christmas Day',           'local' => 'Navidad' ]
    ]
  ];

?>
