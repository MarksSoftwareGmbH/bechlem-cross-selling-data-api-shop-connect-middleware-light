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
namespace BechlemConnectLight\Network;

/**
 * FTP with Implicit SSL/TLS Class
 *
 * Simple wrapper for cURL functions to transfer an ASCII file over FTP with implicit SSL/TLS
 */
class FtpsConnect
{
    private $host;
    private $username;
    private $password;
    private $handle;

    /**
     * FtpsConnect constructor.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $username, string $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->handle = curl_init();
    }

    /**
     * FtpsConnect destructor.
     */
    public function __destruct()
    {
        if (!empty($this->handle)) {
            @curl_close($this->handle);
        }
    }

    /**
     * Common
     *
     * @param string $folderPath
     *
     * @return false|resource
     */
    private function common(string $folderPath)
    {
        curl_reset($this->handle);
        curl_setopt($this->handle, CURLOPT_URL, 'ftps://' . $this->host . $folderPath);
        curl_setopt($this->handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->handle, CURLOPT_USE_SSL, CURLFTPSSL_TRY);
        curl_setopt($this->handle, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
        return $this->handle;
    }

    /**
     * Download file
     *
     * @param string $folderPath
     * @param string|null $localDirectory
     *
     * @return bool|false|string|null
     */
    public function download(string $folderPath, string $localDirectory = null)
    {
        if ($localDirectory === null) {
            $localDirectory = tempnam('/tmp', 'ftps_connect');
        }

        if ($fp = fopen($localDirectory, 'w')) {
            $this->handle = self::common($folderPath);
            curl_setopt($this->handle, CURLOPT_UPLOAD, 0);
            curl_setopt($this->handle, CURLOPT_FILE, $fp);

            curl_exec($this->handle);

            if (curl_error($this->handle)) {
                return false;
            } else {
                return $localDirectory;
            }
        }

        return false;
    }

    /**
     * Upload file
     *
     * @param string $localDirectory
     * @param string $folderPath
     * @param string|null $file
     *
     * @return bool
     */
    public function upload(string $localDirectory, string $folderPath, string $file = null)
    {

        if ($localDirectory === null) {
            $localDirectory = tempnam('/tmp', 'ftps_connect');
        }

        if ($file === null) {
            return false;
        }

        if ($fp = fopen($localDirectory, 'r')) {
            $this->handle = self::common($folderPath . '/' . $file);
            curl_setopt($this->handle, CURLOPT_UPLOAD, 1);
            curl_setopt($this->handle, CURLOPT_INFILE, $fp);

            curl_exec($this->handle);
            $err = curl_error($this->handle);

            return !$err;
        }

        return false;
    }

    /**
     * List folder and files
     *
     * @param string $folderPath
     *
     * @return array|bool
     */
    public function listFolderAndFiles(string $folderPath)
    {
        if (substr($folderPath, -1) != '/') {
            $folderPath .= '/';
        }

        $this->handle = self::common($folderPath);
        curl_setopt($this->handle, CURLOPT_UPLOAD, 0);
        curl_setopt($this->handle, CURLOPT_FTPLISTONLY, 1);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($this->handle);

        if (curl_error($this->handle)) {
            return false;
        } else {
            $remoteFiles = explode("\r\n", trim($result));
            return $remoteFiles;
        }
    }

    /**
     * List files by date
     *
     * @param string $folderPath
     *
     * @return array|null
     */
    public function listByLastModified(string $folderPath)
    {
        $remoteFiles = $this->listFolderAndFiles($folderPath);
        if (empty($remoteFiles)) {
            return null;
        }

        $fileAttributes = [];
        foreach ($remoteFiles as $remoteFile) {

            $this->handle = self::common($folderPath . '/' . $remoteFile);
            curl_setopt($this->handle, CURLOPT_NOBODY, 1);
            curl_setopt($this->handle, CURLOPT_FILETIME, 1);
            curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($this->handle);

            if ($result) {
                $timestamp = curl_getinfo($this->handle, CURLINFO_FILETIME);
                $file = [];
                $file['name'] = $remoteFile;
                $file['last_modified'] = ($timestamp != -1)? date('Y-m-d H:i:s', $timestamp): null;
                $fileAttributes[] = $file;
            }
        }

        usort($fileAttributes, function ($first, $second) {
            return date($second['last_modified']) <=> date($first['last_modified']);
        });

        return $fileAttributes;
    }

    /**
     * Raw list of files
     *
     * @param string $folderPath
     *
     * @return array|bool
     */
    public function rawList(string $folderPath)
    {
        if (substr($folderPath, -1) != '/') {
            $folderPath .= '/';
        }

        $this->handle = self::common($folderPath);
        curl_setopt($this->handle, CURLOPT_UPLOAD, 0);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($this->handle);

        if (curl_error($this->handle)) {
            return false;
        } else {
            $remoteFiles = explode("\n", trim($result));
            return $remoteFiles;
        }
    }

    /**
     * Attribute list of files
     *
     * @param string $folderPath
     *
     * @return array|bool
     */
    public function attributeList(string $folderPath)
    {
        $remoteFiles = $this->rawList($folderPath);
        if (!empty($remoteFiles)) {
            $files = [];
            foreach($remoteFiles as $remoteFile) {
                $fileAttributes = preg_split("/\s+/", $remoteFile);
                list(
                    $attribute['rights'],
                    $attribute['number'],
                    $attribute['user'],
                    $attribute['group'],
                    $attribute['size'],
                    $attribute['month'],
                    $attribute['day'],
                    $attribute['time']
                ) = $fileAttributes;
                array_splice($fileAttributes, 0, 8);
                $attribute['name'] = trim(implode(' ', $fileAttributes));
                $attribute['type'] = (substr($attribute['rights'], 0, 1) === 'd')? 'directory': 'file';
                $files[] = $attribute;
            }
            return $files;
        }

        return false;
    }
}

