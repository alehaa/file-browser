<?php

/* This file is part of file-browser.
 *
 * Copyright (C)
 *  2018 Alexander Haase <ahaase@alexhaase.de>
 *
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

/* Check if the application's environment is set. If it's not, this indicates
 * that either the webserver is not configured properly or the dotenv package is
 * missing. */
if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new RuntimeException(
            'APP_ENV environment variable is not defined. You need to define '.
            'environment variables for configuration or add "symfony/dotenv" '.
            'as a Composer dependency to load variables from a .env file.'
        );
    }
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../.env');
}

/* Get the environment to be used and decide, if the debug mode should be
 * enabled.
 *
 * NOTE: Every environment distinct from 'prod' will enable the debug mode. */
$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = $_SERVER['APP_DEBUG'] ?? ('prod' !== $env);

/* Enable the debug mode, if required.
 *
 * NOTE: Every environment distinct from 'prod' will enable the debug mode,
 *       which requires symfony's debug component. As the debug mode shouldn't
 *       be used in production, there's no check wheter symfony's debug
 *       component is available, so initializing it throws an exception in
 *       production for safety reasons. */
if ($debug) {
    Debug::enable();
}

/* If provided in the environment, initialize the list of trusted proxies and
 * hosts. */
if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(
        explode(',', $trustedProxies),
        Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST
    );
}
if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

/* Create a new kernel, initialize the application with this kernel and handle
 * the HTTP request. A response will be sent back to the client. */
$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
