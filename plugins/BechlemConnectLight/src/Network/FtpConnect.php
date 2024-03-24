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

use Cake\Log\Log;
use Psr\Log\LogLevel;
use Exception;

/**
 * FtpConnect Model
 *
 * @method void alloc(int $filesize, string & $result) - Allocates space for a file to be uploaded
 * @method void cdUp() - Changes to the parent directory
 * @method void chDir(string $directory) - Changes the current directory on a FTP server
 * @method int chMod(int $mode, string $filename) - Set permissions on a file via FTP
 * @method void close() - Closes an FTP connection
 * @method void connect(string $host, int $port = 21, int $timeout = 90) - Opens an FTP connection
 * @method void delete(string $path) - Deletes a file on the FTP server
 * @method bool exec(string $command) - Requests execution of a command on the FTP server
 * @method void fGet(resource $handle, string $remote_file, int $mode, int $resumepos = 0) - Downloads a file from the FTP server and saves to an open file
 * @method void fPut(string $remote_file, resource $handle, int $mode, int $startpos = 0) - Uploads from an open file to the FTP server
 * @method mixed getOption(int $option) - Retrieves various runtime behaviours of the current FTP stream
 * @method void get(string $local_file, string $remote_file, int $mode, int $resumepos = 0) - Downloads a file from the FTP server
 * @method void login(string $username, string $password) - Logs in to an FTP connection
 * @method int mdTm(string $remote_file) - Returns the last modified time of the given file
 * @method string mkDir(string $directory) - Creates a directory
 * @method int nbContinue() - Continues retrieving/sending a file(non-blocking)
 * @method int nbFGet(resource $handle, string $remote_file, int $mode, int $resumepos = 0) - Retrieves a file from the FTP server and writes it to an open file(non-blocking)
 * @method int nbFPut(string $remote_file, resource $handle, int $mode, int $startpos = 0) - Stores a file from an open file to the FTP server(non-blocking)
 * @method int nbGet(string $local_file, string $remote_file, int $mode, int $resumepos = 0) - Retrieves a file from the FTP server and writes it to a local file(non-blocking)
 * @method int nbPut(string $remote_file, string $local_file, int $mode, int $startpos = 0) - Stores a file on the FTP server(non-blocking)
 * @method array nList(string $directory) - Returns a list of files in the given directory
 * @method void pasv(bool $pasv) - Turns passive mode on or off
 * @method void put(string $remote_file, string $local_file, int $mode, int $startpos = 0) - Uploads a file to the FTP server
 * @method string pwd() - Returns the current directory name
 * @method void quit() - Closes an FTP connection(alias of close)
 * @method array raw(string $command) - Sends an arbitrary command to an FTP server
 * @method mixed rawList(string $directory, bool $recursive = false) - Returns a detailed list of files in the given directory
 * @method void rename(string $oldname, string $newname) - Renames a file or a directory on the FTP server
 * @method void rmDir(string $directory) - Removes a directory
 * @method bool setOption(int $option, mixed $value) - Set miscellaneous runtime FTP options
 * @method void site(string $command) - Sends a SITE command to the server
 * @method int size(string $remote_file) - Returns the size of the given file
 * @method void sslConnect(string $host, int $port = 21, int $timeout = 90) - Opens an Secure SSL-FTP connection
 * @method string sysType() - Returns the system type identifier of the remote FTP server
 */
class FtpConnect
{

    /**#@+ FTP constant alias */
    const ASCII = FTP_ASCII;
    const TEXT = FTP_TEXT;
    const BINARY = FTP_BINARY;
    const IMAGE = FTP_IMAGE;
    const TIMEOUT_SEC = FTP_TIMEOUT_SEC;
    const AUTOSEEK = FTP_AUTOSEEK;
    const AUTORESUME = FTP_AUTORESUME;
    const FAILED = FTP_FAILED;
    const FINISHED = FTP_FINISHED;
    const MOREDATA = FTP_MOREDATA;
    /**#@-*/

    private static $aliases = array(
        'sslconnect' => 'ssl_connect',
        'getoption' => 'get_option',
        'setoption' => 'set_option',
        'nbcontinue' => 'nb_continue',
        'nbfget' => 'nb_fget',
        'nbfput' => 'nb_fput',
        'nbget' => 'nb_get',
        'nbput' => 'nb_put',
    );

    /** @var resource */
    private $resource;

    /** @var array */
    private $state;

    /** @var string */
    private $errorMsg;

    /**
     * Constructor
     *
     * FtpConnect constructor.
     * @param string|null $url
     * @param bool $passiveMode
     */
    public function __construct(string $url = null, $passiveMode = true)
    {
        if (!extension_loaded('ftp')) {
            Log::write(LogLevel::ERROR, __d('bechlem_connect_light', 'PHP extension FTP is not loaded.'));
            throw new Exception(__d('bechlem_connect_light', 'PHP extension FTP is not loaded.'));
        }
        if ($url) {
            $parts = parse_url($url);
            if (!isset($parts['scheme']) || !in_array($parts['scheme'], array('ftp', 'ftps', 'sftp'))) {
                Log::write(LogLevel::ERROR, __d('bechlem_connect_light', 'Invalid URL.'));
                throw new Exception(__d('bechlem_connect_light', 'Invalid URL.'));
            }
            $func = $parts['scheme'] === 'ftp' ? 'connect' : 'ssl_connect';
            $this->$func($parts['host'], empty($parts['port']) ? null : (int)$parts['port']);
            $this->login(urldecode($parts['user']), urldecode($parts['pass']));
            $this->pasv((bool)$passiveMode);
            if (isset($parts['path'])) {
                $this->chdir($parts['path']);
            }
        }
    }

