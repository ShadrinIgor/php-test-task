<?php
/**
 * Описание можели Feedback
 */

class feedback_admin extends feedback
{
    public function rules()
    {
        return [
            "require" => [ "name", "email", "message" ],
            "save"    => [ "name", "email", "message", "status", "admin_edit"]
        ];
    }

    public function attribute()
    {
        return [
            "id"         => "ID",
            "name"       => "Имя",
            "email"      => "Email",
            "message"    => "Сообщение",
            "image"      => "Картинка",
            "status"     => "Опубликовать"
        ];
    }
}