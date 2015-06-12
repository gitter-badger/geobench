# GeoBench

An extensible toolkit to manage geo data and maps with WordPress.

**This project is currently in alpha and the plugin is broken.**

Find out more on:

* [Geobench website](http://geoben.ch/) - (**coming soon**)
* [Slack channel](https://geobench.slack.com/) - [(ask for invitation)](mailto:fulvio.notarstefano@gmail.com)
* [Trello board](https://trello.com/b/oUkhpkmd)

Requirements:
* WordPress >= 4.2
* PHP >= 5.4.0

If you want to contribute:
* Make sure you have node, npm and composer installed
* Install [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV) for local development (or alternative of your choice)
* `git clone https://github.com/geobench/geobench` to your local machine
* `cd geobench` and run `composer install`

## Concept

The basic idea of GeoBench is to try to provide a layer of abstraction to handle geo data inside WordPress. At a basic level, it attaches geo data to existing WordPress objects (coordinates/points, polylines, polygons...). Crafted extensions for the toolkit can handle the data to address specific use cases, do distance based queries, create maps on the fly using different map providers, use custom markers, use geolocation services, integrate with third party services or databases, use the REST API and so on.

The idea is not to simply embed a Google Map in a post (there are way too many plugins to fullfill this purpose), but build maps from the WordPress admin and then populate them dynamically or build content according to how the geo data is handled. Since the use cases are very broad, it makes sense to have a toolkit that is sufficiently unopinionated and provides a significantly high level of abstraction. Individual extensions may be suited to developers or less tech savvy end users to accomplish tasks or visualize data.
