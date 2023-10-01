<?php

namespace Arifhp86\ClearExpiredCacheFile\Events;

use Arifhp86\ClearExpiredCacheFile\Support\FileRegister;
use Illuminate\Foundation\Events\Dispatchable;

class GarbageCollectionEnded
{
    use Dispatchable;

    public $time;
    public $memory;
    public $expiredFiles;
    public $activeFiles;
    public $deletedDirectories;

    public function __construct(
        int $time,
        int $memory,
        FileRegister $expiredFiles,
        FileRegister $activeFiles,
        int $deletedDirectories
    ) {
        $this->time = $time;
        $this->memory = $memory;
        $this->expiredFiles = $expiredFiles;
        $this->activeFiles = $activeFiles;
        $this->deletedDirectories = $deletedDirectories;
    }
}