<?php
  require dirname(__DIR__,2) . '/pad/subs/codes/_lib/code11.php';
  $fixture = '101100101101011010010110110010101011010101101101101101011011010100101101011001';
  foreach ( [['123-45',2],['123-455',1],['123-4552',0]] as [$text,$checks])
    if (padCode11($text,$checks)[0]!==$fixture) throw new Exception('Zint/TEC-IT reference mismatch');
  if (padCode11('93')[0]!=='10110010110101011001010101101010110101011001') throw new Exception('hyphen check mismatch');
  if (padCode11('123-45',1)[0]!=='1011001011010110100101101100101010110101011011011011010110110101011001') throw new Exception('single check mismatch');
  if (padCode11('123-45',0)[0]!=='10110010110101101001011011001010101101010110110110110101011001') throw new Exception('no check mismatch');
  if (padCode11('123456789012')[1][0][1]!=='123456789012-8') throw new Exception('long checksum mismatch');
  foreach (['','A','1.2',' 12',str_repeat('1',141)] as $bad) if (!is_string(padCode11($bad))) throw new Exception('invalid Code 11 accepted');
  if (!is_array(padCode11(str_repeat('1',140)))) throw new Exception('maximum length rejected');
  if (!is_string(padCode11('1',3))) throw new Exception('invalid checks accepted');
  echo "Code 11: six independent reference symbols, checksum boundaries and invalid data passed\n";
?>
