<?php
/**
 * Родительский файл для всех контроллеров
 */

class Controller{
    public $layout = "default";
    public $app;

    /**
     * Controller constructor.
     * @param $app
     */
    public function __construct( $app ){
        $this->app = $app;
    }

    /**
     * Рендерит общую вьюшку и включет внутрь внутреннюю часть
     * @param $view
     * @param array $params
     */
    public function render( $view, $params = [] ){

        $content = $this->renderPart( $view, $params );

        if( $this->layout && file_exists( "views/layouts/".$this->layout .".php" ) ){

            $base_url = Services::getConfig("base_url");
            $app = $this->app;

            ob_start();
            require_once( "views/layouts/".$this->layout .".php" );
            $layoutContent = ob_get_contents();
            ob_end_clean();
        }

        echo ( !empty($layoutContent) ) ? $layoutContent : $content;
    }

    /**
     * Рендерит внутреннюю часть вьюшки
     * @param $view
     * @param array $params
     * @return string
     */
    public function renderPart( $view, $params = [] ){
        $className = get_class( $this );
        $fileViews = "";
        $cout = "";

        if( file_exists( "views/".$className."/".$view.".php" ) )$fileViews =  "views/".$className."/".$view.".php";
        if( empty( $fileViews ) && file_exists( "views/".$view.".php" ) )$fileViews =  "views/".$view.".php";

        if( !empty( $fileViews ) ){

            $base_url = Services::getConfig("base_url");
            $app = $this->app;

            ob_start();
            extract( $params );
            require_once( $fileViews );
            $cout = ob_get_contents();
            ob_end_clean();
        }

        return $cout;
    }
}