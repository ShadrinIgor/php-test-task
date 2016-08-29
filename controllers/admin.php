<?php
/**
 * Администрирование отзывов
 */

class admin extends Controller
{
    public function actionIndex()
    {
        $error = "";
        $login = "";
        $listMessages = [];

        if( !empty( $_POST["login"] ) && !empty( $_POST["password"] )  ){
            $login = trim( $_POST["login"] );
            if( $this->app->auth( $login, trim( $_POST["password"] ) ) ){
                Header("Location: ".Services::getUrl(["admin"]) );
            }
                else $error = "Вы ввели не верный логин или пароль";
        }

        if( !empty( $this->app->params["logout"] ) ){
            $this->app->logOut();
            Header("Location: ".Services::getUrl(["admin"]));
        }

        if( $this->app->user->id > 0 ){

            $listMessages = feedback::fetchAll( ["order"=>"id DESC"] );
        }

        $this->render( "index", [ "error"=>$error, "login"=>$login, "listMessages"=>$listMessages ] );
    }

    public function actionEdit()
    {
        if( $this->app->user->id >0 && !empty( $this->app->params["id"] ) && (int)$this->app->params["id"]  >0 ){
            Services::loadModel("feedback_admin");
            $model = feedback_admin::fetchAll( ["where"=>"id=".(int)$this->app->params["id"]] );
            if( sizeof( $model ) >0 ){
                $this->render( "edit", [ "addModel"=>$model[0], "message"=>"" ] );
            }
        }
        else {
            echo $this->app->user->id."-".$this->app->params["edit"];
            return;
        }
    }

    public function actionUpdate()
    {
        if( !empty( $_POST ) && $this->app->user->id >0 && !empty( $_POST["id"] ) && (int)$_POST["id"] >0 ){
            Services::loadModel("feedback_admin");
            $models = feedback_admin::fetchAll( ["where"=>"id=".(int)$_POST["id"]] );
            if( sizeof($models) >0 ){
                $message = "";
                unset( $_POST["id"] );
                $oldModel = serialize( $models[0]->setFromArray( ["sratus"=>$_POST["status"]] ) );

                $image = $models[0]->image;
                $addModel = $models[0]->setFromArray( $_POST );

                if( $oldModel != serialize( $addModel ) )
                    $addModel->admin_edit = 1;

                if( $addModel->save() ){
                    $addModel->image = $image;
                    $message = "Отзыв успешно отредактирован";
                }

                $this->render( "edit", [ "addModel"=>$addModel, "message"=>$message ] );
            }
        }
    }
}