<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;
// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function created(Reply $reply)
    {
        // 命令行运行迁移时不做这些操作！
        if ( ! app()->runningInConsole()) {
            $reply->topic->updateReplyCount();
            // 通知话题作者有新的评论
            $reply->topic->user->notify(new TopicReplied($reply));
        }
    }

    public function creating(Reply $reply)
    {
        $reply->content = clean($reply->content, 'user_topic_body');
    }

    public function deleted(Reply $reply)
    {
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();
    }
}