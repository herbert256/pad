<?php

  // {printable:invoice data='invoices', sender='PAD, Main Street 1, 3511 AB Utrecht'}...{/printable:invoice}
  // - an invoice for a window envelope: the letter's page - its <address> in the field the
  // window shows, its <aside> beside it for the number and the dates, its <header> the
  // letterhead, its <footer> at the foot for the bank and the tax number, the rest the body -
  // with title= (Invoice) as the heading of the body and a table in it set as the lines of an
  // invoice: a rule under the head, the last column to the right, a <tfoot> for the totals. An
  // invoice for every row of the data, its lines a list in the row - {entries}<tr>...</tr>{/entries}.
  //
  //   {printable:invoice data='invoices', sender='PAD, Main Street 1, 3511 AB Utrecht'}
  //     <address>{$customer}<br>{$street}<br>{$city}</address>
  //     <aside>Invoice {$number}<br>Date {$date}</aside>
  //     <table><tr><th>Item</th><th>Amount</th></tr>{entries}<tr><td>{$item}</td><td>{$amount}</td></tr>{/entries}</table>
  //     <footer>IBAN NL91 TEST 0123 4567 89</footer>
  //   {/printable:invoice}

  return padPrintableDin5008 ( [ 'invoice', 'invoices' ], padPrintableText ( 'title', 'Invoice' ) );

?>
