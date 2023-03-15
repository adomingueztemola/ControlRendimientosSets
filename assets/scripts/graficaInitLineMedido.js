$(function () {
    "use strict";
    var gradiantChart = echarts.init(document.getElementById("g-marcado"));
  
    // specify chart configuration item and data
    var data = dataLineaMarcado;
  
    var dateList = data.map(function (item) {
      return item[0];
    });
    var valueList = data.map(function (item) {
      return item[1];
    });
  
    var option = {
      // Make gradient line here
      visualMap: [{
        show: false,
        type: 'continuous',
        dimension: 0,
  
        min: 0,
        max: dateList.length - 1
      }
      ],
  
      title: [
       
        {
          top: "10%",
          left: "center",
          text: "CONTEO DE LOTES DE MARCADO A MANO",
        },
      ],
      tooltip: {
        trigger: "axis",
      },
  
      xAxis: [
     
        {
          data: dateList,
        },
      ],
      yAxis: [
   
        {
          splitLine: { show: false },
        },
      ],
      grid: [
       
        {
          top: "10%",
          left: "3%",
          right: "3%",
        },
      ],
  
      series: [
    
        {
          type: 'line',
          showSymbol: false,
          data: valueList
      }
      ],
    };
    // use configuration item and data specified to show chart
    gradiantChart.setOption(option);
    $(function () {
      // Resize chart on menu width change and window resize
      $(window).on("resize", resize);
      $(".sidebartoggler").on("click", resize);
  
      // Resize function
      function resize() {
        setTimeout(function () {
          // Resize chart
          gradiantChart.resize();
        }, 200);
      }
    });
  
  });
  
  