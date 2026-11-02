<?php

  // ca - Canada: the ten general holidays of the Canada Labour Code, the federal ones, their
  // names in English and French - lang= picks one, English the default. Victoria Day is the
  // Monday before 25 May; Canada Day is 2 July when 1 July is a Sunday (the Holidays Act); the
  // National Day for Truth and Reconciliation, 30 September, is held since 2021. A holiday on
  // a day off is made up for by the next working day of each employee, no set date, so no
  // substitute is given. The provinces' own holidays - Family Day, the Civic Holiday, the
  // Fête nationale du Québec - are left out.

  return [
    'name'  => 'Canada',
    'langs' => [ 'en', 'fr' ],
    'days'  => [
      [ 'date'       => '01-01',           'name' => "New Year's Day",  'local' => [ 'en' => "New Year's Day",  'fr' => "Jour de l'An" ] ],
      [ 'easter'     => -2,                'name' => 'Good Friday',     'local' => [ 'en' => 'Good Friday',     'fr' => 'Vendredi saint' ] ],
      [ 'onOrBefore' => [ 'mon', '05-24' ], 'name' => 'Victoria Day',   'local' => [ 'en' => 'Victoria Day',    'fr' => 'Fête de la Reine' ] ],
      [ 'date'       => '07-01',           'name' => 'Canada Day',      'local' => [ 'en' => 'Canada Day',      'fr' => 'Fête du Canada' ], 'sunday' => 1 ],
      [ 'nth'        => [ 1, 'mon', 9 ],   'name' => 'Labour Day',      'local' => [ 'en' => 'Labour Day',      'fr' => 'Fête du Travail' ] ],
      [ 'date'       => '09-30',           'name' => 'National Day for Truth and Reconciliation',
                                           'local' => [ 'en' => 'National Day for Truth and Reconciliation', 'fr' => 'Journée nationale de la vérité et de la réconciliation' ], 'from' => 2021 ],
      [ 'nth'        => [ 2, 'mon', 10 ],  'name' => 'Thanksgiving',    'local' => [ 'en' => 'Thanksgiving',    'fr' => "Action de grâce" ] ],
      [ 'date'       => '11-11',           'name' => 'Remembrance Day', 'local' => [ 'en' => 'Remembrance Day', 'fr' => 'Jour du Souvenir' ] ],
      [ 'date'       => '12-25',           'name' => 'Christmas Day',   'local' => [ 'en' => 'Christmas Day',   'fr' => 'Noël' ] ],
      [ 'date'       => '12-26',           'name' => 'Boxing Day',      'local' => [ 'en' => 'Boxing Day',      'fr' => 'Lendemain de Noël' ] ]
    ]
  ];

?>
