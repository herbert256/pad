<?php

  // auto - Select EAN-13 for twelve or thirteen digits, otherwise Code 128.
  // {barcode:auto $code, height=60/} preserves automatic selection for variable data.

  return padCodeTag ( preg_match ( '/^\d{12,13}$/D', trim ( padCodeText () ) ) ? 'ean13' : 'code128', padCodeText () );

?>
