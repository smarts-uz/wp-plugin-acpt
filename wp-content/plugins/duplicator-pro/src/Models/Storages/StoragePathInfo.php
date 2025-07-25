<?php

namespace Duplicator\Models\Storages;

class StoragePathInfo
{
    /** @var bool */
    public $isDir = false;
    /** @var string */
    public $path = '';
    /** @var int */
    public $size = 0;
    /** @var int */
    public $created = 0;
    /** @var int */
    public $modified = 0;
}
