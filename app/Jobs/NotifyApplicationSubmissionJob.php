<?php

namespace App\Jobs;

use App\Models\Candidacy;
use App\Models\ProjectRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\JobApplicationNotification;
use App\Notifications\ApplicationReceivedNotification;

class NotifyApplicationSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Candidacy $candidacy
     * @param ProjectRole $projectRole
     */
    public function __construct(
        private Candidacy $candidacy,
        private ProjectRole $projectRole
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->candidacy->user->notify(
            new JobApplicationNotification(
                $this->candidacy,
                $this->projectRole
            )
        );

        $projectCreator = $this->projectRole
            ->project
            ->loadMissing('createdBy')
            ->createdBy;

        $projectCreator->notify(
            new ApplicationReceivedNotification(
                $this->candidacy,
                $this->projectRole
            )
        );
    }
}
