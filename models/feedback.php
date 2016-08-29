<?php
/**
 * Описание можели Feedback
 */

class feedback extends Model
{
    public $name;
    public $email;
    public $message;
    public $image;
    public $status;
    public $admin_edit;

    public $name_table = "feedback";

    public function rules()
    {
        return [
            "require" => [ "name", "email", "message" ],
            "save"    => [ "name", "email", "message", "image", "status", "admin_edit"]
        ];
    }

    public function types()
    {
        return [
            "email"    => "email",
            "image"    => "image",
            "message"  => "text",
            "status"   => "checkbox",
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
            "status"     => "Опубликовать",
            "admin_edit" => "Изминен администратором"
        ];
    }
}