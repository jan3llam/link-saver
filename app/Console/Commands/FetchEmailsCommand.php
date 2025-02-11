<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class FetchEmailsCommand extends Command
{
    protected $signature = 'emails:fetch';
    protected $description = 'Fetch emails and process links';

    public function __construct(private EmailService $emailService) {
        parent::__construct();
    }

    public function handle() {
        $this->emailService->processInbox();
        $this->info('Emails processed successfully.');
    }
}
