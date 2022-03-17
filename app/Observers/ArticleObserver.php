<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Notification;
use App\Models\Report;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function created(Article $article)
    {
        Report::create([
            'subject' => Notification::ALL,
            'action' => Report::CREATED,
            'row_status' => $article->status_label,
            'user_id' => $article->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Article "updated" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function updated(Article $article)
    {
        Report::create([
            'subject' => Notification::ALL,
            'action' => Report::UPDATED,
            'row_status' => $article->status_label,
            'user_id' => $article->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Article "deleted" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function deleted(Article $article)
    {
        Report::create([
            'subject' => Notification::ALL,
            'action' => Report::DELETED,
            'row_status' => $article->status_label,
            'user_id' => $article->user_id,
            'actor_id' => auth()->id(),
            'status' => Report::NEW,
        ]);
    }

    /**
     * Handle the Article "restored" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function restored(Article $article)
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function forceDeleted(Article $article)
    {
        $this->deleted($article);
    }
}
