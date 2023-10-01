<?php

namespace Arifhp86\ClearExpiredCacheFile\Support;

class FileRegister
{
    protected $count = 0;
    protected $size = 0;

    public function add(int $size)
    {
        $this->count += 1;
        $this->size += $size;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getFormattedSize(): string
    {
        return Formatter::formatSpace($this->size);
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
