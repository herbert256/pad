<?php

  $tagAbout   = 'The public holidays of a country - twenty-one of them, each a file of rules - as rows or a table, its regions too.';
  $tagGroup   = 'values';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{holidays 'nl', year=2026}{$date} {$name} {$local}{/holidays}
{holidays 'de-by', year=2026/}
{holidays 'gb', region='sct', from='today', to='+6 months', lang='en'}...{/holidays}
PAD;

  $tagParms   = [
    'country' => 'The ISO 3166-1 alpha-2 code of the country - at, be, ca, ch, cz, de, dk, es, fi, fr, gb, ie, it, lu, nl, no, nz, pl, pt, se, us - with a region after it as ISO 3166-2 writes it: <code>de-by</code>.' ];

  $tagOptions = [
    'region' => 'The region apart - <code>region=\'by\'</code>: Germany\'s states, the United Kingdom\'s nations (<code>sct</code>, <code>nir</code>), France\'s Alsace-Moselle and overseas departments, Finland\'s Åland (<code>01</code>), Portugal\'s Azores and Madeira (<code>20</code>, <code>30</code>), New Zealand\'s provinces (<code>auk</code>, <code>can</code> ...).',
    'year'   => 'The year, 1583 to 9999 - this year by default.',
    'from'   => 'With <code>to</code>: the days between two dates instead of a year - <code>today</code>, <code>2026-03-01</code>, <code>+3 months</code>.',
    'to'     => 'The last date of <code>from</code>; one of the two alone is a year from it (or from today).',
    'lang'   => 'The language of <code>local</code> for a country of more than one: be <code>nl fr de</code>, lu <code>lb fr de</code>, ch <code>de fr it</code>, ca <code>en fr</code>, fi <code>fi sv</code>.' ];

  $tagSee     = [ 'calendar', 'countdown', 'timeline' ];

?>
