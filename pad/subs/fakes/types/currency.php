<?php

  // currency - The code of a currency as ISO 4217 writes it, three capitals: EUR, or another
  // of the currencies of the countries country gives and a few more - each a code of ISO
  // 4217's list one, the currencies in use, as its maintenance agency SIX publishes it. The
  // currency pipe writes an amount in it: {fake 'total:price, currency', rows=3}
  // {$total | currency($currency)}{/fake}.

  return padFakePick ( [ 'AUD', 'BRL', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'INR',
                         'JPY', 'KRW', 'MXN', 'NOK', 'NZD', 'PLN', 'SEK', 'SGD', 'USD', 'ZAR' ] );

?>
