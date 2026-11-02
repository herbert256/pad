# pareto

`{chart:pareto label='cause', value='count'}...{/chart:pareto}` ranks nonnegative contributions in descending order. Equal values retain input order; duplicate category names remain separate observations. Negative and missing values are omitted and counted in the accessible description. All-zero input has a 0% cumulative line. Positive totals finish at 100%; normalized accumulation avoids overflow.

The left axis is contribution and the right axis is cumulative percent. Labels are fitted to available width, with complete values in the description and tooltips. Width/height, title, palette, data/sequence handling and client rendering use the standard chart contract.

Validation: `PAD_HOME=$PWD node apps/regression/pages/charts/_client.mjs --kinds=pareto` compares server and browser SVG including zero, negative, missing and tied input.
