# plejeune/stream-bundle
This is a bundle for Symfony 4 designed to store and provide a simple way to store streams from differents platforms, monitor them and automaticaly retrieve new ones.

Currently, we support automation for : 
* [Twitch.tv](https://www.twitch.tv)

## Dependencies

* [plejeune/core-bundle](https://gitlab.com/pierrelejeune/corebundle)
* [plejeune/api-bundle](https://gitlab.com/pierrelejeune/api-bundle)

## Installation

```bash
composer require plejeune/stream-bundle
```

## Available commands

``` bin\console plejeune:stream:retrieve``` : Retrieve new streams based on enabled categories in the database
``` bin\console plejeune:stream:refresh``` : Refresh streams stored in the database

## Features 
* Automatic removal of dead streams
* [YOUTUBE] Handle IsLive events coming from VideoBundle

## TODO 
* Add more streams platform in the process
* Handle errors from providers (mainly quota)
