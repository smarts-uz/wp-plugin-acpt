<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Package\Storage;

use Duplicator\Libs\Chunking\ChunkingManager;
use Duplicator\Libs\Chunking\Iterators\ChunkFileCopyIterator;
use Duplicator\Models\Storages\StorageAdapterInterface;
use Exception;

/**
 * Chunk manager for storage uploads
 */
class StorageUploadChunkFiles extends ChunkingManager
{
    /** @var int<0, max> */
    protected $chunkSize = 0;
    /** @var StorageAdapterInterface */
    protected $adapter = null;

    /**
     * Class contructor
     *
     * @param mixed $extraData    extra data for manager used on extended classes
     * @param int   $maxIteration max number of iterations
     * @param int   $timeOut      timeout in milliseconds
     * @param int   $throttling   throttling lin milliseconds
     */
    public function __construct($extraData = null, $maxIteration = 0, $timeOut = 0, $throttling = 0)
    {
        $this->chunkSize = $extraData['chunkSize'];
        if (!$extraData['adapter'] instanceof StorageAdapterInterface) {
            throw new Exception('Adapter must be an instance of StorageAdapterInterface');
        }
        $this->adapter = $extraData['adapter'];
        parent::__construct($extraData, $maxIteration, $timeOut, $throttling);
    }

        /**
         * Execute chunk action
         *
         * @param string                    $key     the current key
         * @param array<string, string|int> $current the current element
         *
         * @return bool
         */
    protected function action($key, $current)
    {
        $current = $this->it->current();
        if (strlen($current['from']) == 0) {
            return true;
        }

        if (is_file($current['from'])) {
            return $this->adapter->copyToStorage($current['from'], $current['to'], $current['offset'], $this->chunkSize);
        } elseif (is_dir($current['from'])) {
            return $this->adapter->createDir($current['to']);
        } else {
            return false;
        }
    }

        /**
         * Return iterator
         *
         * @param array<string, mixed> $extraData extra data for manager used on extended classes
         *
         * @return ChunkFileCopyIterator
         */
    protected function getIterator($extraData = null)
    {
        $it = new ChunkFileCopyIterator($extraData['replacements'], $extraData['chunkSize']);
        $it->setTotalSize();
        return $it;
    }

    /**
     * Return persistance adapter
     *
     * @param mixed $extraData extra data for manager used on extended classes
     *
     * @return UploadPackageFilePersistanceAdapter
     */
    protected function getPersistance($extraData = null)
    {
        return new UploadPackageFilePersistanceAdapter($extraData['upload_info'], $extraData['package']);
    }
}
