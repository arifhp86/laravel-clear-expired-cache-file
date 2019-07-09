<?php

namespace Arifhp86\ClearExpiredCacheFile\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ClearExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all expired cache file/folder';

    private $expiredFileCount;
    private $expiredFileSize;
    private $activeFileCount;
    private $activeFileSize;

    public function __construct()
    {
        parent::__construct();

        $cacheDisk = [
            'driver' => 'local',
            'root' => config('cache.stores.file.path')
        ];

        config(['filesystems.disks.fcache' => $cacheDisk]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->deleteExpiredFiles();
        $this->deleteEmptyFolders();
        $this->showResults();
    }

    private function deleteExpiredFiles()
    {
        $files = Storage::disk('fcache')->allFiles();
        $this->output->progressStart(count($files));

        // Loop the files and get rid of any that have expired
        foreach($files as $key => $cachefile) {
            // Ignore files that named with dot(.) at the begining e.g. .gitignore
            if(substr($cachefile, 0, 1) == '.') {
                continue;
            }

            // Grab the contents of the file
            $contents = Storage::disk('fcache')->get($cachefile);

            // Get the expiration time
            $expire = substr($contents, 0, 10);

            // See if we have expired
            if(time() >= $expire) {
                // Delete the file
                $this->expiredFileSize += Storage::disk('fcache')->size($cachefile);
                Storage::disk('fcache')->delete($cachefile);
                $this->expiredFileCount++;
            } else {
                $this->activeFileCount++;
                $this->activeFileSize += Storage::disk('fcache')->size($cachefile);
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }

    private function deleteEmptyFolders()
    {
        $directories = Storage::disk('fcache')->allDirectories();
        $dirCount = count($directories);
        // looping backward to make sure subdirectories are deleted first
        while(--$dirCount >= 0) {
            if(!Storage::disk('fcache')->allFiles($directories[$dirCount])) {
                Storage::disk('fcache')->deleteDirectory($directories[$dirCount]);
            }
        }
    }

    public function showResults()
    {
        $expiredFileSize = $this->formatBytes($this->expiredFileSize);
        $activeFileSize = $this->formatBytes($this->activeFileSize);
        
        if($this->expiredFileCount) {
            $this->info("✔ {$this->expiredFileCount} expired cache files removed");
            $this->info("✔ {$expiredFileSize} disk cleared");
        } else {
            $this->info('✔ No expired cache file found!');
        }
        $this->line("✔ {$this->activeFileCount} non-expired cache files remaining");
        $this->line("✔ {$activeFileSize} disk space taken by non-expired cache files");
    }

    private function formatBytes($size, $precision = 2)
    {
        $unit = ['Byte','KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];

        for($i = 0; $size >= 1024 && $i < count($unit)-1; $i++){
            $size /= 1024;
        }

        return round($size, $precision).' '.$unit[$i];
    }
}
