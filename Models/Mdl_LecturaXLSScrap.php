<?php
error_reporting(0);
class LecturaXLSScrap extends Scrap
{
  protected $debug;
  private $idUserReg;


  public function __construct($debug, $idUserReg)
  {
    $this->debug = $debug;
    $this->idUserReg = $idUserReg;
    $this->initConexion();
  }

  public function leerXLS($file)
  {
    $debug = $this->debug;
    $idUserReg = $this->idUserReg;
    $ArrayDatos = array();
    $ArrayDatos['error'] = 1;
    $ArrayDatos['msj'] = 'No se Ejecuto...';
    $ArrayDatos['ruta'] = $file;

    if (!file_exists($file)) {
      $ArrayDatos['msj'] = 'No se encontro el archivo...';
    } else {
      /************************************
       * LECTURA DE EXCEL DE SCRAP
       ***********************************/
      $inputFileType = PHPExcel_IOFactory::identify($file);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($file);
      $sheet = $objPHPExcel->getSheet(0);
      $filasEnXLS = $sheet->getHighestDataRow();
      $columnasEnXLS = $sheet->getHighestDataColumn();
      /************************************
       * VARIABLES DE CONTEO DE LOGS
       ***********************************/
      $ArrayDatos['gral'] = array();
      $num = 0;
      $total = 0;
      $filasCorrectas = 0;
      $filasSinLote = 0;
      $filasSinSumaCorrecta = 0;
      $filasSinPzas = 0;
      $filasIncompletas = 0;
      $filasSinBD = 0;
      $ArrayDatos['val'] = array();
      /**********************************
       * CONSULTA DATOS DE RENDIMIENTOS DISPONIBLES
       **********************************/
      $DataLotes = $this->getRendimientosDisponibles();
      $DataLotes = Excepciones::validaConsulta($DataLotes);
      /**********************************
       * CONSULTA DATOS DE STOCK DE RECHAZO
       **********************************/
      $DataStkRech = $this->getStockRechDisponibles();
      $DataStkRech = Excepciones::validaConsulta($DataStkRech);
      /**********************************/
      // echo "Filas: ".$filasEnXLS."<br>";
      for ($row = 4; $row <= $filasEnXLS; $row++) {
        $colorFila = '';
        $estatus = 1;
        // echo "Row: ".$row;
        // echo "A$row: " . $sheet->getCell("A" . $row)->getValue() . "<br>";
        // echo "B$row: " . $sheet->getCell("B" . $row)->getValue() . "<br>";
        // echo "C$row: " . $sheet->getCell("C" . $row)->getValue() . "<br>";
        // echo "D$row: " . $sheet->getCell("D" . $row)->getValue() . "<br>";
        // echo "E$row: " . $sheet->getCell("E" . $row)->getValue() . "<br>";
        // echo "F$row: " . $sheet->getCell("F" . $row)->getValue() . "<br>";
        // echo "G$row: " . $sheet->getCell("G" . $row)->getValue() . "<br>";

        if (
         ( $sheet->getCell("A" . $row)->getValue() == '' AND  $sheet->getCell("A" . $row)->getValue() != '0') ||
          ($sheet->getCell("B" . $row)->getValue() == '' AND  $sheet->getCell("B" . $row)->getValue() != '0') ||
          ($sheet->getCell("C" . $row)->getValue() == '' AND  $sheet->getCell("C" . $row)->getValue() != '0') ||
          ($sheet->getCell("D" . $row)->getValue() == '' AND  $sheet->getCell("D" . $row)->getValue() != '0') ||
          ($sheet->getCell("E" . $row)->getValue() == '' AND  $sheet->getCell("E" . $row)->getValue() != '0') ||
          ($sheet->getCell("F" . $row)->getValue() == '' AND  $sheet->getCell("F" . $row)->getValue() != '0') ||
          ($sheet->getCell("G" . $row)->getValue() == '' AND  $sheet->getCell("G" . $row)->getValue() != '0') 
        ) {
          // echo "Row: ".$row;
          // echo "A$row: " . $sheet->getCell("A" . $row)->getValue() . "<br>";
          // echo "B$row: " . $sheet->getCell("B" . $row)->getValue() . "<br>";
          // echo "C$row: " . $sheet->getCell("C" . $row)->getValue() . "<br>";
          // echo "D$row: " . $sheet->getCell("D" . $row)->getValue() . "<br>";
          // echo "E$row: " . $sheet->getCell("E" . $row)->getValue() . "<br>";
          // echo "F$row: " . $sheet->getCell("F" . $row)->getValue() . "<br>";
          // echo "G$row: " . $sheet->getCell("G" . $row)->getValue() . "<br>";
           break;
        }
      
        //VARIABLES DE EXCEL
        $semana = Texto::limpCaracteresSinEsp($sheet->getCell("A" . $row)->getValue());
        $lote = ($sheet->getCell("B" . $row)->getValue());
        $_12 = intval(Texto::limpCaracteresSinEsp($sheet->getCell("C" . $row)->getValue()));
        $_3 = intval(Texto::limpCaracteresSinEsp($sheet->getCell("D" . $row)->getValue()));
        $_6 = intval(Texto::limpCaracteresSinEsp($sheet->getCell("E" . $row)->getValue()));
        $_9 = intval(Texto::limpCaracteresSinEsp($sheet->getCell("F" . $row)->getValue()));
        $total = intval(Texto::limpCaracteresSinEsp($sheet->getCell("G" . $row)->getOldCalculatedValue()));
        $sumatoriaPreview = intval($_12) + intval($_3) + intval($_6) + intval($_9);
        //FILAS DE EXCEL
        // echo "SEMANA: " . $sheet->getCell("A" . $row)->getValue() . "<br>";
        // echo "LOTE: " . $sheet->getCell("B" . $row)->getValue() . "<br>";
        // echo "12:00 ->" . $sheet->getCell("C" . $row)->getValue() . "<br>";
        // echo "03:00 ->" . $sheet->getCell("D" . $row)->getValue() . "<br>";
        // echo "06:00 ->" . $sheet->getCell("E" . $row)->getValue() . "<br>";
        // echo "09:00 ->" . $sheet->getCell("F" . $row)->getValue() . "<br>";
        // echo "TOTAL: " . $sheet->getCell("G" . $row)->getValue() . "<br>";
        //Verificacion de 4 digitos en lotes
        if (strlen($lote) <= '1') {
          $filasSinLote++;
          $colorFila = 'table-info';
          $estatus = 0;
          // echo "LOTE SIN COMPLETO";
        }
        //Verificacion Numeros en Piezas
        if (!is_int($_12) || !is_int($_3) || !is_int($_6) || !is_int($_9)) {
          $filasSinPzas++;
          $colorFila = 'table-warning';
          $estatus = 0;
          // echo "PIEZAS TOTALES";
          // echo intval(is_int($_12));
          // echo intval(is_int($_3));
          // echo intval(is_int($_6));
          // echo intval(is_int($_9));
        }
        //Verificacion Sumatoria Correcta
        if ($sumatoriaPreview != $total) {
          $filasSinSumaCorrecta++;
          $colorFila = 'table-primary';
          $estatus = 0;
          // echo "SUMATORIA CORRECTA";

        }
        //Verificacion De Existencia de Lote: Estatus debe de estar 4 
        $DataRend = $this->busquedaLote($lote, $DataLotes);
        // print_r($DataRend);
        if (count($DataRend) <= 0) {
          $filasSinBD++;
          $estatus = 0;
          $colorFila = 'table-danger';
        }

        //Deteccion de fila Incompleta
        if ($estatus == 0) {
          $filasIncompletas++;
        }
        //UBICACION DE STOCK DE RECHAZO
        $DataStk= $this->busquedaStock( $DataRend['id'], $DataStkRech);

        $_12Scrap= count($DataStk) <= 0?'0': intval($DataStk['_12']);
        $_3Scrap= count($DataStk) <= 0?'0':intval($DataStk['_3']);
        $_6Scrap= count($DataStk) <= 0?'0': intval($DataStk['_6']);
        $_9Scrap= count($DataStk) <= 0?'0': intval($DataStk['_9']);
        $totalScrap= count($DataStk) <= 0?'0': intval($DataStk['pzasTotales']);
        $idStk= count($DataStk) <= 0?'0': $DataStk['id'];
        $ArrayDatos['val'][$num]['estatus'] = $estatus;
        $ArrayDatos['val'][$num]['bgColor'] = $colorFila;
        $ArrayDatos['val'][$num]['semana'] = $semana;
        $ArrayDatos['val'][$num]['lote'] = $lote;
        $ArrayDatos['val'][$num]['idRendimiento'] = $DataRend['id'];
        $ArrayDatos['val'][$num]['_12'] = $_12;
        $ArrayDatos['val'][$num]['_3'] = $_3;
        $ArrayDatos['val'][$num]['_6'] = $_6;
        $ArrayDatos['val'][$num]['_9'] = $_9;
        $ArrayDatos['val'][$num]['total'] = $total;
        $ArrayDatos['val'][$num]['_12Scrap'] = $_12Scrap;
        $ArrayDatos['val'][$num]['_3Scrap'] = $_3Scrap;
        $ArrayDatos['val'][$num]['_6Scrap'] = $_6Scrap;
        $ArrayDatos['val'][$num]['_9Scrap'] = $_9Scrap;
        $ArrayDatos['val'][$num]['totalScrap'] = $totalScrap;
        $ArrayDatos['val'][$num]['idStk'] = $idStk;

        $num++;
        // echo "Result";
        // print_r($ArrayDatos);
      }

      $ArrayDatos['gral']['cantRegistro'] = $filasEnXLS - 1;
      $ArrayDatos['gral']['totalProcesados'] = $total;
      $ArrayDatos['gral']['filasCorrectas'] = $filasCorrectas;
      $ArrayDatos['gral']['filasSinLote'] = $filasSinLote;
      $ArrayDatos['gral']['filasIncompletas'] = $filasIncompletas;
      $ArrayDatos['gral']['filasSinTotal'] = $filasSinSumaCorrecta;
      $ArrayDatos['gral']['filasSinBD'] = $filasSinBD;

      $ArrayDatos['gral']['filasSinPzas'] = $filasSinPzas;

      $ArrayDatos['error'] = 0;
      $ArrayDatos['msj'] = 'Ok';
    }
    return $ArrayDatos;
  }

  public function busquedaLote($lote, $Data)
  {
    $return = array();
    foreach ($Data as $value) {
      if ($value['loteTemola'] == $lote) {
        $return = $value;
      }
    }
    return $return;
  }
  public function busquedaStock($idRendimiento, $Data)
  {
    $return = array();
    foreach ($Data as $value) {
      if ($value['idRendimiento'] == $idRendimiento) {
        $return = $value;
      }
    }
    return $return;
  }
}
