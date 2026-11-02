<?php

  // IPMT and PPMT (types/ipmt.php, ppmt.php) on the tables of Microsoft's pages for them, with
  // the answers those pages give - ($66.67), ($292.45), ($75.62), ($27,598.05); then a whole
  // loan paid at the end and at the start of its periods - the interest and the principal of
  // each period add up to PMT, the principals to the loan, and the first period paid at the
  // start pays no interest - a future value, a rate of 0, and the edges: a period below 1 or
  // past nper #NUM!, a rate that leaves PMT nothing to divide by its #NUM!, a value that is no
  // number #VALUE!. Rounded to 8 decimals, where the last of 15 digits depends on the order of
  // the arithmetic; the answers were checked against a schedule worked out period by period.

  $loanFirst  = [ [ 'Data', 'Description' ], [ '10.00%', 'Annual interest rate' ], [ 1, 'Period' ], [ 3, 'Years of the loan' ], [ 8000, 'Present value of the loan' ] ];
  $loanSecond = [ [ 'Data', 'Description' ], [ '10%', 'Annual interest rate' ], [ 2, 'Years for the loan' ], [ 2000, 'Loan amount' ] ];
  $loanThird  = [ [ '8%' ], [ 10 ], [ 200000 ] ];

  $loanCases = [
    [ '=ROUND(IPMT(A2/12, A3, A4*12, A5),2)', $loanFirst ],
    [ '=ROUND(IPMT(A2, 3, A4, A5),2)', $loanFirst ],
    [ '=ROUND(PPMT(A2/12, 1, A3*12, A4),2)', $loanSecond ],
    [ '=ROUND(PPMT(A1, A2, 10, A3),2)', $loanThird ]
  ];

  $loanOut = [];

  foreach ( $loanCases as [ $loanFormula, $loanCells ] )
    $loanOut [] = "$loanFormula: " . var_export ( padFormula ( $loanFormula, $loanCells ), TRUE );

  foreach ( [ 0, 1 ] as $loanType )
    foreach ( [ 1, 2, 3 ] as $loanPer )
      $loanOut [] = "type $loanType, period $loanPer: "
                  . padFormula ( "=ROUND(IPMT(10%,$loanPer,3,8000,0,$loanType),8)" ) . ' + '
                  . padFormula ( "=ROUND(PPMT(10%,$loanPer,3,8000,0,$loanType),8)" ) . ' = '
                  . padFormula ( "=ROUND(IPMT(10%,$loanPer,3,8000,0,$loanType)+PPMT(10%,$loanPer,3,8000,0,$loanType)-PMT(10%,3,8000,0,$loanType),8)" ) . ' + PMT';

  $cases = implode ( ' | ', $loanOut ) . ' | ' . formulasRun ( [
    '=ROUND(PPMT(10%,1,3,8000)+PPMT(10%,2,3,8000)+PPMT(10%,3,3,8000),8)',
    '=ROUND(PPMT(10%,1,3,8000,0,1)+PPMT(10%,2,3,8000,0,1)+PPMT(10%,3,3,8000,0,1),8)',
    '=ROUND(IPMT(5%/12,10,360,-200000,50000),8)',
    '=ROUND(PPMT(5%/12,10,360,-200000,50000),8)',
    '=IPMT(0,2,4,1000)',
    '=PPMT(0,2,4,1000)',
    '=IPMT(10%,0,3,8000)',
    '=IPMT(10%,0.5,3,8000)',
    '=PPMT(10%,4,3,8000)',
    '=IPMT(10%,1,0,8000)',
    '=IPMT(-200%,2,2,100)',
    '=PPMT(-100%,1,2,100,0,1)',
    '=IPMT("x",1,3,8000)',
    '=PPMT(10%,1,3,A2)',
    '=PPMT(10%,1,3)'
  ] );

?>
