<?php

  // fr - France: the eleven jours fériés of the Code du travail, exact since 1982, when 8 May
  // became a holiday again. Whit Monday is the journée de solidarité many work, still a
  // holiday by law. With region=, by the department's number as ISO 3166-2 writes it: the
  // three departments of Alsace-Moselle (57, 67, 68) add Good Friday and St Stephen's Day,
  // Guadeloupe (971), Martinique (972), Guyane (973), La Réunion (974) and Mayotte (976) the
  // day of the abolition of slavery. No substitute for a holiday on a Sunday.

  $alsaceMoselle = [ '57', '67', '68' ];

  return [
    'name'    => 'France',
    'langs'   => [ 'fr' ],
    'regions' => [ '57' => 'Moselle', '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '971' => 'Guadeloupe',
                   '972' => 'Martinique', '973' => 'Guyane', '974' => 'La Réunion', '976' => 'Mayotte' ],
    'days'    => [
      [ 'date'   => '01-01', 'name' => "New Year's Day",                'local' => "Jour de l'an" ],
      [ 'easter' => -2,      'name' => 'Good Friday',                   'local' => 'Vendredi saint', 'in' => $alsaceMoselle ],
      [ 'easter' => 1,       'name' => 'Easter Monday',                 'local' => 'Lundi de Pâques' ],
      [ 'date'   => '04-27', 'name' => 'Abolition of Slavery',          'local' => "Abolition de l'esclavage", 'in' => [ '976' ] ],
      [ 'date'   => '05-01', 'name' => 'Labour Day',                    'local' => 'Fête du Travail' ],
      [ 'date'   => '05-08', 'name' => 'Victory in Europe Day',         'local' => 'Victoire 1945', 'from' => 1982 ],
      [ 'date'   => '05-22', 'name' => 'Abolition of Slavery',          'local' => "Abolition de l'esclavage", 'in' => [ '972' ] ],
      [ 'date'   => '05-27', 'name' => 'Abolition of Slavery',          'local' => "Abolition de l'esclavage", 'in' => [ '971' ] ],
      [ 'easter' => 39,      'name' => 'Ascension Day',                 'local' => 'Ascension' ],
      [ 'easter' => 50,      'name' => 'Whit Monday',                   'local' => 'Lundi de Pentecôte' ],
      [ 'date'   => '06-10', 'name' => 'Abolition of Slavery',          'local' => "Abolition de l'esclavage", 'in' => [ '973' ] ],
      [ 'date'   => '07-14', 'name' => 'Bastille Day',                  'local' => 'Fête nationale' ],
      [ 'date'   => '08-15', 'name' => 'Assumption Day',                'local' => 'Assomption' ],
      [ 'date'   => '11-01', 'name' => "All Saints' Day",               'local' => 'Toussaint' ],
      [ 'date'   => '11-11', 'name' => 'Armistice Day',                 'local' => 'Armistice 1918' ],
      [ 'date'   => '12-20', 'name' => 'Abolition of Slavery',          'local' => "Abolition de l'esclavage", 'in' => [ '974' ] ],
      [ 'date'   => '12-25', 'name' => 'Christmas Day',                 'local' => 'Noël' ],
      [ 'date'   => '12-26', 'name' => "St Stephen's Day",              'local' => 'Saint-Étienne', 'in' => $alsaceMoselle ]
    ]
  ];

?>
