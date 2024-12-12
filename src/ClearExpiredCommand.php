<?php

namespace Arifhp86\ClearExpiredCacheFile;

use Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionFailed;
use Exception;
use Illuminate\Console\Command;
use Wilderborn\Partyline\Facade as Partyline;

class ClearExpiredCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    
    /**
     * @var CacheGarbageCollector
     */
    protected $garbageCollector;

    /**
     * @var string
     */
    protected $signature = 'cache:clear-expired {--dry-run} {--disable-directory-delete}';

    /**
     * @var string
     */
    protected $description = 'Clear expired cache files and empty directories.';

    /**
     * Create a new command instance.
     *
     * @param CacheGarbageCollector $garbageCollector
     * @return void
     */
    public function __construct(CacheGarbageCollector $garbageCollector)
    {
        parent::__construct();

        $this->garbageCollector = $garbageCollector;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Partyline::bind($this);

        try {
            $this->garbageCollector
                ->setIsDryRun($this->option('dry-run'))
                ->setDisableDirectoryDelete($this->option('disable-directory-delete'))
                ->collectGarbage();
        } catch (Exception $e) {
            GarbageCollectionFailed::dispatch($e);

            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
