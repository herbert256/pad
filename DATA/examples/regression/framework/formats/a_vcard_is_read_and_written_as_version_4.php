<?php

  // The vCard of RFC 6350's example (8) and a version 3.0 card: the parts of N and ADR, the
  // preferred number first (PREF=1, a TYPE=pref of 3.0), tel: left off, BDAY --0203 as
  // --02-03, a NOTE's \n and \, unescaped. Written as version 4.0 - FN, N, the addresses with
  // the preferred one marked, a urn:uuid made of the card - and read again the same cards.

  $vcfText = "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:Simon Perreault\r\nN:Perreault;Simon;;;ing. jr,M.Sc.\r\nBDAY:--0203\r\n"
           . "ORG:Viagenie\r\nADR;TYPE=work:;Suite D2-630;2875 Laurier;Quebec;QC;G1V 2M2;Canada\r\n"
           . "TEL;VALUE=uri;TYPE=\"work,voice\";PREF=1:tel:+1-418-656-9254;ext=102\r\n"
           . "TEL;VALUE=uri;TYPE=\"work,cell,voice,video,text\":tel:+1-418-262-6501\r\n"
           . "EMAIL;TYPE=work:simon.perreault@viagenie.ca\r\nURL;TYPE=home:http://nomis80.org\r\nEND:VCARD\r\n"
           . "BEGIN:VCARD\r\nVERSION:3.0\r\nFN:Ann Example\r\nEMAIL:ann@example.com\r\nEMAIL;TYPE=pref:ann.pref@example.com\r\n"
           . "NOTE:line one\\nline two\\, with a comma\r\nEND:VCARD\r\n";

  $vcfRows = padFormatRead ( 'vcf', $vcfText );
  $vcfOut  = padFormatWrite ( 'vcf', $vcfRows );
  $vcfBack = padFormatRead ( 'vcard', $vcfOut );

  foreach ( [ 'vcfRows', 'vcfBack' ] as $vcfName )
    foreach ( $$vcfName as $vcfAt => $vcfRow )
      unset ( ${$vcfName} [$vcfAt] ['uid'] );

  $vcfJson = json_encode ( $vcfRows, JSON_UNESCAPED_SLASHES );
  $vcfShow = str_replace ( "\r\n", '~', $vcfOut );
  $vcfSame = ( $vcfBack === $vcfRows ) ? 'read back the same' : 'read back otherwise';

?>
