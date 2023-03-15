<?php

class Funciones
{
    public static function cambiarEstatus($nameTable, $estatus, $nameCampo,$id, $link, $debug){
        $sql = "UPDATE $nameTable SET $nameCampo='$estatus' WHERE id='$id'";
        if ($debug == 1) {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return($link->error);
            }
            $canInsert=$link->affected_rows;
        } else {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return('Problemas al cambiar Estatus, notifica a tu Administrador');
            }
            $canInsert=$link->affected_rows;
        }
        return [1, 'Estatus Modidicado Correctamente'];

    } 
    public static function eliminarRegistro($nameTable, $valueId, $campoId, $link, $debug){
        $sql = "DELETE FROM $nameTable  WHERE $campoId='$valueId'";
        if ($debug == 1) {
            echo "SQL: ".$sql;
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return($link->error);
            }
            $canInsert=$link->affected_rows;
        } else {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return('Problemas al eliminar Registro, notifica a tu Administrador');
            }
            $canInsert=$link->affected_rows;
        }
        return [1, 'Registro Eliminado Correctamente'];

    }
    public static function validarDatoTabla($nameTable, $nameCampo, $value, $debug, $link, $id=0, $filtradoCampoExtra="1=1"){
        $idDif=$id!=0?"id!='$id'":'1=1';
        $sql = "SELECT * FROM $nameTable WHERE UPPER($nameCampo)=UPPER('$value') AND $idDif AND $filtradoCampoExtra";
        if ($debug == 1) {
            $resultXquery =$link->query($sql);
            echo 'SQL: '.$sql;
            if (!$resultXquery) {
                return($link->error);
            }
            $canInsert=$link->affected_rows;
        } else {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return('Problemas al consultar Datos, notifica a tu Administrador');
            }
            $canInsert=$link->affected_rows;
        }
        return ['1', $canInsert];
    }

    public static function excluirNoLista($nameTable, $nameCampo, $value, $str_lista, $nameCampoCambio, $debug, $link, $ALL=false, $Excluir=true){
       $notIN=$Excluir?'NOT IN': 'IN';
       $sentencia=$ALL?'1=1':" $nameCampoCambio $notIN ($str_lista)";
        $sql = "UPDATE $nameTable SET $nameCampo='$value' WHERE $sentencia";
        if ($debug == 1) {
            $resultXquery =$link->query($sql);
            echo 'SQL: '.$sql;
            if (!$resultXquery) {
                return($link->error);
            }
            $canInsert=$link->affected_rows;
        } else {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return('Problemas al editar Datos, notifica a tu Administrador');
            }
            $canInsert=$link->affected_rows;
        }
        return ['1', $canInsert];

    }
    /***FUNCION : GENERA FOLIOS FORMATO > CONCEPTO+PREFIJO SUC + ID BD    */

    public static function generarSerie($prefijo, $id)
    {
        $numero = str_pad($id, 6, "0");
        $folio = $prefijo . "-" . $numero;
        return $folio;
    }

    public static function obtenerDetallado($nameTable, $nameID, $val_id, $link, $debug){

        $sql = "SELECT  * FROM $nameTable WHERE $nameID='$val_id'";
        if ($debug == 1) {
            $resultXquery =$link->query($sql);
            echo 'SQL: '.$sql;
            if (!$resultXquery) {
                return($link->error);
            }
            $canInsert=$link->affected_rows;
        } else {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return('Problemas al consultar Datos, notifica a tu Administrador');
            }
            $canInsert=$link->affected_rows;
        }

        return $resultXquery->fetch_array(MYSQLI_BOTH);


    }

    public static function edicionBasica($nameTable, $nameCampo, $val_campo, $campoBusq, $val_busq, $link, $debug){

        $sql = "UPDATE $nameTable SET $nameCampo='$val_campo' WHERE $campoBusq='$val_busq'";
        if ($debug == 1) {
            echo "SQL: ".$sql;
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return($link->error);
            }
            $canInsert=$link->affected_rows;
        } else {
            $resultXquery =$link->query($sql);
            if (!$resultXquery) {
                return('Problemas al edición de  Registro, notifica a tu Administrador');
            }
            $canInsert=$link->affected_rows;
        }

        return ['1', $canInsert];


    }

}