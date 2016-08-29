<?php
/**
 * Описание можели Feedback
 */

class feedback_user extends feedback
{
    public function rules()
    {
        return [
            "require" => [ "name", "email", "message" ],
            "save"    => [ "name", "email", "message", "image"]
        ];
    }
}