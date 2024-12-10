<?php

namespace Larapps\GiftCertificateManager\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;


#[AsCommand(name: 'larapps:bc-gift-certificate:install')]
class InstallCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larapps:bc-gift-certificate:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the gift certificate resources';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {

        (new Filesystem)->copyDirectory(__DIR__.'/../resources/js/Pages', resource_path('js/Pages'));
        (new Filesystem)->copyDirectory(__DIR__.'/../resources/js/Components', resource_path('js/Components'));
        (new Filesystem)->copyDirectory(__DIR__.'/../resources/js/Sections', resource_path('js/Sections'));
        return 1;
    }
}