    /**
     * Magic method (do not call directly).
     *
     * @param $name
     * @param $args
     * @return mixed|null
     */
    public function __call($name, $args)
    {
        $name = strtolower($name);
        $silent = strncmp($name, 'try', 3) === 0;
        $func = $silent ? substr($name, 3) : $name;
        $func = 'ftp_' . (isset(self::$aliases[$func])? self::$aliases[$func]: $func);

        if (!function_exists($func)) {
            Log::write(
                LogLevel::ERROR,
                __d(
                    'bechlem_connect_light',
                    'Call to undefined method Ftp::{name}().',
                    ['name' => $name]
                )
            );
            throw new Exception(
                __d(
                    'bechlem_connect_light',
                    'Call to undefined method Ftp::{name}().',
                    ['name' => $name]
                )
            );
        }

        $this->errorMsg = null;
        set_error_handler([$this, '_errorHandler']);

        if ($func === 'ftp_connect' || $func === 'ftp_ssl_connect') {
            $this->state = [$name => $args];
            $this->resource = call_user_func_array($func, $args);
            $res = null;
        } elseif (!is_resource($this->resource)) {
            restore_error_handler();
            Log::write(LogLevel::ERROR, __d('bechlem_connect_light', 'Not connected to FTP server. Call connect() or ssl_connect() first.'));
            throw new Exception(
                __d('bechlem_connect_light', 'Not connected to FTP server. Call connect() or ssl_connect() first.')
            );
        } else {
            if ($func === 'ftp_login' || $func === 'ftp_pasv') {
                $this->state[$name] = $args;
            }

            array_unshift($args, $this->resource);
            $res = call_user_func_array($func, $args);

            if ($func === 'ftp_chdir' || $func === 'ftp_cdup') {
                $this->state['chdir'] = [ftp_pwd($this->resource)];
            }
        }

        restore_error_handler();
        if (!$silent && $this->errorMsg !== null) {
            if (ini_get('html_errors')) {
                $this->errorMsg = html_entity_decode(strip_tags($this->errorMsg));
            }

            if (($a = strpos($this->errorMsg, ': ')) !== false) {
                $this->errorMsg = substr($this->errorMsg, $a + 2);
            }

            Log::write(LogLevel::ERROR, $this->errorMsg);
            throw new Exception($this->errorMsg);
        }

        return $res;
    }

    /**
     * Internal error handler. Do not call directly.
     *
     * @param $code
     * @param $message
     */
    public function _errorHandler($code, $message)
    {
        $this->errorMsg = $message;
    }

    /**
     * Reconnects to FTP server.
     *
     * @return void
     */
    public function reconnect()
    {
        @ftp_close($this->resource); // intentionally @
        foreach ($this->state as $name => $args) {
            call_user_func_array(array($this, $name), $args);
        }
    }

    /**
     * Checks if file or directory exists.
     *
     * @param $file
     * @return bool
     */
    public function fileExists($file)
    {
        return (bool)$this->nlist($file);
    }

    /**
     * Checks if directory exists.
     *
     * @param  string
     * @return bool
     * @throws Exception
     */
    public function isDir($dir)
    {
        $current = $this->pwd();
        try {
            $this->chdir($dir);
        } catch (Exception $ex) {
            Log::write(LogLevel::ERROR, $ex);
            throw new Exception((string) $ex);
        }
        $this->chdir($current);

        return empty($e);
    }

    /**
     * Recursive creates directories.
     *
     * @param  string
     * @return void
     */
    public function mkDirRecursive($dir)
    {
        $parts = explode('/', $dir);
        $path = '';
        while (!empty($parts)) {
            $path .= array_shift($parts);
            try {
                if ($path !== '') {
                    $this->mkdir($path);
                }
            } catch (Exception $ex) {
                if (!$this->isDir($path)) {
                    Log::write(
                        LogLevel::ERROR,
                        __d(
                            'bechlem_connect_light',
                            'Cannot create directory "{path}".',
                            ['path' => $path]
                        )
                    );
                    throw new Exception(
                        __d(
                            'bechlem_connect_light',
                            'Cannot create directory "{path}".',
                            ['path' => $path]
                        )
                    );
                }
            }
            $path .= '/';
        }
    }

    /**
     * Recursive deletes path.
     *
     * @param $path
     * @return void
     */
    public function deleteRecursive($path)
    {
        if (!$this->tryDelete($path)) {
            foreach ((array)$this->nlist($path) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $this->deleteRecursive(strpos($file, '/') === false ? "$path/$file" : $file);
                }
            }
            $this->rmdir($path);
        }
    }
}
