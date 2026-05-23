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
        // Modération du contenu (vérification du texte)
        $bannedWords = config('moderation.banned_words', []);
        $content = strtolower($this->comment->content);
        
        $hasBannedWords = false;
        foreach ($bannedWords as $word) {
            if (strpos($content, strtolower($word)) !== false) {
                $hasBannedWords = true;
                break;
            }
        }

        if ($hasBannedWords) {
            $this->comment->delete();
            return;
        }

        // Approuver le commentaire
        $this->comment->update(['is_approved' => true]);
    }
}
