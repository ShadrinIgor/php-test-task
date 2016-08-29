<?php
/**
 * Общие функции
 */

class Services
{
    static $config;
    /**
     * Подгружает модель
     * @param $modelName
     * @return null
     */
    static public function loadModel( $modelName ){

        if( file_exists( "models/".$modelName.".php" ) ){
            require_once "models/".$modelName.".php";
            return new $modelName();
        }
            else return null;
    }

    /**
     * Выводит раздел конфига по ключу
     * @param $key
     * @return array or null
     */
    static public function getConfig( $key ){
        if( empty( self::$config ) )self::$config = require "config/main.php";
        return !empty( self::$config[$key] ) ? self::$config[$key] : null;
    }

    /**
     * Выводит поздравительное сообщение
     * @param $message
     * @return string
     */
    static public function getSuccessPanel( $message ){
        if( $message )return '<div class="panel panel-info"><div class="panel-heading">'.$message.'</div></div>';
            else return "";
    }

    /**
     * Если у модели есть ошибки выводим их
     * @return string
     */
    static public function getErrorPanel( $errors ){
        if( ( is_array($errors) && sizeof( $errors ) >0 ) || ( !is_array($errors) && !empty( $errors ) ) ){
            $cout = '<div class="panel panel-danger"><div class="panel-heading">Внимание!!!</div><div class="panel-body"><ul>';
            if( is_array($errors) ) {
                for( $i=0;$i<sizeof( $errors );$i++ ){
                    if( !empty( $errors[$i] ) )$cout .= "<li>".$errors[$i]."</li>";
                }
            }
                else {
                    $cout .= "<li>".$errors."</li>";
                }

            $cout .= '</ul></div></div>';
            return $cout;
        }

        return "";
    }

    /**
     * Выдает URL
     * @param array $param
     * @return string
     */
    static public function getUrl( $param = [] ){
        $baseUrl = Services::getConfig( "base_url" );
        return $baseUrl.implode( "/", $param );
    }
}