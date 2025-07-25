<?php

namespace Duplicator\Models\Storages;

interface StorageAdapterInterface
{
    /**
     * Initialize the storage on creation.
     *
     * @param string $errorMsg The error message if storage is invalid.
     *
     * @return bool true on success or false on failure.
     */
    public function initialize(&$errorMsg = '');

    /**
     * Destroy the storage on deletion.
     *
     * @return bool true on success or false on failure.
     */
    public function destroy();

    /**
     * Check if storage is valid and ready to use.
     *
     * @param string $errorMsg The error message if storage is invalid.
     *
     * @return bool
     */
    public function isValid(&$errorMsg = '');

    /**
     * Check if path exists and is a directory.
     *
     * @param string $path The path to check.
     *
     * @return bool
     */
    public function isDir($path);

    /**
     * Create the directory specified by pathname, recursively if necessary.
     *
     * @param string $path The directory path.
     *
     * @return bool true on success or false on failure.
     */
    public function createDir($path);

    /**
     * Check if path exists and is a file.
     *
     * @param string $path The path to check.
     *
     * @return bool
     */
    public function isFile($path);

    /**
     * Create file with content.
     *
     * @param string $path    The path to file.
     * @param string $content The content of file.
     *
     * @return false|int The number of bytes that were written to the file, or false on failure.
     */
    public function createFile($path, $content);

    /**
     * Path dir or file exists.
     *
     * @param string $path The path to check. If empty, check root path.
     *
     * @return bool
     */
    public function exists($path);

    /**
     * Delete reletative path from storage root.
     *
     * @param string $path      The path to delete. (Accepts directories and files)
     * @param bool   $recursive Allows the deletion of nested directories specified in the pathname. Default to false.
     *
     * @return bool true on success or false on failure.
     */
    public function delete($path, $recursive = false);

    /**
     * Get file content.
     *
     * @param string $path The path to file.
     *
     * @return string|false The content of file or false on failure.
     */
    public function getFileContent($path);

    /**
     * Move and/or rename a file or directory.
     *
     * @param string $oldPath Relative storage path
     * @param string $newPath Relative storage path
     *
     * @return bool true on success or false on failure.
     */
    public function move($oldPath, $newPath);

    /**
     * Get path info.
     *
     * @param string $path Relative storage path, if empty, return root path info.
     *
     * @return StoragePathInfo|false The path info or false if path is invalid.
     */
    public function getPathInfo($path);

    /**
     * Get the list of files and directories inside the specified path.
     *
     * @param string $path    Relative storage path, if empty, scan root path.
     * @param bool   $files   If true, add files to the list. Default to true.
     * @param bool   $folders If true, add folders to the list. Default to true.
     *
     * @return string[] The list of files and directories, empty array if path is invalid.
     */
    public function scanDir($path, $files = true, $folders = true);

    /**
     * Check if directory is empty.
     *
     * @param string   $path    The folder path
     * @param string[] $filters Filters to exclude files and folders from the check, if start and end with /, use regex.
     *
     * @return bool True is ok, false otherwise
     */
    public function isDirEmpty($path, $filters = []);

    /**
     * Copy local file to storage, partial copy is supported.
     * If destination file exists, it will be overwritten.
     * If offset is less than the destionation file size, the file will be truncated.
     *
     * @param string     $sourceFile  The source file full path
     * @param string     $storageFile Storage destination path
     * @param int<0,max> $offset      The offset where the data starts.
     * @param int        $length      The maximum number of bytes read. Default to -1 (read all the remaining buffer).
     *
     * @return bool true on success or false on failure.
     */
    public function copyToStorage($sourceFile, $storageFile, $offset = 0, $length = -1);
}
