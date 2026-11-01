<?php

  // A parameter written more than once is the union of its values, as RFC 2426 allows and
  // Apple Contacts writes a 3.0 card - EMAIL;type=INTERNET;type=HOME;type=pref: - so the
  // TYPE=pref before another TYPE marks the preferred address, where the last TYPE alone was
  // kept and the first address taken.

  $vcfTwice = padFormatRead ( 'vcf', "BEGIN:VCARD\nVERSION:3.0\nFN:A\nEMAIL;TYPE=INTERNET:first@x.nl\nEMAIL;TYPE=pref;TYPE=INTERNET:second@x.nl\n"
                                   . "TEL;type=CELL:+31 1\nTEL;type=VOICE;type=pref;type=HOME:+31 2\nEND:VCARD" );

  $vcfTwice = $vcfTwice [0] ['email'] . ' ' . $vcfTwice [0] ['phone'] . ' | ' . $vcfTwice [0] ['emails'];

?>
