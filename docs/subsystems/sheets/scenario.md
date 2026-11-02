# sheet:scenario

`model=` is an A1-addressed scalar/formula map. `scenarios=` maps names to input overrides; `inputs=` and `outputs=` are explicit address lists. Each scenario copies and recalculates the model using the existing server formula engine and same seed. Error codes remain visible; base state is unchanged. Overrides cannot add formulas or undeclared cells. Bounds: 50 scenarios, 100 outputs, 20000 model-cell evaluations across scenarios. Existing evaluator bounds also apply.

## Example

```pad
{sheet:scenario model='scenarioModel', scenarios='scenarioCases', inputs='scenarioInputs', outputs='scenarioOutputs'/}
```

Run the focused fixture with `php tests/subsystems/sheet-scenario.php`.

Array-valued options accept a literal array or the quoted name of an application array/store.
