<?php
if(isset($_GET['Ml_Config'])){
    $data= [];
    foreach($_POST as $key => $valor){
        $data[$key]=$valor;
    };
    $query = DBUpdate('ecommerce_mercadolivre', $data, "id = '1'");
    if ($query != 0) {
        header('Location: https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id='.$data['appid'].'&redirect_uri='.ConfigPainel('base_url').'ecommerce.php?MercadoLivre');
    } else {
        Redireciona('?MercadoLivre&erro');
  }
}
if(isset($_GET['Ml_Token'])){
    $query = DBUpdate('ecommerce_mercadolivre', ['token'=>$_GET['Ml_Token']], "id = '1'"); 
}