# GeoBench

An extensible toolkit to manage geo data and maps with WordPress.

**This project is currently in alpha and the plugin is broken.** (Install GeoBench plugin in a local development environment if you want to contribute or fiddle with it).

Find more on:

* [Geobench website (**coming soon**)](http://geoben.ch)
* [Trello board](https://trello.com/b/oUkhpkmd)
* [Slack channel](https://geobench.slack.com)

Requirements:
* WordPress 4.2
* PHP => 5.4.0

## Concept

The basic idea of GeoBench is to try to provide a layer of abstraction to handle geo data inside WordPress. At a basic level, it attaches geo data to existing WordPress objects (coordinates, polylines, polygons...). Crafted extensions for the toolkit can handle the data to address specific use cases, do distance based queries, create maps on the fly using different map providers, use geolocation services, integrate with third party services or databases, use the REST API  and so on.

The idea is not to simply embed a Google Map in a post, but build maps from the WordPress admin and then populate them dynamically or build content according to how the geo data is handled. Since the use cases are very broad, it makes sense to have a toolkit that is unopinionated and provides a significantly high level of abstraction. Individual extensions may be suited to developers or less tech savvy end users.
