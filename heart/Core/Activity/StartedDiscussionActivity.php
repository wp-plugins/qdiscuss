<?php namespace Qdiscuss\Core\Activity;

class StartedDiscussionActivity extends PostedActivity
{
    public static function getType()
    {
        return 'startedDiscussion';
    }
}