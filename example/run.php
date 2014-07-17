<?php
/**
 * @package   SmfX
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */

$src     = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src');
$package = 'PHPFork';

$root       = $src . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR;
$handlerDir = $root . DIRECTORY_SEPARATOR . 'Handler';

require_once($handlerDir . DIRECTORY_SEPARATOR . 'ExecResultsHandler.php');
require_once($handlerDir . DIRECTORY_SEPARATOR . 'PidResultsHandler.php');
require_once($root . 'Fork.php');

$_url = $_requestsPerSecond = $_timeOut = $_timeLimit = null;

if (is_array($_SERVER['argv'])) {
    foreach($_SERVER['argv'] as $argv) {
        $out = array();
        preg_match('/^--(?P<param>([a-z]+))\=(?P<value>(.+))$/', $argv, $out);
        if (isset($out['param']) && isset($out['value'])) {
            switch ($out['param']) {
                case 'url'  : $_url = $out['value'];                break;
                case 'r'    : $_requestsPerSecond = $out['value'];  break;
                case 't'    : $_timeOut = $out['value'];            break;
                case 'l'    : $_timeLimit = $out['value'];          break;
            }
        }
    }
}

if (empty($_url) || empty($_requestsPerSecond) || empty($_timeLimit) || empty($_timeLimit)) {
    print 'Please type all arguments:' . PHP_EOL;
    print '  --url' . PHP_EOL;
    print '  --r (requests per second)' . PHP_EOL;
    print '  --t (timeout in seconds)' . PHP_EOL;
    print '  --l (execution time in seconds))' . PHP_EOL . PHP_EOL;
    exit;
}

$fork = new \PHPFork\Fork($_requestsPerSecond, 1000, $_timeLimit, 1);
$fork->execute(function() use($_url, $_timeOut) {

    // create a new cURL resource
    $ch = curl_init();

    if (!$ch) {
        print 'Please install CURL extension'; exit;
    }

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_PROXY, '');
    curl_setopt($ch, CURLOPT_URL, $_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, $_timeOut);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

    curl_exec($ch);

    if (curl_errno($ch)) {
        print curl_errno($ch) . ": " . curl_error($ch);
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);;

    // close cURL resource, and free up system resources
    curl_close($ch);
    unset($ch);

    print $code . PHP_EOL;
});
