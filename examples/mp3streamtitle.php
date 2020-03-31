<?php

require_once(__DIR__.'/../Mp3StreamTitle.php');

use Mp3StreamTitle\Mp3StreamTitle;

$mp3_stream_title = new Mp3StreamTitle();

var_dump($mp3_stream_title->sendRequest('http://str2b.openstream.co/572'));
