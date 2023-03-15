<?php
class ConexionBD
{
    protected $link;
    protected $resultXquery;
    protected  function initConexion()
    {
        $this->link = conectar::conexion();
    }
    public function runRollback()
    {
        /* Revertir */
        $this->link->rollback();
    }
    public function runClose()
    {
        /* Cerrar Conexion */
        $this->link->close();
    }
    public function getConexion()
    {
        return $this->link;
    }
    public function deshabilitarAutoCommit()
    {
        /* deshabilitar autocommit */
        $this->link->autocommit(FALSE);
    }
    public function beginTransaction()
    {
        /* Revertir */
        $this->deshabilitarAutoCommit();
        $this->link->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    }
    public function insertarCommit()
    {
        /* insertar commit */
        if (!$this->link->commit()) {
            $this->errorBD("Falla al finalizar almacenamiento, notifica a tu Administrador.", 1);
        }
    }
    protected function close()
    {
        $this->link->close();
    }
    public function errorBD($error, $NecesitaRollBack)
    {
        $debug = $this->debug;
        $link =  $this->link;
        if ($NecesitaRollBack == '1') {
            $this->runRollback();
        }
        if ($debug == 1) {
            echo '<br><br>Det Error: ' . $error;
            echo '<br><br>Error Report: ' . $link->error;
        } else {
            echo '0|' . $error;
        }
        exit(0);
    }
    public function ejecutarQuery($sql, $topic, $_consulta = false, $_insert_id = false)
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        if ($debug == 1) {
            $resultXquery = $link->query($sql);
            echo "SQL: " . $sql;
            if (!$resultXquery) {
                return ($link->error);
            }
            $canInsert = $link->affected_rows;
            echo "Can Insert: " . $canInsert;
        } else {
            $resultXquery = $link->query($sql);
            if (!$resultXquery) {
                return ("Problemas al $topic, notifica a tu Administrador");
            }
            $canInsert = $link->affected_rows;
        }
        if ($_consulta) {
            $count = 0;
            while ($row = $resultXquery->fetch_array(MYSQLI_BOTH)) {
                $ArrayDatos[$count] = $row;
                $count++;
            }
            return $ArrayDatos;
        } else {
            if ($_insert_id) {
                return ['1', $canInsert, $link->insert_id];
            } else {
                return ['1', $canInsert];
            }
        }
    }

    /***************************************************************************** */
    /********** FUNCIONES ACTUALIZADAS PARA EJECUTAR QUERYES V.2.0 ************/
    /***************************************************************************** */
    //Ejecutar operacion de Query
    public function runQuery($sql, $message, $_idInsert = false)
    {
        $debug = $this->debug;
        $link = $this->link;
        if ($debug == 1) {
            $resultXquery = $link->query($sql);
            echo "SQL: " . $sql;
            if (!$resultXquery) {
                return ($link->error);
            }
            $canInsert = $link->affected_rows;
        } else {
            $resultXquery = $link->query($sql);
            if (!$resultXquery) {
                return ('Problemas al ' . $message . ', notifica a tu Administrador');
            }
            $canInsert = $link->affected_rows;
        }
        if ($_idInsert) {
            return ['1', $canInsert, $link->insert_id];
        } else {
            return ['1', $canInsert];
        }
    }
    //Ejecutar Consulta de Query
    public function consultarQuery($sql, $message, $_multiple = true)
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        if ($debug == 1) {
            $resultXquery = $link->query($sql);
            echo "SQL: " . $sql;
            if (!$resultXquery) {
                return ($link->error);
            }
            $canInsert = $link->affected_rows;
        } else {
            $resultXquery = $link->query($sql);
            if (!$resultXquery) {
                return ('Problemas al consultar ' . $message . ', notifica a tu Administrador');
            }
            $canInsert = $link->affected_rows;
        }
        if ($_multiple) {
            $count = 0;
            while ($row = $resultXquery->fetch_array(MYSQLI_ASSOC)) {
                $ArrayDatos[$count] = $row;
                $count++;
            }
        } else {
            $ArrayDatos = $resultXquery->fetch_array(MYSQLI_ASSOC);
        }

        return $ArrayDatos;
    }
}
