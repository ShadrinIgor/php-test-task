<?php
/**
 * Общие функции
 */

class Model_services
{
    /**
     * Выводит форму добавления для указанной модели
     * @param Model $model
     * @return string
     */
    static public function getAddForm( Model $model ){
        return Model_services::getListField( $model );
    }

    /**
     * Выводит список полей для данной модели
     * @param Model $model
     * @return string
     */
    static public function getListField( Model $model ){
        $cout = "";
        $attributes = $model->attribute();
        $saveAttribute = $model->rules()["save"];

        foreach( $model->attribute() as $value=>$name ){
            if( in_array( $value,  $saveAttribute ) ){
                $value = trim( strtolower( $value ) );
                $cout .= "<div class=\"form-group\"><label for=\"input".$value."\">".( $name ).( in_array( $value, $model->rules()["require"] ) ? "*" : "" )."</label>".Model_services::getField( $model, $value )."</div>";
            }
        }
        return $cout;
    }

    /**
     * Выводит поля для атрибутта, изходя из указанного типа
     * @param Model $model
     * @param $field
     * @return string
     */
    static public function getField( Model $model, $field ){
        if( $model->types()[$field] )$type = $model->types()[$field];
        $tableName = $model->name_table;
        $require = in_array( $field, $model->rules()["require"] ) ? "required" : "";

        switch( $type ){
            case "email"    : $input = '<input type="email" name="'.$tableName.'['.$field.']" id="field_'.$field.'" class="form-control" value="'.$model->$field.'" '.$require.' />';break;
            case "image"    : $input = '<input type="file" name="'.$field.'" id="field_'.$field.'" class="form-control" value="'.$model->$field.'" '.$require.' />';break;
            case "checkbox" : $input = '<input type="checkbox" name="'.$tableName.'['.$field.']" class="form-control" id="field_'.$field.'" '.$require.' value="1" '.( $model->$field==1 ? "checked" : "" ).' />';break;
            case "text"     : $input = '<textarea name="'.$tableName.'['.$field.']" class="form-control" id="field_'.$field.'" value="1" '.$require.'>'.$model->$field.'</textarea>';break;
            default : $input = '<input type="text" name="'.$tableName.'['.$field.']" class="form-control" id="field_'.$field.'" '.$require.' value="'.$model->$field.'" />';
        }

        return $input;
    }


}