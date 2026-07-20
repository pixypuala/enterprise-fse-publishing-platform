=== Enterprise Publishing ===
Contributors: pixypuala
Tags: custom post types, capabilities, editorial workflow, block, schema
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Governed content models, server-authoritative capabilities, and tracked schema migrations for editorial teams.

== Description ==

Enterprise Publishing owns the durable side of an editorial site: the content
models, who may do what to them, and how their stored shape changes over time.
It deliberately holds no presentation, so the block theme on top of it stays
purely a matter of design and can be replaced without touching data or rules.

Three governed content models ship with it — Program, Event, and Story — each
registered with its own capability base rather than borrowing the built-in post
capabilities. That separation is what makes the permission model meaningful:
granting someone editorial rights over programs does not hand them the blog.

= Server-authoritative permissions =

Capabilities are computed from one policy table and applied to roles by the
installer, so the rules live in a single, unit-tested place:

* Contributors may create and edit their own drafts. They cannot publish, and
  cannot touch anyone else's work.
* Editors may publish and manage everyone's content, but cannot delete
  published content.
* Administrators hold every capability, including the destructive ones.

No client-side check is trusted to enforce any of this.

= Tracked schema migrations =

The stored schema carries a version number. Pending migration steps run once,
in order, and the version is recorded. A database at a version newer than the
installed build refuses to downgrade rather than corrupting data quietly.

= Program List block =

A server-rendered block lists published programs with an accessible
name filter driven by the Interactivity API. Nothing is persisted in post
content, so the list can never go stale against the database.

= Structured data =

Single program views emit JSON-LD describing the program, built from the
governed fields rather than scraped from rendered markup.

= Privacy =

Personal-data exporter and eraser callbacks are registered, so content owned by
a user is included in WordPress' own export and erasure requests.

== Frequently Asked Questions ==

= Does uninstalling delete my content? =

No. Uninstalling removes only what the plugin installed: its schema-version
option and the custom capabilities it granted to roles. Programs, events, and
stories are your data and are left untouched in the database. If you want that
content gone, delete or export it explicitly before removing the plugin.

= Do I have to use the companion theme? =

No. The plugin registers the content models, capabilities, block, and
structured data on its own. Any theme can render them; the companion FSE theme
simply ships templates that already match.

= Why custom capabilities instead of the post capabilities? =

So that editorial access can be granted per content model. With the built-in
post capabilities, letting an editor manage programs would also let them manage
every post on the site.

= What happens on deactivation? =

Nothing destructive. Rewrite rules are flushed; capabilities and content stay
in place so deactivating temporarily never costs an editor their access.

== Screenshots ==

1. Publishing Health, under Tools. The schema version the database is actually
   at against the version this build expects, every migration step that has
   run, and each governed model — Programs, Events, Stories — with its post
   type key and published count.
2. The capability policy for a single model, exactly as the plugin computes it:
   contributors author their own drafts, editors publish and manage everyone's
   work, and only administrators may delete published content. Alongside it,
   the Schema.org JSON-LD emitted into the document head for a program, and the
   Program List block's filter on the front end.

== Changelog ==

= 0.1.0 =
* Initial release: governed Program, Event, and Story content models,
  server-authoritative capability policy, versioned schema migrations, the
  Program List block, program JSON-LD, and privacy exporter/eraser support.

== Upgrade Notice ==

= 0.1.0 =
Initial release.
