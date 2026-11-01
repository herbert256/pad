<?php

  // Three events of the examples of RFC 5545 (3.6.1, 4): a conference with its description
  // folded over lines, its \n and \, unescaped; an anniversary that is a day, with its
  // categories and its rule, which lasts the day; and a lunch in a TZID with a DURATION, its
  // alarm read past - each a row with every field.

  $icsText = "BEGIN:VCALENDAR\r\nPRODID:-//xyz Corp//NONSGML PDA Calendar Version 1.0//EN\r\nVERSION:2.0\r\n"
           . "BEGIN:VEVENT\r\nDTSTAMP:19960704T120000Z\r\nUID:uid1@example.com\r\nORGANIZER:mailto:jsmith@example.com\r\n"
           . "DTSTART:19960918T143000Z\r\nDTEND:19960920T220000Z\r\nSTATUS:CONFIRMED\r\nCATEGORIES:CONFERENCE\r\n"
           . "SUMMARY:Networld+Interop Conference\r\nDESCRIPTION:Networld+Interop Conference\r\n  and Exhibit\\nAtlanta World Congress Center\\n\r\n Atlanta\\, Georgia\r\nEND:VEVENT\r\n"
           . "BEGIN:VEVENT\r\nUID:19970901T130000Z-123403@example.com\r\nDTSTAMP:19970901T130000Z\r\nDTSTART;VALUE=DATE:19971102\r\n"
           . "SUMMARY:Our Blissful Anniversary\r\nTRANSP:TRANSPARENT\r\nCLASS:CONFIDENTIAL\r\nCATEGORIES:ANNIVERSARY,PERSONAL,SPECIAL OCCASION\r\nRRULE:FREQ=YEARLY\r\nEND:VEVENT\r\n"
           . "BEGIN:VEVENT\r\nUID:lunch@example.com\r\nDTSTAMP:19970901T130000Z\r\nDTSTART;TZID=America/New_York:19970714T133000\r\nDURATION:PT1H30M\r\nSUMMARY:Lunch\r\n"
           . "BEGIN:VALARM\r\nTRIGGER:-PT15M\r\nACTION:DISPLAY\r\nDESCRIPTION:Reminder\r\nEND:VALARM\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";

  $icsRows = json_encode ( padFormatRead ( 'ics', $icsText ), JSON_UNESCAPED_SLASHES );

?>
