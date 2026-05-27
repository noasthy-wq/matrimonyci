<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ModerateProfileMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $media;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Vérifier le fichier avec AWS Rekognition ou un autre service
        // Pour ce template, on approuve simplement le fichier

        if (config('matrimony.moderation.enabled') && config('matrimony.moderation.service') === 'aws-rekognition') {
            // Implémenter la logique AWS Rekognition ici
            // Pour l'instant, on approuve automatiquement
            $this->media->approve();
        } else {
            // Mode manuel
            // Le fichier reste en attente de modération
        }
    }
}
