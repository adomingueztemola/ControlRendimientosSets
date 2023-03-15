<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Proveedor.php');
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_proveedor = new Proveedor($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}
$DataJiru = $obj_proveedor->getRendimientoAreaXProveedor("p.idCatProveedor='7'");
$DataGalaviz = $obj_proveedor->getRendimientoAreaXProveedor("p.idCatProveedor='4'");
$DataPMP = $obj_proveedor->getRendimientoAreaXProveedor("p.idCatProveedor='4'");

$_AreaWBxSetJiru = $DataJiru['promAreaWBUnit'] == '' ? '0' : formatoMil($DataJiru['promAreaWBUnit']);
$_CostoWBxSetJiru = $DataJiru['promCostoWBUnit'] == '' ? '0' : formatoMil($DataJiru['promCostoWBUnit']);

$_AreaWBxSetGalaviz = $DataGalaviz['promAreaWBUnit'] == '' ? '0' : formatoMil($DataGalaviz['promAreaWBUnit']);
$_CostoWBxSetGalaviz = $DataGalaviz['promCostoWBUnit'] == '' ? '0' : formatoMil($DataGalaviz['promCostoWBUnit']);

$_AreaWBxSetPMP = $DataPMP['promAreaWBUnit'] == '' ? '0' : formatoMil($DataPMP['promCostoWBUnit']);
$_CostoWBxSetPMP = $DataPMP['promCostoWBUnit'] == '' ? '0' : formatoMil($DataPMP['promCostoWBUnit']);
?>
<div id="data-color"></div>
<script src="../assets/extra-libs/c3/d3.min.js"></script>
<script src="../assets/extra-libs/c3/c3.min.js"></script>
<script>
    $(function() {
        var a = c3.generate({
            bindto: "#data-color",
            size: {
                height: 400
            },
            data: {
                columns: [
                    ["JIRU", <?= $_CostoWBxSetJiru ?>, <?= $_AreaWBxSetJiru ?>],
                    ["GALAVIZ", <?= $_CostoWBxSetGalaviz ?>, <?= $_AreaWBxSetGalaviz ?>],
                    ["PMP", <?= $_CostoWBxSetPMP ?>, <?= $_AreaWBxSetPMP ?>]
                ],
                type: "bar",
                colors: {
                    data1: "#4fc3f7",
                    data2: "#2962FF"
                },
                color: function(a, o) {
                    return o.id && "data3" === o.id ? d3.rgb(a).darker(o.value / 150) : a
                }
            },
            grid: {
                y: {
                    show: !0
                }
            },
            axis: {
                x: {
                    type: 'categorized',
                    categories: ['Costo WB x Set', 'Área WB x Set']
                },
                rotated: true
            }
        });
    });
</script>