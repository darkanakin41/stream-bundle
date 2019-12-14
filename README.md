# darkanakin41/stream-bundle

[![Actions Status](https://github.com/darkanakin41/stream-bundle/workflows/Quality/badge.svg)](https://github.com/darkanakin41/stream-bundle/actions)

This is a bundle for Symfony 4 designed to store and provide a simple way to store streams from differents platforms, monitor them and automaticaly retrieve new ones.

Currently, we support automation for : 
* [Twitch.tv](https://www.twitch.tv)

## Dependencies

* [darkanakin41/core-bundle](https://github.com/darkanakin41/core-bundle)

## Installation

```bash
composer require darkanakin41/stream-bundle
```

## Available commands

```bash
#Retrieve new streams based on enabled categories in the database
bin\console darkanakin41:stream:retrieve 

#Refresh streams stored in the database
bin\console darkanakin41:stream:refresh 
```

## Features 
* Automatic removal of dead streams
* [YOUTUBE] Handle IsLive events coming from [darkanakin41/video-bundle](https://github.com/darkanakin41/video-bundle)

## TODO 
* Add more streams platform in the process
* Handle errors from providers (mainly quota)
* Add more unit tests
