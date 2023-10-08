<?php

namespace Arifhp86\ClearExpiredCacheFile;

use Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionEnded;
use Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionStarting;
use Arifhp86\ClearExpiredCacheFile\Support\FileRegister;
use Arifhp86\ClearExpiredCacheFile\Support\Formatter;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Wilderborn\Partyline\Facade as Partyline;

class CacheGarbageCollector
{
    private $filesystem;

    private $expiredFiles;

    private $activeFiles;

    private $disableDirectoryDelete = false;

    private $isDryRun = false;

    private $deletedDirectories = 0;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->expiredFiles = new FileRegister();
        $this->activeFiles = new FileRegister();
    }

    public function collectGarbage()
    {
        GarbageCollectionStarting::dispatch();

        $start = microtime(true);
        $this->deleteExpiredFiles()->deleteEmptyDirectories();

        $timeInSeconds = microtime(true) - $start;
        $memoryInBytes = memory_get_peak_usage(true);

        $time = Formatter::formatDuration($timeInSeconds);
        $memory = Formatter::formatSpace($memoryInBytes);

        Partyline::line("Duration: {$time}, Memory: {$memory}");

        GarbageCollectionEnded::dispatch(
            $timeInSeconds,
            $memoryInBytes,
            $this->expiredFiles,
            $this->activeFiles,
            $this->deletedDirectories
        );
    }

    public function setDisableDirectoryDelete(bool $disableDirectoryDelete): self
    {
        $this->disableDirectoryDelete = $disableDirectoryDelete;

        return $this;
    }

    public function setIsDryRun(bool $isDryRun): self
    {
        $this->isDryRun = $isDryRun;

        return $this;
    }

    protected function deleteExpiredFiles(): self
    {
        Partyline::line('Scanning cache files...');

        foreach ($this->filesystem->allFiles() as $file) {
            if ($this->notACacheFile($file)) {
                continue;
            }

            $size = $this->filesystem->size($file);
            if ($this->fileIsExpired($file)) {
                $this->expiredFiles->add($size);
                if (! $this->isDryRun) {
                    $this->filesystem->delete($file);
                }
            } else {
                $this->activeFiles->add($size);
            }
        }

        if (! $this->expiredFiles->getCount()) {
            Partyline::line('No expired cache file found!');
        } else {
            $count = $this->expiredFiles->getCount();
            $size = $this->expiredFiles->getFormattedSize();
            Partyline::info("{$count} ({$size}) expired cache files deleted.");
        }

        if (! $this->activeFiles->getCount()) {
            Partyline::line('0 cache file remaining.');
        } else {
            $count = $this->activeFiles->getCount();
            $size = $this->activeFiles->getFormattedSize();
            Partyline::info("{$count} ({$size}) cache file remaining.");
        }
        Partyline::newLine();

        return $this;
    }

    protected function deleteEmptyDirectories(): self
    {
        if ($this->disableDirectoryDelete || $this->isDryRun) {
            return $this;
        }

        Partyline::line('Scanning directories...');

        foreach (array_reverse($this->filesystem->allDirectories()) as $dir) {
            if (! $this->directoryIsEmpty($dir)) {
                continue;
            }

            $this->filesystem->deleteDirectory($dir);
            $this->deletedDirectories += 1;
        }

        if (! $this->deletedDirectories) {
            Partyline::line('No empty directory found!');
        } else {
            Partyline::info("{$this->deletedDirectories} empty directories deleted.");
        }

        Partyline::newLine();

        return $this;
    }

    protected function directoryIsEmpty(string $dir): bool
    {
        return count(scandir($this->filesystem->path($dir))) === 2;
    }

    protected function fileIsExpired(string $file): bool
    {
        return Carbon::now()->timestamp >= $this->getExpireDate($file);
    }

    protected function getExpireDate(string $file): int
    {
        $handle = fopen($this->filesystem->path($file), 'r');
        $expire = fread($handle, 10);
        fclose($handle);

        return $expire;
    }

    protected function notACacheFile(string $name): bool
    {
        return substr($name, 0, 1) === '.';
    }
}
