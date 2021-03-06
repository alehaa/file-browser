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
 *
 * NOTE: This file includes autogenerated code from Symfony, also licensed under
 *       the MIT license.
 */

namespace App\Service;

/**
 * Locate files in the storage directory.
 *
 * As the storage directory may be any directory at the filesystem, this storage
 * file locator resolves paths to the absolute path at the filesystem, so one is
 * able to interact with these.
 */
class StorageLocator
{
    /**
     * The location of the file storage.
     *
     *
     * @var string
     */
    private $storagePath;

    /**
     * Constructor.
     *
     *
     * @param string $storagePath the root directory containing the files
     */
    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;
    }

    /**
     * Get the absolute path of `$path` in the storage directory.
     *
     *
     * @param string $path the path to be resolved
     *
     * @return string the absolute path in the storage directory
     */
    public function getAbsolutePath(string $path = null): string
    {
        return sprintf(
            '%s%s%s',
            $this->storagePath,
            DIRECTORY_SEPARATOR,
            $path
        );
    }
}
