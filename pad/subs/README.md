# PAD subsystems

The 25 directories here own related rendering and data families. A family's
`types/` files define its available kinds; `pad/lib/subsystem.php` maps the public
namespaces and helper families to their directories, including nested families.

HTML components belong here. `pad/tags/` contains the engine's control flow,
evaluation, stores, composition, request handling and diagnostics.

See [the ownership review and migration map](../../docs/SUBSYSTEMS.md) for all
families, the 22 migrated tags, and the reasons for retaining separate registries.
