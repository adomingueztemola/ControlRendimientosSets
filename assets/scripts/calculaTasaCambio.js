function calculaUSDPiel() {
 v_tc= $("#tcPiel").val().replace(",", "")
 v_usd= $("#precioUSDPiel").val().replace(",", "")
 //result= new Intl.NumberFormat('es-MX').format(((v_usd*v_tc)/9.29).toFixed(2))
 result= (((v_usd*v_tc)/9.29).toFixed(2))
$("#precioPesoPiel").val(result)

}

function calculaUSDCza() {
    v_tc= $("#tcCza").val().replace(",", "")
    v_pesos= $("#precioPesoCza").val().replace(",", "")
    //result= new Intl.NumberFormat('es-MX').format(((v_pesos*9.29)/v_tc).toFixed(2))
    result= ((v_pesos*9.29)/v_tc).toFixed(2)
   $("#precioUSDCza").val(result)
   
   }