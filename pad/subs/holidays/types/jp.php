<?php

  // jp - Japan: the national holidays of the Act on National Holidays (国民の祝日に関する法律)
  // and the one-off holidays of their own acts, with its two rules of days off: a holiday on
  // a Sunday is made up for on the next day that is no holiday yet (振替休日, since 12 April
  // 1973 - the next day until 2006, when no two holidays followed each other, so the same
  // days), and a day between two holidays is one too (国民の休日, since 1986: 4 May from 1988
  // to 2006, and 22 September when Respect for the Aged Day falls on the 21st). The Happy
  // Monday moves of 2000 and 2003, the holidays of the emperors - the birthday on 29 April,
  // 23 December, 23 February, 2019 without one - and the Olympic moves of 2020 and 2021 are
  // rules of their years. The equinox days are fixed each February for the next year by the
  // National Astronomical Observatory's almanac; here they are the common formula of the
  // Japan Coast Guard's calendar reckoning (1900 to 2099), which gives every equinox day the
  // Cabinet Office lists, so the file knows the years from 1955 to 2099 - a date after 2027
  // is the formula's, which the almanac has yet to confirm. Checked by a script against the
  // Cabinet Office's list of every national holiday and day off since 1955 (syukujitsu.csv):
  // all 1067 dates from 1955 to 2027 agree, and every equinox day from 1955 to 2099 is the
  // day of the equinox in Japan Standard Time by Meeus' algorithm - the closest call the
  // autumn of 2074, three minutes after midnight.

  // An equinox day: the day of March (20.8431) or September (23.2488) of the year, with the
  // constants before 1980 (20.8357, 23.2588) counted from 1983.

  $equinox = fn ( $year, $old, $new ) => (int) floor ( ( $year < 1980 ? $old : $new ) + 0.242194 * ( $year - 1980 )
                                                       - intdiv ( $year - ( $year < 1980 ? 1983 : 1980 ), 4 ) );

  return [
    'name'           => 'Japan',
    'langs'          => [ 'ja' ],
    'substitute'     => 'sundayNext',
    'suffix'         => ' (substitute holiday)',
    'suffixLocal'    => '（振替休日）',
    'substituteFrom' => '1973-04-12',
    'between'        => [ 'from' => 1986, 'name' => "Citizens' Holiday", 'local' => '国民の休日' ],
    'years'          => [ 1955, 2099 ],
    'days'           => [
      [ 'date'   => '01-01',           'name' => "New Year's Day",            'local' => '元日', 'substitute' => TRUE ],
      [ 'date'   => '01-15',           'name' => 'Coming of Age Day',         'local' => '成人の日', 'substitute' => TRUE, 'to' => 1999 ],
      [ 'nth'    => [ 2, 'mon', 1 ],   'name' => 'Coming of Age Day',         'local' => '成人の日', 'from' => 2000 ],
      [ 'date'   => '02-11',           'name' => 'National Foundation Day',   'local' => '建国記念の日', 'substitute' => TRUE, 'from' => 1967 ],
      [ 'date'   => '02-23',           'name' => "The Emperor's Birthday",    'local' => '天皇誕生日', 'substitute' => TRUE, 'from' => 2020 ],
      [ 'date'   => '02-24',           'name' => 'Funeral of Emperor Showa',  'local' => '昭和天皇の大喪の礼', 'from' => 1989, 'to' => 1989 ],
      [ 'calc'   => fn ( $year ) => sprintf ( '%04d-03-%02d', $year, $equinox ( $year, 20.8357, 20.8431 ) ),
                                       'name' => 'Vernal Equinox Day',        'local' => '春分の日', 'substitute' => TRUE ],
      [ 'date'   => '04-10',           'name' => 'Wedding of Crown Prince Akihito', 'local' => '皇太子明仁親王の結婚の儀', 'from' => 1959, 'to' => 1959 ],
      [ 'date'   => '04-29',           'name' => "The Emperor's Birthday",    'local' => '天皇誕生日', 'substitute' => TRUE, 'to' => 1988 ],
      [ 'date'   => '04-29',           'name' => 'Greenery Day',              'local' => 'みどりの日', 'substitute' => TRUE, 'from' => 1989, 'to' => 2006 ],
      [ 'date'   => '04-29',           'name' => 'Showa Day',                 'local' => '昭和の日', 'substitute' => TRUE, 'from' => 2007 ],
      [ 'date'   => '05-01',           'name' => 'Enthronement Day',          'local' => '天皇の即位の日', 'from' => 2019, 'to' => 2019 ],
      [ 'date'   => '05-03',           'name' => 'Constitution Memorial Day', 'local' => '憲法記念日', 'substitute' => TRUE ],
      [ 'date'   => '05-04',           'name' => 'Greenery Day',              'local' => 'みどりの日', 'substitute' => TRUE, 'from' => 2007 ],
      [ 'date'   => '05-05',           'name' => "Children's Day",            'local' => 'こどもの日', 'substitute' => TRUE ],
      [ 'date'   => '06-09',           'name' => 'Wedding of Crown Prince Naruhito', 'local' => '皇太子徳仁親王の結婚の儀', 'from' => 1993, 'to' => 1993 ],
      [ 'date'   => '07-20',           'name' => 'Marine Day',                'local' => '海の日', 'substitute' => TRUE, 'from' => 1996, 'to' => 2002 ],
      [ 'nth'    => [ 3, 'mon', 7 ],   'name' => 'Marine Day',                'local' => '海の日', 'from' => 2003, 'moved' => [ 2020 => '07-23', 2021 => '07-22' ] ],
      [ 'nth'    => [ 2, 'mon', 10 ],  'name' => 'Sports Day',                'local' => 'スポーツの日', 'from' => 2020, 'moved' => [ 2020 => '07-24', 2021 => '07-23' ] ],
      [ 'date'   => '08-11',           'name' => 'Mountain Day',              'local' => '山の日', 'substitute' => TRUE, 'from' => 2016, 'moved' => [ 2020 => '08-10', 2021 => '08-08' ] ],
      [ 'date'   => '09-15',           'name' => 'Respect for the Aged Day',  'local' => '敬老の日', 'substitute' => TRUE, 'from' => 1966, 'to' => 2002 ],
      [ 'nth'    => [ 3, 'mon', 9 ],   'name' => 'Respect for the Aged Day',  'local' => '敬老の日', 'from' => 2003 ],
      [ 'calc'   => fn ( $year ) => sprintf ( '%04d-09-%02d', $year, $equinox ( $year, 23.2588, 23.2488 ) ),
                                       'name' => 'Autumnal Equinox Day',      'local' => '秋分の日', 'substitute' => TRUE ],
      [ 'date'   => '10-10',           'name' => 'Health and Sports Day',     'local' => '体育の日', 'substitute' => TRUE, 'from' => 1966, 'to' => 1999 ],
      [ 'nth'    => [ 2, 'mon', 10 ],  'name' => 'Health and Sports Day',     'local' => '体育の日', 'from' => 2000, 'to' => 2019 ],
      [ 'date'   => '10-22',           'name' => 'Enthronement Ceremony',     'local' => '即位礼正殿の儀', 'from' => 2019, 'to' => 2019 ],
      [ 'date'   => '11-03',           'name' => 'Culture Day',               'local' => '文化の日', 'substitute' => TRUE ],
      [ 'date'   => '11-12',           'name' => 'Enthronement Ceremony',     'local' => '即位礼正殿の儀', 'from' => 1990, 'to' => 1990 ],
      [ 'date'   => '11-23',           'name' => 'Labour Thanksgiving Day',   'local' => '勤労感謝の日', 'substitute' => TRUE ],
      [ 'date'   => '12-23',           'name' => "The Emperor's Birthday",    'local' => '天皇誕生日', 'substitute' => TRUE, 'from' => 1989, 'to' => 2018 ]
    ]
  ];

?>
