<?php
/**
 * Описывает общую структуру и методы для моделей
 */

class Model
{
    /**
     * ID записи
     * @var
     */
    public $id = 0;

    /**
     * Сожержит ошибки данное модели
     * @var
     */
    public $errors = [];

    /**
     * Название таблицы в базе данных
     * @var
     */
    public $name_table;

    /**
     * Описывает правила сохранения
     * @return array
     */
    public function rules(){
        return [ "require"=>[], "save"=>[] ];
    }

    /**
     * Описывает типы полей
     * @return array
     */
    public function types(){
        return [];
    }

    /**
     * Описывает назавание атрибутов
     * @return array
     */
    public function attribute(){
        return [];
    }

    /**
     * Производим предварительные действия, перед сохранением. Например сохранение файла
     */
    private function beforSave(){
        /**
         * Если есть атрибуты типа image, загружаем их
         */
        foreach( $this->types() as $key=>$type ){
            if( $type == "image" ){
                $this->$key = image_services::uploadFile( $key );
            }
        }
    }

    /**
     * Сохраняем данные модели в базу
     * @return bool
     */
    public function save( $resetModel = false ){
        if( $this->validate() ){

            $this->beforSave();

            $attributes = $this->attribute();
            $saveFields = $this->rules()["save"];

            $sql = "";
            $sqlDop = "";
            for( $i=0;$i<sizeof($saveFields);$i++ ){
                $field = trim( strtolower( $saveFields[$i] ) );
                if( $this->id == 0 ){
                    if( !empty($sql) ){
                        $sql .= ", ";
                        $sqlDop .= ", ";
                    }
                    $sql .= "`".$field."`";
                    $sqlDop .= "'".$GLOBALS["App"]->mysql->real_escape_string( $this->$field )."'";
                }
                    else {
                        if( !empty($sql) )$sql .= ", ";
                        $sql .= "`".$field."`='".$GLOBALS["App"]->mysql->real_escape_string( $this->$field )."'";
                    }
            }

            $dbConfig = Services::getConfig("db");
            $dbPrefex = !empty( $dbConfig["prefix"] ) ? $dbConfig["prefix"]."_" : "";

            if( $this->id == 0 )$allSql = "INSERT INTO ".$dbPrefex.$this->name_table."( ".$sql." ) VALUES( ".$sqlDop." )";
                else $allSql = "UPDATE ".$dbPrefex.$this->name_table." SET ".$sql." WHERE id=".$this->id;

            if( $GLOBALS["App"]->mysql->query( $allSql ) == true ){
                if( !$this->id || $this->id == 0 )$this->id = $GLOBALS["App"]->mysql->insert_id;
                return true;
            }
            else {
                $this->errors[] = $GLOBALS["App"]->mysql->error." ( ".$allSql." )";
                return false;
            }

        }
            else return false;
    }

    /**
     * Проверем палидность аттрибутов
     * @return bool
     */
    public function validate(){
        $saveFields = $this->rules()["save"];
        $requireFields = $this->rules()["require"];

        for( $i=0;$i<sizeof($saveFields);$i++ ){
            $field = trim( strtolower( $saveFields[$i] ) );

            /**
             * Проверяем обязательные поля
             */
            if( in_array( $field, $requireFields ) && !$this->$field ){
                $this->errors[] = "Вы не заполнили обязательное поле - <b>".$this->attribute()[$field]."</b>";
            }

            /**
             * Проверяем поля типа Email
             */
            if( $this->types()[ $field ] && $this->types()[ $field ] == "email" && !filter_var($this->$field, FILTER_VALIDATE_EMAIL) ){
                $this->errors[] = "Вы указали не валидный Email, в поле - <b>".$this->attribute()[$field]."</b>";
            }
        }

        if( sizeof( $this->errors ) >0 )return false;
            else return true;
    }

    /**
     * Заполняем аттрибуты моделей значениями из массива
     * @param array $params
     */
    public function setFromArray( array $params ){
        foreach( $params as $key=>$value ){
            $key = strtolower( trim( $key ) );
            if( property_exists( $this, $key ) )$this->$key = $value;
        }

        return $this;
    }

    /**
     * Находит записе в базе по указанным критериям
     * @param array $param
     * @return array
     */
    static function fetchAll( $param = [] ){
        $cout = [];
        $class = get_called_class();
        $obj = new $class();
        $tableName = $obj->name_table;
        $where = empty( $param["where"] ) ? "" : " WHERE ".$param["where"];
        $limit = 10;
        $order = empty( $param["order"] ) ? " id ASC" : $param["order"] ;
        $dbConfig = Services::getConfig("db");
        $tablePrefix = !empty( $dbConfig["prefix"] ) ? $dbConfig["prefix"]."_" : "";
        $sql = "SELECT * FROM ".$tablePrefix.$tableName.$where." ORDER BY ".$order." LIMIT ".$limit;

        $res = $GLOBALS["App"]->mysql->query( str_replace( "'", '"', $sql ) );

        //echo $sql;//." - ".$res;
        if( $res ){
            while( $line = $res->fetch_array() ){
                $new = new $class();
                $cout[] = $new->setFromArray( $line );
            }

            return $cout;
        }
            else return [];
    }
}