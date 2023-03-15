<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$id = !empty($_GET['id']) ? $_GET['id'] : '';
$obj_piezas = new PzasOKNOK($debug, $idUser);
$Data = $obj_piezas->getLotesClasificados($id);
$Data = Excepciones::validaConsulta($Data);
$Str_Array = "";
foreach ($Data as $key => $value) {
    $_12OK = formatoMil($value['_12OK'], 0);
    $_3OK = formatoMil($value['_3OK'], 0);
    $_6OK = formatoMil($value['_6OK'], 0);
    $_9OK = formatoMil($value['_9OK'], 0);

    $_12NOK = formatoMil($value['_12NOK'], 0);
    $_3NOK = formatoMil($value['_3NOK'], 0);
    $_6NOK = formatoMil($value['_6NOK'], 0);
    $_9NOK = formatoMil($value['_9NOK'], 0);
    $pzasCortadasTeseo = formatoMil($value['pzasCortadasTeseo'], 0);

    $Str_Array .= "
    {value: {$value['pzasOk']}, 
     name: 'Piezas Ok',
    
    },
    {value: {$value['pzasNok']}, 
     name: 'Piezas NOK',
    
    }";
}
?>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xl-6">
    <div class="card">
        <div class="card-body analytics-info">
            <div id="customized-chart" style="height:400px;"></div>
        </div>

    </div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xl-6">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Detallado de Piezas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>12:00</td>
                <td>
                    <span class="text-success">OK:<?= $_12OK ?></span><br>
                    <span class="text-danger">NOK: <?= $_12NOK ?></span>
                </td>
            </tr>
            <tr>
                <td>03:00</td>
                <td>
                    <span class="text-success">OK:<?= $_3OK ?></span><br>
                    <span class="text-danger">NOK: <?= $_3NOK ?></span>
                </td>
            </tr>
            <tr>
                <td>06:00</td>
                <td>
                    <span class="text-success">OK:<?= $_6OK ?></span><br>
                    <span class="text-danger">NOK: <?= $_6NOK ?></span>
                </td>
            </tr>
            <tr>
                <td>09:00</td>
                <td>
                    <span class="text-success">OK:<?= $_9OK ?></span><br>
                    <span class="text-danger">NOK: <?= $_9NOK ?></span>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="col-lg-12" style="height:95px">
        <div class="card-body mb-0 bordered">
            <div class="d-flex no-block align-items-center">
                <img src="../assets/images/TESEO.jpg" width="50%" alt="" srcset="">
                <div class="mx-5 text-rigth">
                    <h2>
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><?=$pzasCortadasTeseo?></font>
                        </font>
                    </h2>

                </div>
            </div>
        </div>
    </div>

</div>


<!-- This Page JS -->
<script src="../assets/libs/echarts/dist/echarts-en.min.js"></script>
<script>
    // ------------------------------
    // GRAFICA DE LLENADO DE LOTE
    // ------------------------------

    var customizedChart = echarts.init(document.getElementById('customized-chart'));
    var option = {

        backgroundColor: '#fff',

        title: {
            text: 'Clasificación de Piezas',
            left: 'center',
            top: 20,
            textStyle: {
                color: '#ccc'
            }
        },

        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },

        visualMap: {
            show: false,
            min: 80,
            max: 600,
            inRange: {
               color: ['#F51414', '#F1C205', 'green'],
            }
        },
        series: [{
            name: 'Tipos de Piezas',
            type: 'pie',
            radius: '55%',
            center: ['50%', '50%'],
            data: [<?= $Str_Array ?>

            ].sort(function(a, b) {
                return a.value - b.value;
            }),

            roseType: 'radius',
            label: {
                normal: {
                    textStyle: {
                        color: 'rgba(0, 0, 0, 0.3)'
                    }
                }
            },
            labelLine: {
                normal: {
                    lineStyle: {
                        color: 'rgba(0, 0, 0, 0.3)'
                    },
                    smooth: 0.2,
                    length: 10,
                    length2: 20
                }
            },
            itemStyle: {
                normal: {
                    color: ['#2962FF','#2962FF'],
                    shadowBlur: 200,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },

            animationType: 'scale',
            animationEasing: 'elasticOut',
            animationDelay: function(idx) {
                return Math.random() * 200;
            }
        }],

    };


    customizedChart.setOption(option);
</script>