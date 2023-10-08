<?php

namespace Arifhp86\ClearExpiredCacheFile\Events;

use Exception;
use Illuminate\Foundation\Events\Dispatchable;

class GarbageCollectionFailed
{
    use Dispatchable;

    public $exception;

    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }
}
