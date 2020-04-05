# MP3 Stream Title

This repository contains two PHP libraries Mp3StreamTitle and Radio101RuTitle.

The library Mp3StreamTitle is designed to receive information about the currently playing song from the stream of the online radio station and display this information on the screen.

The library Radio101RuTitle is designed to obtain information about the currently playing song on the specified radio channel of the site 101.ru and display this information on the screen.

Libraries are independent of each other.


## Requirements

* PHP >= 7.2

## Examples/Usage

### Mp3StreamTitle

```php
<?php

require_once('Mp3StreamTitle/Mp3StreamTitle.php');

use Mp3StreamTitle\Mp3StreamTitle;

$mp3_stream_title = new Mp3StreamTitle();

// Instead of "http://example.com", specify a direct link to the stream of any online radio station.
$stream_title = $mp3_stream_title->sendRequest('http://example.com');

echo $stream_title;
```

### Radio101RuTitle

```php
<?php

require_once('Mp3StreamTitle/Radio101RuTitle.php');

use Mp3StreamTitle\Radio101RuTitle;

$radio101_ru_title = new Radio101RuTitle();

// Instead of "http://example.com" specify a direct link to any radio channel of the site 101.ru.
$stream_title = $radio101_ru_title->sendRequest('http://example.com');

echo $stream_title;

```


## Authors

* **Oleg Kovalenko** - *Owner/Maintainer* - [KO-N](https://github.com/KO-N)

See also the list of [contributors](https://github.com/KO-N/mp3streamtitle/contributors) who participated in this project.

## License

This project is licensed under the Apache License, Version 2.0 - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

* I want to thank [Francesco Casula](https://github.com/fracasula) for his [code](https://gist.github.com/fracasula/5781710) that inspired me to create these PHP libraries.

## Buy me a coffee

<a href="https://www.buymeacoffee.com/kondeveloper" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-yellow.png" alt="Buy Me A Coffee" style="height: 51px !important;width: 217px !important;" ></a>

If you like these libraries, please consider buying me coffee. :relaxed:
