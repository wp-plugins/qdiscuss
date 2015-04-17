<?php namespace Qdiscuss\Core\Models;

use Qdiscuss\Core\Formatter\FormatterManager;
use Qdiscuss\Core\Events\PostWasPosted;
use Qdiscuss\Core\Events\PostWasRevised;
use Qdiscuss\Core\Events\PostWasHidden;
use Qdiscuss\Core\Events\PostWasRestored;

class CommentPost extends Post
{
    /**
     * The text formatter instance.
     *
     * @var \Qdiscuss\Core\Formatter\Formatter
     */
    protected static $formatter;

    /**
     * Create a new instance in reply to a discussion.
     *
     * @param  int  $discussionId
     * @param  string  $content
     * @param  int  $userId
     * @return static
     */
    public static function reply($discussionId, $content, $userId)
    {
        $post = new static;

        $post->content       = $content;
        $post->content_html  = static::formatContent($post->content);
        $post->time          = time();
        $post->discussion_id = $discussionId;
        $post->user_id       = $userId;
        $post->type          = 'comment';

        $post->raise(new PostWasPosted($post));

        return $post;
    }

    /**
     * Revise the post's content.
     *
     * @param  string  $content
     * @param  \Qdiscuss\Core\Models\User  $user
     * @return $this
     */
    public function revise($content, $user)
    {
        if ($this->content !== $content) {
            $this->content = $content;
            $this->content_html = static::formatContent($this->content);

            $this->edit_time = time();
            $this->edit_user_id = $user->id;

            $this->raise(new PostWasRevised($this));
        }

        return $this;
    }

    /**
     * Hide the post.
     *
     * @param  \Qdiscuss\Core\Models\User  $user
     * @return $this
     */
    public function hide($user)
    {
        if (! $this->hide_time) {
            $this->hide_time = time();
            $this->hide_user_id = $user->id;

            $this->raise(new PostWasHidden($this));
        }

        return $this;
    }

    /**
     * Restore the post.
     *
     * @param  \Qdiscuss\Core\Models\User  $user
     * @return $this
     */
    public function restore($user)
    {
        if ($this->hide_time !== null) {
            $this->hide_time = null;
            $this->hide_user_id = null;

            $this->raise(new PostWasRestored($this));
        }

        return $this;
    }

    /**
     * Get the content formatter as HTML.
     *
     * @param  string  $value
     * @return string
     */
    public function getContentHtmlAttribute($value)
    {
        if (! $value) {
            $this->content_html = $value = static::formatContent($this->content);
            $this->save();
        }

        return $value;
    }

    /**
     * Get text formatter instance.
     *
     * @return \Qdiscuss\Core\Formatter\FormatterManager
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set text formatter instance.
     *
     * @param  \Qdiscuss\Core\Formatter\FormatterManager  $formatter
     */
    public static function setFormatter(FormatterManager $formatter)
    {
        static::$formatter = $formatter;
    }

    /**
     * Format a string of post content using the set formatter.
     *
     * @param  string  $content
     * @return string
     */
    protected static function formatContent($content)
    {
        return static::$formatter->format($content);
    }
}
