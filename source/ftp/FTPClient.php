<?php
namespace FTPClient;
use \Countable;

class FTPClient implements Countable
{
    protected $conn;
    private $ftp;

    /**
     * Constructor.
     *
     * @param  resource|null $connection
     * @throws FTPException  If FTP extension is not loaded.
     */
    public function __construct($connection = null)
    {
        if (!extension_loaded('ftp')) {
            throw new FTPException('FTP extension is not loaded!');
        }
        if ($connection) {
            $this->conn = $connection;
        }
        $this->setWrapper(new FTPWrapper($this->conn));
    }

    /**
     * Close the connection when the object is destroyed.
     */
    public function __destruct()
    {
        if ($this->conn) {
            $this->ftp->close();
        }
    }

    /**
     * Call an internal method or a FTP method handled by the wrapper.
     *
     * Wrap the FTP PHP functions to call as method of FTPClient object.
     * The connection is automaticaly passed to the FTP PHP functions.
     *
     * @param  string       $method
     * @param  array        $arguments
     * @return mixed
     * @throws FTPException When the function is not valid
     */
    public function __call($method, array $arguments)
    {
        return $this->ftp->__call($method, $arguments);
    }

    /**
     * Get the help information of the remote FTP server.
     *
     * @return array
     */
    public function help()
    {
        return $this->ftp->raw('help');
    }

    /**
     * Open a FTP connection.
     *
     * @param string $host
     * @param bool   $ssl
     * @param int    $port
     * @param int    $timeout
     *
     * @return FTPClient
     * @throws FTPException If unable to connect
     */
    public function connectFTP($host, $ssl = false, $port = 21, $timeout = 90)
    {
        if ($ssl) {
            $this->conn = $this->ftp->ssl_connect($host, $port, $timeout);
        } else {
            $this->conn = $this->ftp->connect($host, $port, $timeout);
        }
        if (!$this->conn) {
            throw new FTPException('Unable to connect');
        }
        return $this;
    }

    /**
     * Closes the current FTP connection.
     *
     * @return bool
     */
    public function close()
    {
        if ($this->conn) {
            $this->ftp->close();
            $this->conn = null;
        }
    }

    /**
     * Logs in to an FTP connection.
     *
     * @param string $username
     * @param string $password
     *
     * @return FTPClient
     * @throws FTPException If the login is incorrect
     */
    public function loginFTP($username = 'anonymous', $password = '')
    {
        $result = $this->ftp->login($username, $password);
        if ($result === false) {
            throw new FTPException('Login incorrect');
        }
        return $this;
    }

    /**
     * Returns the last modified time of the given file.
     * Return -1 on error
     *
     * @param string $remoteFile
     * @param string|null $format
     *
     * @return int
     */
    public function modifiedTime($remoteFile, $format = null)
    {
        $time = $this->ftp->mdtm($remoteFile);
        if ($time !== -1 && $format !== null) {
            return date($format, $time);
        }
        return $time;
    }

    /**
     * Changes to the parent directory.
     *
     * @throws FTPException
     * @return FTPClient
     */
    public function up()
    {
        $result = $this->ftp->cdup();
        if ($result === false) {
            throw new FTPException('Unable to get parent folder');
        }
        return $this;
    }

