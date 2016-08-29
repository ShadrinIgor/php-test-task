<?php
/**
 * Описание модели users
 */

class users extends Model
{
    public $login;
    public $password;
    public $salt;

    public $name_table = "users";

    public function rules()
    {
        return [
            "require" => [ "login", "password", "salt" ],
            "save"    => [ "login", "password", "salt" ]
        ];
    }

    public function attribute()
    {
        return [
            "id"       => "ID",
            "login"    => "Логин",
            "password" => "Пароль",
            "salt"     => "Соль"
        ];
    }
}