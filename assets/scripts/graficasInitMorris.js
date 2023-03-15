$(function () {
  "use strict";
  Morris.Bar({
    element: 'morris-bar-chart',
    data: data,
    xkey: 'y',
    ykeys: ['a', 'b'],
    labels: ['PEDIDO', 'VENTAS'],
    barColors:['#B22727', '#413F42'],
    hideHover: 'auto',
    gridLineColor: '#eef0f2',
    resize: true,
    xLabelAngle: 45,

    xLabelFormat: function (x) {

      return "Sem."+x.label;
  },
});

 
});
