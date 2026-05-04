<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Parsedown;

class ForumReplyController extends Controller
{
    public function quote(ForumTopic $topic, ForumReply $reply)
    {
        $parsedown = new Parsedown();
        return view('forums.topics.reply', compact('topic', 'reply', 'parsedown'));
    }
}
