<?php
return ['name'=>'ODS','mime'=>'application/vnd.oasis.opendocument.spreadsheet','extension'=>['ods'],'shape'=>'rows','lib'=>'ods','binary'=>TRUE,'options'=>['sheet','header','formulas'],'read'=>'padOdsRead','write'=>fn($rows,$options)=>padOdsWrite(padFormatRows($rows),$options),'download'=>'attachment'];
