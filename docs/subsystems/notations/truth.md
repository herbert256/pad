# truth

A semantic truth table with named expressions separated by semicolons or newlines, e.g. `X = A xor B; F = X and C`. Previous columns may appear in later expressions. Supports the bounded Karnaugh Boolean grammar (and/or/xor/not, parentheses and symbolic aliases); never executes source. Inputs use natural name order and binary assignment order, false before true.

At most 6 inputs (64 rows), 8 expression columns, 8,000 source bytes; each expression inherits the parser depth/length bounds. Labels and title are escaped. `{notation:truth "F = A and B"/}`.
