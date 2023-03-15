<?php
class LecturaXLS extends ConexionBD
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
      $inputFileType = PHPExcel_IOFactory::identify($file);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($file);
      $sheet = $objPHPExcel->getSheet(0);
      $filasEnXLS = $sheet->getHighestDataRow();
      $columnasEnXLS = $sheet->getHighestDataColumn();
      $ArrayDatos['gral'] = array();
      $num = 0;
      $total = 0;
      $filasCorrectas = 0;
      $filasSinTurno = 0;
      $filasIncompletas = 0;
      $estatTurno = '';
      $colorFila = '';
      $ArrayDatos['val'] = array();
      //ENCONTRAR EL NUMERO DE LOTE
      $descrip = str_replace("'", "", $sheet->getCell("B2")->getValue());
      $descrip = stripslashes($descrip);
      $ArrayDescrip = explode('/', $descrip);
      $loteTemola = $ArrayDescrip[0];
      $this->beginTransaction();
      //CONSULTAR EXISTENCIA
      $DataLote = $this->checarRegistroLote($loteTemola);
      $DataLote = $DataLote == '' ? array() : $DataLote;
      if (count($DataLote) <= 0) {
        $this->errorBD("LOTE TEMOLA -{$loteTemola}- NO IDENTIFICADO, PIDE AL ÁREA DE PROGRAMACIÓN SU REGISTRO, SI EL ERROR PERSISTE CONTACTA AL EQUIPO DE SISTEMAS", 1);
        exit(0);
      }
      $idRendimiento = $DataLote['id'];
      //CONSULTAR PIEZAS MARCADAS
      $timestamp1 = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("J7")->getValue());
      $pza1 = gmdate("H:i", (int)$timestamp1);
      $timestamp2 = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("K7")->getValue());
      $pza2 = gmdate("H:i", (int)$timestamp2);
      $timestamp3 = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("L7")->getValue());
      $pza3 = gmdate("H:i", (int)$timestamp3);
      $timestamp4 = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("M7")->getValue());
      $pza4 = gmdate("H:i", (int)$timestamp4);
      //CONTEO DE PIEZAS
      $totalPzas1 = $sheet->getCell("J8")->getValue() == '' ? '0' : $sheet->getCell("J8")->getValue();
      $totalPzas2 = $sheet->getCell("K8")->getValue() == '' ? '0' : $sheet->getCell("K8")->getValue();
      $totalPzas3 = $sheet->getCell("L8")->getValue() == '' ? '0' : $sheet->getCell("L8")->getValue();
      $totalPzas4 = $sheet->getCell("M8")->getValue() == '' ? '0' : $sheet->getCell("M8")->getValue();

      $ArrayPiezas = array(
        $pza1 => $totalPzas1,
        $pza2 => $totalPzas2,
        $pza3 => $totalPzas3,
        $pza4 => $totalPzas4
      );
      $totalPzas =  $totalPzas1 +  $totalPzas2 +
        $totalPzas3 +  $totalPzas4;
      //VALIDA PIEZAS CLASIFICADAS SEAN IGUALES A LAS PIEZAS REPORTADAS 
      $TOTALPZASMARCADAS = $sheet->getCell("G" . $filasEnXLS)->getCalculatedValue() == '' ? '0' : $sheet->getCell("G" . $filasEnXLS)->getCalculatedValue();
      if ($TOTALPZASMARCADAS != $totalPzas) {
        $this->errorBD("LA CANTIDAD DE PIEZAS CLASIFICADAS NO COINCIDEN CON LAS PIEZAS TOTALES CORTADAS, FAVOR DE VERIFICAR LOS VALORES.", 1);
      }
      //AREAS REPORTADA POR TESEO
      $AREAREPORTADAXTESEO = $sheet->getCell("H" . $filasEnXLS)->getCalculatedValue() == '' ? '0' : $sheet->getCell("H" . $filasEnXLS)->getCalculatedValue();
      //RENDIMIENTO REPORTADA POR TESEO
      $RENDIMIENTOXTESEO = $sheet->getCell("J" . $filasEnXLS)->getCalculatedValue() == '' ? '0' : $sheet->getCell("J" . $filasEnXLS)->getCalculatedValue() * 100;
      //AREA REAL POR TESEO
      $AREAREALTESEO = $sheet->getCell("I" . $filasEnXLS)->getCalculatedValue() == '' ? '0' : $sheet->getCell("I" . $filasEnXLS)->getCalculatedValue();

      //CONSULTAR EXISTENCIA DE CONTEO EN TESEO
      $DataMarcado = $this->checarMarcadoLote($idRendimiento);
      $DataMarcado = $DataMarcado == '' ? array() : $DataMarcado;
      if (count($DataMarcado) <= 0) {
        //AGREGAR DATO DE MARCADO DE TESEO
        $datos = $this->agregarMarcadoTeseo($idRendimiento, $totalPzas, $ArrayPiezas);
        try {
          Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
          $this->errorBD($e->getMessage(), 1);
        }
        $idMarcado = $datos[2];
      } else {
        //ACTUALIZAR DATO DE MARCADO DE TESEO
        $idMarcado = $DataMarcado["id"];
        $datos = $this->actualizarMarcadoTeseo($idMarcado, $ArrayPiezas, $totalPzas);
        try {
          Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
          $this->errorBD($e->getMessage(), 1);
        }
      }

      //REMANENTES DE TESEO
      $ArrayRemanentes = array();
      $timestamp1_rem = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("E7")->getValue());
      $pza1_rem = gmdate("H:i", (int)$timestamp1_rem);
      $timestamp2_rem = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("F7")->getValue());
      $pza2_rem = gmdate("H:i", (int)$timestamp2_rem);
      $timestamp3_rem = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("G7")->getValue());
      $pza3_rem = gmdate("H:i", (int)$timestamp3_rem);
      $timestamp4_rem = PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell("H7")->getValue());
      $pza4_rem = gmdate("H:i", (int)$timestamp4_rem);
      //CONTEO DE PIEZAS REMANENTES
      $totalPzas1_rem = $sheet->getCell("E8")->getValue() == '' ? '0' : $sheet->getCell("E8")->getValue();
      $totalPzas2_rem = $sheet->getCell("F8")->getValue() == '' ? '0' : $sheet->getCell("F8")->getValue();
      $totalPzas3_rem = $sheet->getCell("G8")->getValue() == '' ? '0' : $sheet->getCell("G8")->getValue();
      $totalPzas4_rem = $sheet->getCell("H8")->getValue() == '' ? '0' : $sheet->getCell("H8")->getValue();
      $ArrayRemanentes = array(
        $pza1_rem => $totalPzas1_rem,
        $pza2_rem => $totalPzas2_rem,
        $pza3_rem => $totalPzas3_rem,
        $pza4_rem => $totalPzas4_rem
      );
      $totalPzas_rem = $totalPzas1_rem + $totalPzas2_rem +
        $totalPzas3_rem + $totalPzas4_rem;
      $loteTemolaFinal = $sheet->getCell("D8")->getValue() == '' ? '0' : $sheet->getCell("D8")->getValue();
      if ($loteTemolaFinal == '0') {
        $this->errorBD("NO SE IDENTIFICA EL LOTE QUE RECIBIRÁ EL REMANENTE DESEADO.", 1);
        exit(0);
      }

      //CONSULTAR LOTE PARA AGREGAR 
      $DataLoteRem = $this->checarRegistroLote($loteTemolaFinal);
      $DataLoteRem = $DataLoteRem == '' ? array() : $DataLoteRem;
      if (count($DataLoteRem) <= 0) {
        $this->errorBD("LOTE TEMOLA REMANENTE -{$loteTemolaFinal}- NO IDENTIFICADO, PIDE AL ÁREA DE PROGRAMACIÓN SU REGISTRO, SI EL ERROR PERSISTE CONTACTA AL EQUIPO DE SISTEMAS", 1);
        exit(0);
      }
      $idRendimientoFinal = $DataLoteRem['id'];
      //ELIMINAR  REMANENTES PASADOS
      $datos = $this->eliminarRemanentes($idMarcado);
      try {
        Excepciones::validaMsjError($datos);
      } catch (Exception $e) {
        $this->errorBD($e->getMessage(), 1);
      }
      //AGREGAR NUEVO  REMANENTE
      $datos = $this->agregarRemanentes($idMarcado, $ArrayRemanentes, $totalPzas_rem, $idRendimientoFinal);
      try {
        Excepciones::validaMsjError($datos);
      } catch (Exception $e) {
        $this->errorBD($e->getMessage(), 1);
      }

      //AGREGAR PZAS DE TESEO CORTADAS MENOS REMANENTE
      $pzasTotalesTeseo = $totalPzas - $totalPzas_rem;
      $datos = $this->paseDeMarcadoDeTeseo($idRendimiento, $totalPzas, $totalPzas_rem, $pzasTotalesTeseo, $RENDIMIENTOXTESEO, $AREAREPORTADAXTESEO, $AREAREALTESEO);
      try {
        Excepciones::validaMsjError($datos);
      } catch (Exception $e) {
        $this->errorBD($e->getMessage(), 1);
      }
      $datos = $this->sumarRemanentesDeTeseo($totalPzas_rem, $idRendimientoFinal);
      try {
        Excepciones::validaMsjError($datos);
      } catch (Exception $e) {
        $this->errorBD($e->getMessage(), 1);
      }

      $this->insertarCommit();






      /*  for ($row = 2; $row <= $filasEnXLS; $row++) {
        $num++;

        $ArrayDatos['val'][$num] = array();
        /***************************************************
         *Limpiar caracteres de los numeros de trabajadores*
         ****************************************************/
      /*   $descrip = str_replace("'", "", $sheet->getCell("A" . $row)->getValue());
         
        /***************************************************
         *validar registro de trabajadores en la BD*
         ****************************************************/



      /*  echo '
            <tr class="">
              <th scope="row">'.$num.' </th>
              <td>'.$sheet->getCell("B".$row)->getValue().'</td>
              <td>'.$sheet->getCell("C".$row)->getValue().'</td>
              <td>'.$sheet->getCell("D".$row)->getValue().'</td>
              <td>'.$sheet->getCell("E".$row)->getValue().'</td>
            </tr>';

        $total++;*/
    }


    /* echo '
            </tbody>
          </table>
          ';  */

    /*  $ArrayDatos['gral']['cantRegistro'] = $filasEnXLS - 1;
      $ArrayDatos['gral']['totalProcesados'] = $total;
      $ArrayDatos['gral']['filasCorrectas'] = $filasCorrectas;
      $ArrayDatos['gral']['filasSinTurno'] = $filasSinTurno;
      $ArrayDatos['gral']['filasIncompletas'] = $filasIncompletas;
      $ArrayDatos['error'] = 0;
      $ArrayDatos['msj'] = 'Ok';*/
    //  }

    return $ArrayDatos;
  }

  public function checarRegistroLote($loteTemola)
  {
    $sql = "SELECT * FROM rendimientos WHERE loteTemola='$loteTemola'
          AND estado>='2'";
    return  $this->consultarQuery($sql, "revisión de Lote Temola", false);
  }

  public function checarMarcadoLote($idRendimiento)
  {
    $sql = "SELECT * FROM marcadoteseo WHERE idRendimiento='{$idRendimiento}'";
    return  $this->consultarQuery($sql, "revisión de Lote Temola", false);
  }

  public function agregarMarcadoTeseo($idRendimiento, $totalPzas, $ArrayPiezas)
  {
    $detallePiezas = "";
    $detalleCampos = "";
    foreach ($ArrayPiezas as $key => $value) {
      if ($key == '12:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _12";
      }
      if ($key == '03:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _3";
      }
      if ($key == '09:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _9";
      }
      if ($key == '06:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _6";
      }
    }
    $sql = "INSERT INTO marcadoteseo (idRendimiento, totalPzas, fechaReg, idUserReg{$detalleCampos}) 
            VALUES('$idRendimiento', '$totalPzas',NOW(), '99'$detallePiezas )";
    return  $this->ejecutarQuery($sql, "agregar Marcado de Lote Temola", true);
  }
  public function actualizarMarcadoTeseo($idMarcado, $ArrayPiezas, $totalPzas)
  {
    $detallePiezas = "";
    foreach ($ArrayPiezas as $key => $value) {
      if ($key == '12:00') {
        $detallePiezas .= ", _12='{$value}'";
      }
      if ($key == '03:00') {
        $detallePiezas .= ",  _3='{$value}'";
      }
      if ($key == '09:00') {
        $detallePiezas .= ",  _9='{$value}'";
      }
      if ($key == '06:00') {
        $detallePiezas .= ",  _6='{$value}'";
      }
    }
    $sql = "UPDATE marcadoteseo SET totalPzas='$totalPzas', fechaReg=NOW()$detallePiezas WHERE id='$idMarcado'";
    return  $this->ejecutarQuery($sql, "editar Marcado de Lote Temola");
  }
  public function agregarRemanentes($idMarcado, $ArrayPiezas, $totalPzas, $idRendimientoFinal)
  {
    $detallePiezas = "";
    $detalleCampos = "";
    foreach ($ArrayPiezas as $key => $value) {
      if ($key == '12:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _12";
      }
      if ($key == '03:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _3";
      }
      if ($key == '09:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _9";
      }
      if ($key == '06:00') {
        $detallePiezas .= ", '{$value}'";
        $detalleCampos .= ", _6";
      }
    }
    $sql = "INSERT INTO remanentesteseo (idRendimientoFinal, idMarcado, totalPzas, fechaReg, idUserReg{$detalleCampos}) 
            VALUES('$idRendimientoFinal','$idMarcado', '$totalPzas',NOW(), '99'$detallePiezas )";
    return  $this->ejecutarQuery($sql, "agregar Remanente de Lote Temola");
  }

  public function eliminarRemanentes($idMarcado)
  {
    $sql = "DELETE FROM remanentesteseo WHERE idMarcado='$idMarcado'";
    return  $this->ejecutarQuery($sql, "eliminar Remanentes de Lote Temola");
  }

  public function sumarRemanentesDeTeseo($totalPzas_rem,  $idRendimientoFinal)
  {
    $sql = "UPDATE rendimientos SET pzasAgregTeseo='$totalPzas_rem',  
    pzasCortadasTeseo=IFNULL(pzasInitTeseo,0)+$totalPzas_rem
    WHERE id='$idRendimientoFinal'";
    return  $this->ejecutarQuery($sql, "actualizar Remanente de Lote Temola");
  }
  public function paseDeMarcadoDeTeseo($idRendimiento, $totalPzas, $totalPzas_rem,  $pzasTotalesTeseo,  $RENDIMIENTOXTESEO, $AREAREPORTADAXTESEO, $AREAREALTESEO)
  {
    $sql = "UPDATE rendimientos SET pzasTotalesTeseo='$totalPzas',pzasRemanentes='$totalPzas_rem',  
                 pzasCortadasTeseo='$pzasTotalesTeseo'+IFNULL(pzasAgregTeseo,0), yieldInicialTeseo='$RENDIMIENTOXTESEO', 
                 areaFinal='$AREAREPORTADAXTESEO', areaRealTeseo='$AREAREALTESEO', pzasInitTeseo='$pzasTotalesTeseo'
          WHERE id='$idRendimiento'";
    return  $this->ejecutarQuery($sql, "actualizar Teseo de Lote Temola");
  }
}
