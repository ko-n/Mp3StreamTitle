<?php

require_once(__DIR__.'/../Radio101RuTitle.php');

use Mp3StreamTitle\Radio101RuTitle;

$radio101_ru_title = new Radio101RuTitle();

var_dump($radio101_ru_title->sendRequest('https://101.ru/radio/channel/82'));
