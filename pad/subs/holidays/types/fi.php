<?php

  // fi - Finland: the church holidays of the Church Act - New Year's Day, Epiphany, Good
  // Friday, Easter Sunday and Monday, Ascension Day, Whit Sunday, Midsummer Day (the Saturday
  // from 20 to 26 June), All Saints' Day (the Saturday from 31 October to 6 November),
  // Christmas Day and Boxing Day - with May Day and Independence Day, the paid public holidays
  // the University of Helsinki Almanac Office lists; Midsummer Eve and Christmas Eve are days
  // off by many collective agreements, not by law - observances. Names in Finnish and
  // Swedish (lang='sv'). The rules hold since 1992, when Epiphany and Ascension Day came back
  // from the Saturdays they were moved to from 1973 - the years the file knows. With region
  // '01', Åland: Autonomy Day, 9 June, a day off for its regional and municipal civil service
  // only (Landskapslag 1976:26) - an observance. Checked against the Almanac Office's
  // "Flagging and public holidays" and its flag days of 2026 and 2027, and the church's
  // 2025 dates (Midsummer Day 21 June, All Saints' Day 1 November, Whit Sunday 8 June).

  return [
    'name'    => 'Finland',
    'langs'   => [ 'fi', 'sv' ],
    'regions' => [ '01' => 'Åland' ],
    'years'   => [ 1992, padHolidaysLast ],
    'days'    => [
      [ 'date'      => '01-01',            'name' => "New Year's Day",      'local' => [ 'fi' => 'Uudenvuodenpäivä',     'sv' => 'Nyårsdagen' ] ],
      [ 'date'      => '01-06',            'name' => 'Epiphany',            'local' => [ 'fi' => 'Loppiainen',           'sv' => 'Trettondagen' ] ],
      [ 'easter'    => -2,                 'name' => 'Good Friday',         'local' => [ 'fi' => 'Pitkäperjantai',       'sv' => 'Långfredagen' ] ],
      [ 'easter'    => 0,                  'name' => 'Easter Sunday',       'local' => [ 'fi' => 'Pääsiäispäivä',        'sv' => 'Påskdagen' ] ],
      [ 'easter'    => 1,                  'name' => 'Easter Monday',       'local' => [ 'fi' => 'Toinen pääsiäispäivä', 'sv' => 'Annandag påsk' ] ],
      [ 'date'      => '05-01',            'name' => 'May Day',             'local' => [ 'fi' => 'Vappu',                'sv' => 'Första maj' ] ],
      [ 'easter'    => 39,                 'name' => 'Ascension Day',       'local' => [ 'fi' => 'Helatorstai',          'sv' => 'Kristi himmelsfärdsdag' ] ],
      [ 'easter'    => 49,                 'name' => 'Whit Sunday',         'local' => [ 'fi' => 'Helluntaipäivä',       'sv' => 'Pingstdagen' ] ],
      [ 'date'      => '06-09',            'name' => "Åland's Autonomy Day", 'local' => [ 'fi' => 'Ahvenanmaan itsehallintopäivä', 'sv' => 'Ålands självstyrelsedag' ],
                                             'type' => 'observance', 'in' => [ '01' ] ],
      [ 'onOrAfter' => [ 'fri', '06-19' ], 'name' => 'Midsummer Eve',       'local' => [ 'fi' => 'Juhannusaatto',        'sv' => 'Midsommarafton' ], 'type' => 'observance' ],
      [ 'onOrAfter' => [ 'sat', '06-20' ], 'name' => 'Midsummer Day',       'local' => [ 'fi' => 'Juhannuspäivä',        'sv' => 'Midsommardagen' ] ],
      [ 'onOrAfter' => [ 'sat', '10-31' ], 'name' => "All Saints' Day",     'local' => [ 'fi' => 'Pyhäinpäivä',          'sv' => 'Alla helgons dag' ] ],
      [ 'date'      => '12-06',            'name' => 'Independence Day',    'local' => [ 'fi' => 'Itsenäisyyspäivä',     'sv' => 'Självständighetsdagen' ] ],
      [ 'date'      => '12-24',            'name' => 'Christmas Eve',       'local' => [ 'fi' => 'Jouluaatto',           'sv' => 'Julafton' ], 'type' => 'observance' ],
      [ 'date'      => '12-25',            'name' => 'Christmas Day',       'local' => [ 'fi' => 'Joulupäivä',           'sv' => 'Juldagen' ] ],
      [ 'date'      => '12-26',            'name' => 'Boxing Day',          'local' => [ 'fi' => 'Tapaninpäivä',         'sv' => 'Annandag jul' ] ]
    ]
  ];

?>
