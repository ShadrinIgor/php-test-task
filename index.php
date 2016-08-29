<?php

require_once "components/core.php";
$config =  "config/main.php";

/**
 * Инициализация прилоджения
 *  - создаение соединения
 *  - подключение роутинга
 *  - подключение контроллера
 */


echo $App->init( $config );

$App->close( );