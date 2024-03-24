<?php
declare(strict_types=1);

/* 
 * MIT License
 *
 * Copyright (c) 2018-present, Marks Software GmbH (https://www.marks-software.de/)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace BechlemConnectLight\Utility\AppCache;

/**
 * App Cache Model
 *
 */
class AppCache
{

    /**
     * Removes all cache files from TMP . DS . cache folder. Returns true on success.
     *
     * @return bool
     */
    public function clearAppCache()
    {
        $rootFiles = glob(TMP . '*'); 
        foreach ($rootFiles as $rootFile) {
            if (is_file($rootFile)) {
                unlink($rootFile);
            }
        }

        $cacheFiles = glob(TMP . 'cache' . DS  . '*');
        foreach ($cacheFiles as $cacheFile) {
            if (is_file($cacheFile)) {
                unlink($cacheFile);
            }
        }

        $cacheModelFiles = glob(TMP . 'cache' . DS . 'models' . DS  . '*');
        foreach ($cacheModelFiles as $cacheModelFile) {
            if (is_file($cacheModelFile)) {
                unlink($cacheModelFile);
            }
        }

        $cachePersistentFiles = glob(TMP . 'cache' . DS . 'persistent' . DS  . '*');
        foreach ($cachePersistentFiles as $cachePersistentFile) {
            if (is_file($cachePersistentFile)) {
                unlink($cachePersistentFile);
            }
        }

        $cacheUserFiles = glob(TMP . 'cache' . DS . 'users' . DS  . '*');
        foreach ($cacheUserFiles as $cacheUserFile) {
            if (is_file($cacheUserFile)) {
                unlink($cacheUserFile);
            }
        }

        $cacheViewFiles = glob(TMP . 'cache' . DS . 'users' . DS  . '*');
        foreach ($cacheViewFiles as $cacheViewFile) {
            if (is_file($cacheViewFile)) {
                unlink($cacheViewFile);
            }
        }

        return true;
    }
}
