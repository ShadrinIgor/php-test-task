<?php
/**
 * Контроллер по умолчанию, выводит список отзывов с формой отправки отзыва
 */

class main extends Controller{
    public function actionIndex(){
        Services::loadModel("feedback");
        $addModel = Services::loadModel("feedback_user");

        $sortField="id";
        $sortType = "asc";
        $sort = "id DESC";
        if( !empty( $this->app->params["sort"] ) ){
            if( in_array( $this->app->params["sort"], ["id","name","email"] ) ){

                $sort = $this->app->params["sort"];
                $sortField = $this->app->params["sort"];
                if( in_array( $this->app->params["type"], ["asc","desc"] ) ){
                    $sort .= " ".$this->app->params["type"];
                    $sortType = $this->app->params["type"];
                }
            }
        }

        $listMessages = feedback::fetchAll(["where"=>"status = 1", "order"=>$sort]);
        $message = "";

        if( $_POST[ $addModel->name_table ] ){

            $addModel->setFromArray( $_POST[ $addModel->name_table ] );
            if( $addModel->save() ){
                $message = "Ваше сообщение успешно переданно на модерацию";
            }
        }

        $this->render( "index", [ "name"=>"Урра", "addModel"=>$addModel, "message"=>$message, "listMessages"=>$listMessages, "sortField"=>$sortField, "sortType"=>$sortType ] );
    }
}