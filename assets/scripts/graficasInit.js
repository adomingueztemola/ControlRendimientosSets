$(function () {
  "use strict";
  new Chart(document.getElementById("sets-chart"), {
    type: "line",
    data: {
      labels: labelsWeek,
      datasets: dataSets,
    },
   
    options: {
      responsive:true,
      maintainAspectRatio:false,
      title: {
        display: true,
        text: "SET'S POR SEMANA DE PRODUCCIÓN",
      },
    },
  });


  new Chart(document.getElementById("wb-chart"), {
    type: "line",
    data: {
      labels: labelsWbWeek,
      datasets: dataWB,
    },
   
    options: {
      responsive:true,
      maintainAspectRatio:false,
      title: {
        display: true,
        text: "WET BLUE POR SEMANA DE PRODUCCIÓN",
      },
    },
  });

  new Chart(document.getElementById("m2autocza-chart"), {
    type: "line",
    data: {
      labels: labelsCzaWeek,
      datasets: dataCza,
    },
   
    options: {
      responsive:true,
      maintainAspectRatio:false,
      title: {
        display: true,
        text: "M2 AUTO CZA POR SEMANA DE PRODUCCIÓN",
      },
    },
  });

  new Chart(document.getElementById("m2autopiel-chart"), {
    type: "line",
    data: {
      labels: labelsPielWeek,
      datasets: dataPiel,
    },
   
    options: {
      responsive:true,
      maintainAspectRatio:false,
      title: {
        display: true,
        text: "M2 AUTO PIEL POR SEMANA DE PRODUCCIÓN",
      },
    },
  });

  new Chart(document.getElementById("etq-chart"), {
    type: "line",
    data: {
      
      labels: labelsEtqWeek,
      datasets: dataEtiquetas,
    },
  
    options: {
      responsive:true,
      maintainAspectRatio:false,
      title: {
        display: true,
        text: "M2 ETIQUETAS POR SEMANA DE PRODUCCIÓN",
      },
    
    }, 
  });

  new Chart(document.getElementById("clz-chart"), {
    type: "line",
    data: {
      labels: labelsClzWeek,
      datasets: dataClz,
    },
   
    options: {
      responsive:true,
      maintainAspectRatio:false,
      title: {
        display: true,
        text: "M2 CALZADO POR SEMANA DE PRODUCCIÓN",
      },
    },
  });

 
});
