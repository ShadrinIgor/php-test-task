<?php
/**
 * Котроллер будет отображать ошибку
 */

class errors extends Controller{
    public function actionError( $error = "" ){
        echo "<b>Ошибка:</b><br/>";
        if( is_array( $error ) ){
            for($i=0;$i<sizeof($error);$i++){
                echo $error[$i]."<br/>";
            }
        }
        else {
            echo $error;
        }
    }
}