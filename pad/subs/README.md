# PAD subsystems

The 53 directories here own rendering, data and computational families. Every
subsystem is a direct child of `pad/subs/`. A family's `types/` files define its
available kinds; `pad/lib/subsystem.php` maps public namespaces and helper families
to these directories.

HTML components belong here. `pad/tags/` contains the engine's control flow,
evaluation, stores, composition, request handling and diagnostics. Computational
families expose PHP helpers and plain data, without inventing rendering tags.

See [the ownership review and migration map](../../docs/SUBSYSTEMS.md) for all
families, public APIs, migrated tags and the reasons for separate registries.
Each computational family's README documents its supported inputs and work limits.
