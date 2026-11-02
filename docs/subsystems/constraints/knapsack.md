# constraints: knapsack

Exact 0/1 integer knapsack accepts items `{id,weight,value}` and integer capacity. IDs are unique strings. Weights/values are nonnegative integers; zero weight is permitted. Capacity ≤10000, items ≤1000, DP cells ≤250000; values are bounded to prevent integer overflow. Returns chosen IDs, objective value, weight and solved/limit/cancelled status. A stopped run never claims infeasibility; ties prefer earlier items.

Runnable example: `apps/tags/examples/solve/knapsack.pad`. Independent fixture: `php tests/subsystems/expansion-constraints-knapsack.php`.
