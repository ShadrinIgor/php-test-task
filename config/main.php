<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 27.08.2016
 * Time: 12:31
 */

return [
    "base_url" => "http://test/",
    "includes" => [
        "components/controller.php",
        "components/model.php",
        "components/model_services.php",
        "components/services.php",
        "components/image_services.php",
        "models/users.php",
        "models/feedback.php",
    ],
    "db" => [
        "host"     => "localhost",
        "db"       => "test_db",
        "user"     => "root",
        "password" => "",
        "prefix"   => "tbl"
    ],
    "default_controller" => "main",
    "image" => "320*240",
];