<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 27.08.2016
 * Time: 13:08
 */

class core{
    public $mysql;
    public $errors = [];
    public $controller;
    public $action = "index";
    public $params=[];
    public $user;

    /**
     * Инициализация приложения
     * @param $configUrl
     */
    public function init( $configUrl ){
        @session_start();
        $config = require_once($configUrl);
        $this->db_connect( $config["db"] );
        if( !empty( $config["includes"] ) )$this->checkIncludes( $config["includes"] );

        if( !empty( $_SESSION["login"] ) && !empty( $_SESSION["login"]["login"] ) && !empty( $_SESSION["login"]["password"] ) ) {
            $this->auth($_SESSION["login"]["login"], "", $_SESSION["login"]["password"]);
        }
            else $this->user = new users();

        $this->checkRouting( $config );
        $this->loadController();

        if( sizeof( $this->errors ) >0 )$this->getError();
    }

    /**
     * Подключеем все указанные в конфиге файлы
     * @param $listInclude
     */
    private function checkIncludes( $listInclude ){
        if( sizeof( $listInclude ) > 0 ){
            for( $i=0;$i<sizeof( $listInclude );$i++ )
                require_once $listInclude[$i];
        }
    }

    /**
     * Финализируем приложение
     */
    public function close(){
        if( $this->mysql )$this->mysql->close();
    }

    /**
     * Открываем соединие с базой банных
     * @param $config
     */
    private function db_connect($config){
        $this->mysql = new mysqli( $config["host"], $config["user"], $config["password"], $config["db"] );

        if ($this->mysql->connect_error)
            $this->errors[] = $this->mysql->connect_error;
    }

    /**
     * Разбираем REQUEST_URI
     *  - вытаскиваем котроллер, или подставляем дефолтный
     *  - вытаскиваем переданные параметры
     * @param $config
     */
    private function checkRouting( $config ){
        $dopParams = [];
        // REQUEST_METHOD

        if( strpos( $_SERVER["REQUEST_URI"], "?" ) !== false ){
            $params = explode( "?", $_SERVER["REQUEST_URI"] );
            $dopParams = explode( "&", $params[1] );
            $queryArr = explode( "/", $params[0] );
        }
            else {
                $queryArr = explode( "/", $_SERVER["REQUEST_URI"] );
            }

        if( sizeof( $queryArr ) >0 ){
            if( file_exists( "controllers/".trim( $queryArr[1] ).".php" ) )$this->controller = trim( $queryArr[1] );
        }

        /**
         * Если не определен контроллер, берем из конфига поумолчанию
         */
        if( empty( $this->controller ) && !empty( $config["default_controller"] ) ) {
            $this->controller =  $config["default_controller"];
        }

        if( sizeof( $queryArr ) >2 ){
            $queryArr[2] = trim( $queryArr[2] );
            if( !empty( $queryArr[2] ) )$this->action = strtolower( $queryArr[2] ) ;
            for( $i=3;$i<sizeof( $queryArr );$i+=2 ){
                $key = trim( strtolower( $queryArr[$i] ) );
                $value = ( !empty( $queryArr[$i+1] ) ) ? trim( strtolower( $queryArr[$i+1] ) ) : true;
                $this->params[ $key ] = $value;
            }
        }

        /*
         * Дописываем в PARAM переменные переданные через ?
         */
        if( sizeof($dopParams) >0  ){
            for( $i=0;$i<sizeof($dopParams);$i++ ){
                $keyParam = explode( "=", $dopParams[$i] );
                $key = !empty($keyParam[0]) ? $keyParam[0] : "";
                $value = !empty($keyParam[1]) ? $keyParam[1] : true;
                $this->params[ $key ]=$value;
            }
        }
    }

    /**
     * Подгружаем CONTROLLER и запускаем ACTION
     */
    private function loadController(){
        if( !empty( $this->controller ) ){
            $includeController = $this->controller;
            require_once "controllers/".$includeController.".php";
            $objController = new $includeController( $this );

            $includeControllerAction = "action".ucfirst( $this->action );
            if( method_exists( $objController, $includeControllerAction ) )
                $objController->$includeControllerAction();
            else {
                $this->errors[] = "В URL указан неверный ACTION ";
            }
        }
    }

    /**
     * Выводим ошибку
     */
    private function getError(){
        require_once "controllers/errors.php";
        $errors = new errors();
        if( $errors )
            $errors->actionError( $this->errors );
    }

    /**
     * Проверяет данные пользователя, если верны то высталяет пользователя в App
     * @param $login
     * @param $password
     * @return bool
     */
    public function auth( $login, $password="", $md5Password="" ){
        $login = $this->mysql->real_escape_string($login);
        $password = $this->mysql->real_escape_string($password);
        $md5Password = $this->mysql->real_escape_string($md5Password);
        $user = users::fetchAll( ["where"=>"login='".$login."'"] );
        if( sizeof( $user ) >0 ){

            if( !empty( $password ) ){
                if( md5( $user[0]->salt.$password ) == $user[0]->password ){

                    $this->user = $user[0];
                    $_SESSION["login"] = [ "id"=>$user[0]->id, "login"=>$user[0]->login, "password"=>$user[0]->password ];
                    return true;
                }
            }

            if( !empty( $md5Password ) ){
                if( $md5Password == $user[0]->password ){

                    $this->user = $user[0];
                    $_SESSION["login"] = [ "id"=>$user[0]->id, "login"=>$user[0]->login, "password"=>$user[0]->password ];
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Закрывает ссесию пользователя
     */
    public function logOut(){
        $this->user = new users();
        unset( $_SESSION["login"] );
    }
}

$App = new core();