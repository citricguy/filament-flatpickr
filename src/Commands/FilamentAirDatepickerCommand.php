<?php

namespace Citricguy\FilamentAirDatepicker\Commands;

use Illuminate\Console\Command;

class FilamentAirDatepickerCommand extends Command
{
    public $signature = 'filament-air-datepicker';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
