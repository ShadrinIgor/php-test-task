<?php
/**
 * Финкции по работе с картинками
 */

class image_services
{
    /**
     * Начинаем процесс сохранения картинки
     * @param $attribute
     */
    static public function uploadFile( $attribute ){
        if( !empty( $_FILES[ $attribute ] ) ){
            if( is_uploaded_file( $_FILES[$attribute]["tmp_name"] ) ){
                $newName = rand( 1000000, 9000000 );

                switch( $_FILES[$attribute]["type"] ){
                    case "image/png" : $newName .= ".png";break;
                    case "image/gif" : $newName .= ".gif";break;
                    case "image/jpeg" : $newName .= ".jpg";break;
                    case "image/jpg" : $newName .= ".jpg";break;
                    default : $newName .= ".jpg";break;
                }

                if( move_uploaded_file( $_FILES[$attribute]["tmp_name"], "uploads/".$newName ) ){
                    image_services::optimization( "uploads/".$newName, $_FILES[$attribute]["type"] );
                    return "uploads/".$newName;
                }

            }
        }
    }

    static public function optimization( $file, $type ){
        list( $paramsX, $paramsY ) = explode( "*", Services::getConfig("image") );

        if( (int)$paramsX>0 && (int)$paramsY>0  ) {

            switch( $type ){
                case "image/png" : $img = imageCreateFromPng( $file );break;
                case "image/gif" : $img = imageCreateFromGif( $file );break;
                case "image/jpeg" :
                case "image/jpg" : $img = imageCreateFromJpeg( $file );break;
                default : $img = imageCreateFromJpeg( $file );
            }

            $dstImage = imagecreatetruecolor($paramsX, $paramsY );

            $fileX = imagesx( $img );
            $fileY = imagesy( $img );

            if( $fileX > $paramsX && $fileY > $paramsY  ){

                // Сжимаем по большей стороне
                if( $fileX-$paramsX < $fileY-$paramsY  ){
                    $procent = round( 100*$paramsX/$fileX );
                    $newX = $paramsX;
                    $newY = round( $procent*$fileY/100 );

                }
                    else {
                        $procent = floor( 100*$paramsY/$fileY );
                        $newX = floor( $procent*$fileX/100 );
                        $newY = $paramsY;
                    }

                imagecopyresized($dstImage, $img, 0, 0, 0, 0, $newX, $newY, $fileX, $fileY );
            }

            if( !$dstImage )$dstImage = $img;

            switch( $type ){
                case "image/png" : imagepng( $dstImage, $file );break;
                case "image/gif" : imagegif( $dstImage, $file );break;
                case "image/jpeg" :
                case "image/jpg" : imagejpeg( $dstImage, $file );break;
                default : imagejpeg( $dstImage, $file );
            }
        }
    }
}