<?php

namespace App\Console\Commands;

use App\Modules\Transactions\Services\RecurringTransactionProcessorService;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process-recurring';
    protected $description = 'Process all due recurring transactions';

    /**
     * Execute the console command.
     */
    public function __construct(protected RecurringTransactionProcessorService $service)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Processing recurring transactions...');

        $processedCount = $this->service->processDueTransactions();

        if ($processedCount === 0) {
            $this->info('No recurring transactions were due at this time.');
            return;
        }

        $this->info("Successfully processed {$processedCount} recurring transaction(s).");
    }
}
