<?php

/**
 * Copyright 2020 Oleg Kovalenko
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mp3StreamTitle;

class Radio101RuTitle
{
    /**
     * Indicate which function to use to send requests to the server.
     * 1 — cURL-function.
     * 2 — Socket-function.
     * 3 — FGC-function.
     *
     * @var int
     */
    public $send_type = 1;

    /**
     * The contents of our "User-Agent" HTTP-header.
     *
     * @var string
     */
    public $user_agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36';

    /**
     * Enable or disable the display of error messages.
     * 0 — Error messages display disabled.
     * 1 — Error messages display enabled.
     *
     * @var int
     */
    public $show_errors = 0;

    /**
     * The function takes as an argument a direct link to the radio channel
     * of an online radio station and uses the function specified in the
     * settings to send requests to the server.
     *
     * @param  string  $streaming_url
     * @return $this
     */
    public function sendRequest($streaming_url)
    {
        // Use the cURL-function.
        if ($this->send_type == 1) {
            return $this->sendCurl($streaming_url);
        // Use the Socket-function.
        } elseif ($this->send_type == 2) {
            return $this->sendSocket($streaming_url);
        // Use the FGC-function.
        } else {
            return $this->sendFGC($streaming_url);
        }
    }

    /**
     * The function takes as argument data in JSON format, and returns
     * from the received data information about the song in the following
     * format "artist name and song name".
     *
     * @param  string  $json
     * @return mixed
     */
    public function getSongInfo($json)
    {
        // Convert JSON-data to an array.
        if ($base = json_decode($json, true)) {

            // Depending on the request execution code, take appropriate actions.
            if ($base['status'] == 1 && $base['errorCode'] == 0) {
                // We get information about the song in the following format "artist name and song name".
                $result = $base['result']['short']['title'];
            // If error messages display disabled.
            } elseif ($this->show_errors == 0) {
                $result = 0;
            // If enabled.
            } else {
                $result = 'No Information about the track ether.';
            }

        // If error messages display disabled.
        } elseif ($this->show_errors == 0) {
            $result = 0;
        // If enabled.
        } else {
            $result = 'Error loading JSON.';
        }
        return $result;
    }

    /**
     * The function takes as an argument a direct link to the radio channel
     * of the online radio station and obtains the radio channel number of
     * the radio station from exile. As a result, the function returns the URL
     * to which the request will be sent.
     *
     * @param  string  $streaming_url
     * @return mixed
     */
    public function getURL($streaming_url)
    {
        // Initialize variables.
        $result = 0;

        // Parse URL.
        $url_part = parse_url($streaming_url);

        // Find out the number of the radio channel of the radio station.
        if ($number = @substr($url_part['path'], (strrpos($url_part['path'], '/') + 1))) {
            // Compose the URL to which the request will be sent.
            $result = 'https://101.ru/api/channel/getTrackOnAir/'.$number.'/channel/?dataFormat=json';
        }
        return $result;
    }

    /**
     * The cURL-function takes as an argument a direct link to the radio
     * channel of an online radio station and sends a cURL request to the
     * server. As a result, the function returns information about the song in
     * the following format "artist name and song name".
     *
     * @param  string  $streaming_url
     * @return mixed
     */
    public function sendCurl($streaming_url)
    {
        // Checking if we can use cURL.
        if (extension_loaded('curl') && function_exists('curl_init')) {

            // Get the URL to which the request will be sent.
            if ($url_api = $this->getURL($streaming_url)) {
                // Initialize the cURL session.
                $ch = curl_init();

                // Set the parameters for the session.
                curl_setopt($ch, CURLOPT_URL, $url_api);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);

                // Execute the request.
                $data = @curl_exec($ch);

                // If there are errors we save them into variables.
                $errno = curl_errno($ch);
                $error = curl_error($ch);

                // End the session.
                curl_close($ch);

                // Return the result of the request.
                if (!$errno) {
                    $result = $this->getSongInfo($data);
                // If error messages display disabled.
                } elseif ($this->show_errors == 0) {
                    $result = 0;
                // If enabled.
                } else {
                    $result = $error.' ('.$errno.').';
                }

            // If error messages display disabled.
            } elseif ($this->show_errors == 0) {
                $result = 0;
            // If enabled.
            } else {
                $result = 'Failed to get the radio channel number of the radio station.';
            }

        // If error messages display disabled.
        } elseif ($this->show_errors == 0) {
            $result = 0;
        // If enabled.
        } else {
            $result = 'There is no way to use the cURL library on this hosting.';
        }
        return $result;
    }

    /**
     * The socket-function takes as an argument a direct link to the radio
     * channel of the online radio station and sends an HTTP-request to the
     * server. As a result, the function returns information about the song in
     * the following format "artist name and song name".
     *
     * @param  string  $streaming_url
     * @return mixed
     */
    public function sendSocket($streaming_url)
    {
        // Initialize variables.
        $prefix  = '';
        $port    = 80;
        $timeout = 30;
        $path    = '/';
        $query   = '';
        $buffer  = '';

        // Get the URL to which the request will be sent.
        if ($url_api = $this->getURL($streaming_url)) {
            // Parse URL.
            $url_part = parse_url($url_api);

            // Find out protocol.
            if ($url_part['scheme'] == 'https') {
                $prefix = 'ssl://'; // If HTTPS, use the SSL protocol.
                $port   = 443; // If HTTPS, the port can only be 443.
            }

            // Find out path.
            if (!empty($url_part['path'])) {
                $path = $url_part['path'];
            }

            // Find out query string.
            if (!empty($url_part['query'])) {
                $query = $url_part['query'];
            }

            // Open connection.
            if ($fp = @fsockopen($prefix.$url_part['host'], $port, $errno, $errstr, $timeout)) {
                // HTTP-request headers.
                $headers = "GET ".$path."?".$query." HTTP/1.0\r\n";
                $headers .= "Host: ".$url_part['host']."\r\n";
                $headers .= "User-Agent: ".$this->user_agent."\r\n\r\n";

                // Send a request to the server.
                if (fwrite($fp, $headers)) {
                    // Set the timeout value for the stream.
                    stream_set_timeout($fp, $timeout);
                    // Retrieving metadata from the stream.
                    $info = stream_get_meta_data($fp);

                    // Get a response from the server.
                    while (!feof($fp) && !$info['timed_out']) {
                        // Put the data in a variable.
                        $buffer .= fgets($fp, 4096);
                        // Retrieving metadata from the stream.
                        $info = stream_get_meta_data($fp);
                    }

                    // Close the connection.
                    fclose($fp);

                    // Separate the headers from the "body".
                    list($tmp, $body) = explode("\r\n\r\n", $buffer, 2);

                    // Return the result of the request.
                    $result = $this->getSongInfo($body);
                // If error messages display disabled.
                } elseif ($this->show_errors == 0) {
                    // Close the connection.
                    fclose($fp);

                    $result = 0;
                // If enabled.
                } else {
                    // Close the connection.
                    fclose($fp);

                    $result = 'Failed to get server response.';
                }

            // If error messages display disabled.
            } elseif ($this->show_errors == 0) {
                $result = 0;
            // If enabled.
            } else {
                $result = 'An error occurred while using sockets. '.$errstr.' ('.$errno.').';
            }

        // If error messages display disabled.
        } elseif ($this->show_errors == 0) {
            $result = 0;
        // If enabled.
        } else {
            $result = 'Failed to get the radio channel number of the radio station.';
        }
        return $result;
    }

    /**
     * The FGC-function takes as an argument a direct link to the radio
     * channel of the online radio station and opens the file using the set
     * HTTP-headers. As a result, the function returns information about the
     * song in the following format "artist name and song name".
     *
     * @param  string  $streaming_url
     * @return mixed
     */
    public function sendFGC($streaming_url)
    {
        // Get the URL to which the request will be sent.
        if ($url_api = $this->getURL($streaming_url)) {
            // HTTP-request headers.
            $options_method = "GET";
            $options_header = "User-Agent: ".$this->user_agent."\r\n\r\n";

            $options = [
                'http' => ['method'  => $options_method,
                           'header'  => $options_header,
                           'timeout' => 30]
            ];

            // Create a thread context.
            $context = stream_context_create($options);

            // Open the file using the HTTP-headers set above.
            if ($buffer = @file_get_contents($url_api, false, $context)) {
                // Return the execution result of the function.
                $result = $this->getSongInfo($buffer);
            // If error messages display disabled.
            } elseif ($this->show_errors == 0) {
                $result = 0;
            // If enabled.
            } else {
                $result = 'Failed to get server response.';
            }

        // If error messages display disabled.
        } elseif ($this->show_errors == 0) {
            $result = 0;
        // If enabled.
        } else {
            $result = 'Failed to get the radio channel number of the radio station.';
        }
        return $result;
    }
}
