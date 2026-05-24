<?php

namespace App\Jobs;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $comment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Ici, vous pouvez ajouter la logique de modération
        // Par exemple, vérifier si le contenu contient des mots clés interdits
        // ou utiliser un service comme AWS Rekognition

        $bannedWords = config('moderation.banned_words', []);
        $content = strtolower($this->comment->content);

        $containsBannedWord = false;
        foreach ($bannedWords as $word) {
            if (strpos($content, strtolower($word)) !== false) {
                $containsBannedWord = true;
                break;
            }
        }

        if (!$containsBannedWord) {
            $this->comment->update(['is_approved' => true]);
        } else {
            // Créer une violation
            $this->comment->user->violations()->create([
                'type' => 'warning',
                'reason' => 'Inappropriate comment content',
                'status' => 'active',
            ]);
        }
    }
}
