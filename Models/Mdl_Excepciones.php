<?php
class Excepciones extends Exception
{
    public static function validaMsjError($datos)
    {
        if (!is_array($datos)) {
            throw new Exception($datos);
        } else if ($datos[0] != '1') {
            throw new Exception("Ocurrió un Error Inesperado, Notifica a tu Administrador");
        } else if ($datos == '') {
            throw new Exception("Ocurrió un Error Inesperado, Notifica a tu Administrador");
        }
        return $datos;
    }
    //Mdl excepcion    
    public static function validaLlenadoDatos($ArrayDatos, $obj)
    {
        $ErrorLog = 'No se recibió';
        $log = '';
        foreach ($ArrayDatos as $key => $value) {
            if ($value == '') {
                $ErrorLog .= $key . ', ';
                $log = '1';
            }
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj->errorBD($ErrorLog, 1);
        }
    }


    /************************************************/
    /* VALIDACION DE RESPUESTA DE CONSULTA EN BACKEND */
    /************************************************/
    public  static function validaConsulta($data)
    {
        $data = $data == '' ? array() : $data;
        if (!is_array($data)) {
            echo "<p class='text-danger'>Error, $data</p>";
            // exit(0);
        }
        return $data;
    }

    
}
