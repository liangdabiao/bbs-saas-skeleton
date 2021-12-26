<?php

namespace App\Observers;

use App\Models\Link;
use App\Notifications\TopicReplied;
use Cache;
// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class LinkObserver
{
    // 在保存时清空 cache_key 对应的缓存
    public function saved(Link $link)
    {
        Cache::forget($link->cache_key);
    }
}