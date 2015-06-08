<?php namespace Qdiscuss\Api\Serializers;

class AttachmentSerializer extends BaseSerializer
{
    /**
     * The resource type.
     *
     * @var string
     */
    protected $type = 'attachments';

    /**
     * Serialize attributes of an Attachment model for JSON output.
     *
     * @param Attachment $attachment The Attachment model to serialize.
     * @return array
     */
    protected function attributes($attachment)
    {
        $attributes = [
            'path' => $attachment->path
        ];

        return $this->extendAttributes($attachment, $attributes);
    }
    
}
