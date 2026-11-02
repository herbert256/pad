# Formula: CORREL

`CORREL(range1,range2)` computes Pearson correlation over numeric values paired by row-major position. Pairs with a blank, text or boolean on either side are excluded; zero counts. Errors in either range propagate. Unequal range sizes return #N/A; fewer than two paired numbers or zero variance return #DIV/0!. Each axis is scaled before covariance to avoid overflow. No array/spill result. Example: `=CORREL(A1:A5,B1:B5)`.

Reference contract and examples: https://support.microsoft.com/en-us/excel/functions/correl-function . Run `php tests/subsystems/formula-correl.php`.
