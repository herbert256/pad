# inputs: currency

`{input:currency 'amount', currency='EUR', decimals=2, value='12.50', label='Price'/}`
submits `amount=12.50` through a native number field, with or without JavaScript.
Currency is an explicit three-letter code; no currency conversion is performed.
`decimals` 0–6 sets the native step. Extra precision fails browser validation; the
widget never rounds the submitted amount. Validate amount/precision again on the server.
An optional Intl preview (`locale='nl-NL'`) formats a separate output; it never changes
the field, cursor or submitted decimal convention. Native min/max/required/disabled,
PAD rules, refill and error descriptions remain available.
