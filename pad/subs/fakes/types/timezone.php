<?php

  // timezone - A time zone by its name in the IANA time zone database, Europe/Paris or
  // America/Chicago - zones of the cities and countries the other kinds give and a few more,
  // each a zone of its own in the database's zone1970.tab, none kept only as a link to
  // another since (Europe/Amsterdam has been Europe/Brussels' since 2022, Asia/Calcutta
  // Asia/Kolkata's) - and UTC, so DateTimeZone takes every one and lists it among its
  // identifiers. For the zone of a user in a seeder: padFake ( 'timezone' ).

  return padFakePick ( [ 'Africa/Cairo', 'Africa/Johannesburg', 'Africa/Lagos', 'Africa/Nairobi',
                         'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'America/Mexico_City',
                         'America/New_York', 'America/Sao_Paulo', 'America/Toronto', 'Asia/Dubai', 'Asia/Kolkata',
                         'Asia/Shanghai', 'Asia/Singapore', 'Asia/Tokyo', 'Australia/Melbourne', 'Australia/Sydney',
                         'Europe/Athens', 'Europe/Berlin', 'Europe/Brussels', 'Europe/Dublin', 'Europe/Helsinki',
                         'Europe/Lisbon', 'Europe/London', 'Europe/Madrid', 'Europe/Paris', 'Europe/Prague',
                         'Europe/Rome', 'Europe/Vienna', 'Europe/Warsaw', 'Europe/Zurich', 'Pacific/Auckland', 'UTC' ] );

?>