    /**
     * Returns a list of files in the given directory.
     *
     * @param string   $directory The directory, by default is '.' the current directory
     * @param bool     $recursive
     * @param callable $filter    A callable to filter the result, by default is asort() PHP function.
     *                            The result is passed in array argument,
     *                            must take the argument by reference !
     *                            The callable should proceed with the reference array
     *                            because is the behavior of several PHP sorting
     *                            functions (by reference ensure directly the compatibility
     *                            with all PHP sorting functions).
     *
     * @return array
     * @throws FTPException If unable to list the directory
     */
    public function nlist($directory = '.', $recursive = false, $filter = 'sort')
    {
        if (!$this->isDir($directory)) {
            throw new FTPException('"'.$directory.'" is not a directory');
        }
        $files = $this->ftp->nlist($directory);

        if ($files === false) {
            throw new FTPException('Unable to list directory');
        }
        $result  = array();
        $dir_len = strlen($directory);
        //If it's the current
        if (false !== ($kdot = array_search('.', $files))) {
            unset($files[$kdot]);
        }
        //If it's the parent
        if (false !== ($kdot = array_search('..', $files))) {
            unset($files[$kdot]);
        }
        if (!$recursive) {
            $result = $files;
            //Working with the reference (behavior of several PHP sorting functions)
            $filter($result);
            return $result;
        }
        //Utils for recursion
        $flatten = function(array $arr) use (&$flatten) {
            $flat = [];
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $flat = array_merge($flat, $flatten($v));
                } else {
                    $flat[] = $v;
                }
            }
            return $flat;
        };
        foreach ($files as $file) {
            $file = $directory.'/'.$file;
            //If contains the root path (behavior of the recursivity)
            if (0 === strpos($file, $directory, $dir_len)) {
                $file = substr($file, $dir_len);
            }
            if ($this->isDir($file)) {
                $result[] = $file;
                $items    = $flatten($this->nlist($file, true, $filter));
                foreach ($items as $item) {
                    $result[] = $item;
                }
            } else {
                $result[] = $file;
            }
        }
        $result = array_unique($result);
        $filter($result);
        return $result;
    }

    /**
     * Creates a directory.
     *
     * @see FTPClient::rmdir()
     * @see FTPClient::remove()
     * @see FTPClient::put()
     * @see FTPClient::putAll()
     *
     * @param  string $directory The directory
     * @param  bool   $recursive
     * @return array
     */
    public function mkdir($directory, $recursive = false)
    {
        if (!$recursive or $this->isDir($directory) === false) {
            return @$this->ftp->mkdir($directory);
        }
        $result = false;
        $pwd = $this->ftp->pwd();
        $parts = explode('/', $directory);
        foreach ($parts as $part) {
            if ($part == '') {
                continue;
            }
            if (!$this->ftp->chdir($part)) {
                $result = $this->ftp->mkdir($part);
                $this->ftp->chdir($part);
            }
        }
        $this->ftp->chdir($pwd);
        return $result;
    }

    /**
     * Remove a directory.
     *
     * @see FTPClient::mkdir()
     * @see FTPClient::cleanDir()
     * @see FTPClient::remove()
     * @see FTPClient::delete()
     * @param  string       $directory
     * @param  bool         $recursive Forces deletion if the directory is not empty
     * @return bool
     * @throws FTPException If unable to list the directory to remove
     */
    public function rmdir($directory, $recursive = true)
    {
        if ($recursive) {
            $files = $this->nlist($directory, false, 'rsort');
            //Remove children
            foreach ($files as $file) {
                $this->remove($file, true);
            }
        }
        //Remove the directory
        return $this->ftp->rmdir($directory);
    }

    /**
     * Empty directory.
     *
     * @see FTPClient::remove()
     * @see FTPClient::delete()
     * @see FTPClient::rmdir()
     *
     * @param  string $directory
     * @return bool
     */
    public function cleanDir($directory)
    {
        if (!$files = $this->nlist($directory)) {
            return $this->isEmpty($directory);
        }
        //Remove children
        foreach ($files as $file) {
            $this->remove($file, true);
        }
        return $this->isEmpty($directory);
    }

    /**
     * Remove a file or a directory.
     *
     * @see FTPClient::rmdir()
     * @see FTPClient::cleanDir()
     * @see FTPClient::delete()
     * @param  string $path      The path of the file or directory to remove
     * @param  bool   $recursive Is effective only if $path is a directory, {@see FTPClient::rmdir()}
     * @return bool
     */
    public function remove($path, $recursive = false)
    {
        try {
            if ($this->ftp->delete($path) or ($this->isDir($path) and $this->rmdir($path, $recursive))) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a directory exist.
     *
     * @param string $directory
     * @return bool
     * @throws FTPException
     */
    public function isDir($directory)
    {
        $pwd = $this->ftp->pwd();
        if ($pwd === false) {
            throw new FTPException('Unable to resolve the current directory');
        }
        $checkDirExists = $this->ftp->nlist(dirname($directory));
        if ($checkDirExists !== false && is_array($checkDirExists)) {
            if (in_array($directory, $checkDirExists)) {  
                if ($this->ftp->chdir($directory)) {
                    $this->ftp->chdir($pwd);
                    return true;
                }
            } else {
                $this->ftp->mkdir($directory);
            }
        }
        $this->ftp->chdir($pwd);
        return false;
    }

    /**
     * Check if a directory is empty.
     *
     * @param  string $directory
     * @return bool
     */
    public function isEmpty($directory)
    {
        return $this->count($directory, null, false) === 0 ? true : false;
    }

    /**
     * Scan a directory and returns the details of each item.
     *
     * @see FTPClient::nlist()
     * @see FTPClient::rawlist()
     * @see FTPClient::parseRawList()
     * @see FTPClient::dirSize()
     * @param  string $directory
     * @param  bool   $recursive
     * @return array
     */
    public function scanDir($directory = '.', $recursive = false)
    {
        return $this->parseRawList($this->rawlist($directory, $recursive));
    }

    /**
     * Returns the total size of the given directory in bytes.
     *
     * @param  string $directory The directory, by default is the current directory.
     * @param  bool   $recursive true by default
     * @return int    The size in bytes.
     */
    public function dirSize($directory = '.', $recursive = true)
    {
        $items = $this->scanDir($directory, $recursive);
        $size  = 0;
        foreach ($items as $item) {
            $size += (int) $item['size'];
        }
        return $size;
    }

    /**
     * Count the items (file, directory, link, unknown).
     *
     * @param  string      $directory The directory, by default is the current directory.
     * @param  string|null $type      The type of item to count (file, directory, link, unknown)
     * @param  bool        $recursive true by default
     * @return int
     */
    public function count($directory = '.', $type = null, $recursive = true)
    {
        $items  = (null === $type ? $this->nlist($directory, $recursive) : $this->scanDir($directory, $recursive));
        $count = 0;
        foreach ($items as $item) {
            if (null === $type or $item['type'] == $type) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Downloads a file from the FTP server into a string
     *
     * @param  string $remote_file
     * @param  int    $mode
     * @param  int    $resumepos
     * @return string|null
     */
    public function getContent($remote_file, $mode = FTP_BINARY, $resumepos = 0)
    {
        $handle = fopen('php://temp', 'r+');
        if ($this->fget($handle, $remote_file, $mode, $resumepos)) {
            rewind($handle);
            return stream_get_contents($handle);
        }
        return null;
    }

    /**
     * Uploads a file to the server from a string.
     *
     * @param  string       $remote_file
     * @param  string       $content
     * @return FTPClient
     * @throws FTPException When the transfer fails
     */
    public function putFromString($remote_file, $content)
    {
        $handle = fopen('php://temp', 'w');
        fwrite($handle, $content);
        rewind($handle);
        if ($this->ftp->fput($remote_file, $handle, FTP_BINARY)) {
            return $this;
        }
        throw new FTPException('Unable to put the file "'.$remote_file.'"');
    }

    /**
     * Uploads a file to the server.
     *
     * @param  string       $local_file
     * @return FTPClient
     * @throws FTPException When the transfer fails
     */
    public function putFromPath($local_file)
    {
        $remote_file = basename($local_file);
        $handle      = fopen($local_file, 'r');
        if ($this->ftp->fput($remote_file, $handle, FTP_BINARY)) {
            rewind($handle);
            return $this;
        }
        throw new FTPException('Unable to put the remote file from the local file "'.$local_file.'"');
    }

    /**
     * Upload files.
     *
     * @param  string    $source_directory
     * @param  string    $target_directory
     * @param  int       $mode
     * @return FTPClient
     */
    public function putAll($source_directory, $target_directory, $mode = FTP_BINARY)
    {
        $d = dir($source_directory);
        try {
            //Do this for each file in the directory
            while ($file = $d->read()) {
                //To prevent an infinite loop
                if ($file != '.' && $file != '..') {
                    //Do the following if it is a directory
                    if (is_dir($source_directory.'/'.$file)) {
                        if (!$this->isDir($target_directory.'/'.$file)) {
                            //Create directories that do not yet exist
                            @$this->ftp->mkdir($target_directory.'/'.$file);
                        }
                        //Recursive part
                        $this->putAll(
                            $source_directory.'/'.$file,
                            $target_directory.'/'.$file,
                            $mode
                        );
                    } else {
                        //Put the files
                        $this->ftp->put(
                            $target_directory.'/'.$file,
                            $source_directory.'/'.$file,
                            $mode
                        );
                    }
                }
            }
        } catch (FTPException $e) {
            return false;
        }
        return $this;
    }

    /**
     * Downloads all files from remote FTP directory
     *
     * @param  string $source_directory The remote directory
     * @param  string $target_directory The local directory
     * @param  int    $mode
     * @return FTPClient
     */
    public function getAll($source_directory, $target_directory, $mode = FTP_BINARY)
    {
        if ($source_directory != '.') {
            if ($this->ftp->chdir($source_directory) == false) {
                throw new FTPException('Unable to change directory: '.$source_directory);
            }
            if (!$this->isDir($source_directory)) {
                if (!$this->ftp->chdir($source_directory)) {
                    return false;
                } else {
                    $this->ftp->chdir($source_directory);
                }
            }
        }
        $contents = $this->ftp->nlist('.');
        foreach ($contents as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $this->ftp->get($target_directory.'/'.$file, $file, $mode);
        }
        $this->ftp->chdir('..');
        chdir('..');
        return $this;
    }

    /**
     * Returns a detailed list of files in the given directory.
     *
     * @see FTPClient::nlist()
     * @see FTPClient::scanDir()
     * @see FTPClient::dirSize()
     * @param  string       $directory The directory, by default is the current directory
     * @param  bool         $recursive
     * @return array
     * @throws FTPException
     */
    public function rawlist($directory = '.', $recursive = false)
    {
        if (!$this->isDir($directory)) {
            throw new FTPException('"'.$directory.'" is not a directory.');
        }
        if (strpos($directory, ' ') > 0) {
            $ftproot = $this->ftp->pwd();
            $this->ftp->chdir($directory);
            $list  = $this->ftp->rawlist('');
            $this->ftp->chdir($ftproot);
        } else {
            $list  = $this->ftp->rawlist($directory);
        }
        $items = array();
        if (!$list) {
            return $items;
        }
        if (false == $recursive) {
            foreach ($list as $path => $item) {
                $chunks = preg_split("/\s+/", $item);
                //If not 'name'
                if (empty($chunks[8]) || $chunks[8] == '.' || $chunks[8] == '..') {
                    continue;
                }
                $path = $directory.'/'.$chunks[8];
                if (isset($chunks[9])) {
                    $nbChunks = count($chunks);

                    for ($i = 9; $i < $nbChunks; $i++) {
                        $path .= ' '.$chunks[$i];
                    }
                }
                if (substr($path, 0, 2) == './') {
                    $path = substr($path, 2);
                }
                $items[ $this->rawToType($item).'#'.$path ] = $item;
            }
            return $items;
        }
        $path = '';
        foreach ($list as $item) {
            $len = strlen($item);
            if (!$len
            // '.'
            || ($item[$len-1] == '.' && $item[$len-2] == ' '
            // '..'
            or $item[$len-1] == '.' && $item[$len-2] == '.' && $item[$len-3] == ' ')
            ) {
                continue;
            }
            $chunks = preg_split("/\s+/", $item);
            //If not "name"
            if (empty($chunks[8]) || $chunks[8] == '.' || $chunks[8] == '..') {
                continue;
            }
            $path = $directory.'/'.$chunks[8];
            if (isset($chunks[9])) {
                $nbChunks = count($chunks);
                for ($i = 9; $i < $nbChunks; $i++) {
                    $path .= ' '.$chunks[$i];
                }
            }
            if (substr($path, 0, 2) == './') {
                $path = substr($path, 2);
            }
            $items[$this->rawToType($item).'#'.$path] = $item;
            if ($item[0] == 'd') {
                $sublist = $this->rawlist($path, true);
                foreach ($sublist as $subpath => $subitem) {
                    $items[$subpath] = $subitem;
                }
            }
        }
        return $items;
    }

    /**
     * Parse raw list.
     *
     * @see FTPClient::rawlist()
     * @see FTPClient::scanDir()
     * @see FTPClient::dirSize()
     * @param  array $rawlist
     * @return array
     */
    public function parseRawList(array $rawlist)
    {
        $items = array();
        $path  = '';
        foreach ($rawlist as $key => $child) {
            $chunks = preg_split("/\s+/", $child, 9);
            if (isset($chunks[8]) && ($chunks[8] == '.' or $chunks[8] == '..')) {
                continue;
            }
            if (count($chunks) === 1) {
                $len = strlen($chunks[0]);
                if ($len && $chunks[0][$len-1] == ':') {
                    $path = substr($chunks[0], 0, -1);
                }
                continue;
            }
            // Prepare for filename that has space
            $nameSlices = array_slice($chunks, 8, true);
            $item = [
                'permissions' => $chunks[0],
                'number'      => $chunks[1],
                'owner'       => $chunks[2],
                'group'       => $chunks[3],
                'size'        => $chunks[4],
                'month'       => $chunks[5],
                'day'         => $chunks[6],
                'time'        => $chunks[7],
                'name'        => implode(' ', $nameSlices),
                'type'        => $this->rawToType($chunks[0]),
            ];
            if ($item['type'] == 'link' && isset($chunks[10])) {
                $item['target'] = $chunks[10]; // 9 is "->"
            }
            //If the key is not the path, behavior of ftp_rawlist() PHP function
            if (is_int($key) || false === strpos($key, $item['name'])) {
                array_splice($chunks, 0, 8);
                $key = $item['type'].'#'
                    .($path ? $path.'/' : '')
                    .implode(' ', $chunks);
                if ($item['type'] == 'link') {
                    //Get the first part of 'link#the-link.ext -> /path/of/the/source.ext'
                    $exp = explode(' ->', $key);
                    $key = rtrim($exp[0]);
                }
                $items[$key] = $item;
            } else {
                //The key is the path, behavior of FTPClient::rawlist() method()
                $items[$key] = $item;
            }
        }
        return $items;
    }

    /**
     * Convert raw info (drwx---r-x ...) to type (file, directory, link, unknown).
     * Only the first char is used for resolving.
     *
     * @param  string $permission Example : drwx---r-x
     *
     * @return string The file type (file, directory, link, unknown)
     * @throws FTPException
     */
    public function rawToType($permission)
    {
        if (!is_string($permission)) {
            throw new FTPException('The "$permission" argument must be a string, "'.gettype($permission).'" given.');
        }
        if (empty($permission[0])) {
            return 'unknown';
        }
        switch ($permission[0]) {
            case '-':
                return 'file';

            case 'd':
                return 'directory';

            case 'l':
                return 'link';

            default:
                return 'unknown';
        }
    }

    /**
     * Set the wrapper which forward the PHP FTP functions to use in FTPClient instance.
     *
     * @param  FTPWrapper $wrapper
     * @return FTPClient
     */
    protected function setWrapper(FTPWrapper $wrapper)
    {
        $this->ftp = $wrapper;
        return $this;
    }
}
