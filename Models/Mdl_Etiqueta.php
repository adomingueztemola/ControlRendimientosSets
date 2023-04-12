<?php
class Etiqueta extends ConexionBD
{
    protected $debug;
    private $idUserReg;


    public function __construct($debug, $idUserReg)
    {
        $this->initConexion();
        $this->debug = $debug;
        $this->idUserReg = $idUserReg;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getLotes($filtradoFecha='1=1', $filtradoPrograma='1=1', $filtradoMateriaPrima='1=1'){
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaFinal,'%d/%m/%Y') AS f_fechaFinal,
        pg.nombre AS n_programa, mp.nombre AS n_materia,
        CONCAT(u.nombre, ' ', u.apellidos) AS str_usuario,
        DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, p.nombre AS n_proveedor,
        pd.numFactura        
        FROM rendimientosetiquetas r
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        INNER JOIN catproveedores p ON r.idCatProveedor=p.id
        LEFT JOIN pedidos pd ON r.idPedido=pd.id
        INNER JOIN segusuarios u ON r.idUserReg=u.id
        WHERE $filtradoFecha AND $filtradoMateriaPrima AND $filtradoPrograma
        AND r.estado='2' AND pg.tipo='2'
        ORDER BY r.fechaFinal DESC, r.loteTemola";
        return  $this->consultarQuery($sql, "consultar lotes");
    }
}
