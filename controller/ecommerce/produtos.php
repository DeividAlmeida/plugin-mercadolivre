<?php
//
// PRODUTO
//

// Pasta para upload dos arquivos enviados
$produto_upload_folder = 'wa/ecommerce/uploads/';
if( file_exists('mercadolivre.php')){
    $MLtoken = DBRead('ecommerce_mercadolivre', '*')[0];
}
function deletarProduto($id){
  // Exclui todas fotos do produto
  $lista_fotos = DBRead('ecommerce_prod_imagens','*', "WHERE id_produto = {$id}");
  foreach($lista_fotos as $foto){
    @unlink($produto_upload_folder.$foto['uniq']);
  }

  deletarMatrizProduto($id);

  // Deleta no banco: produto, imagens, produtos relacionados e categorias relacionadas
  DBDelete('ecommerce',"id = {$id}");
  DBDelete('ecommerce_prod_imagens',"id_produto = {$id}");
  DBDelete('ecommerce_prod_relacionados',"id_produto = {$id}");
  DBDelete('ecommerce_prod_categorias',"id_produto = {$id}");
}

// Adicionar Produto
if (isset($_GET['AddProduto'])) {
  $data = array(
    'nome'            => post('nome'),
    'descricao'       => post('descricao'),
    'resumo'          => post('resumo'),
    'codigo'          => post('codigo'),
    'url'             => post('url'),
    'palavras_chave'  => post('palavras_chave'),
    'preco'           => post('preco'),
    'etiqueta'        => post('etiqueta'),
    'etiqueta_cor'    => post('etiqueta_cor'),
    'estoque'         => post('estoque'),
    'btn_texto'       => post('btn_texto'),
    'ordem_manual'    => post('ordem_manual'),
    'diminuir_est'    => post('diminuir_est'),
    'peso'            => post('peso'),
    'comprimento'     => post('comprimento'),
    'altura'          => post('altura'),
    'status'          => post('status'),
    'largura'         => post('largura')
  );

  // Cadastra produto e cria ID
  $id_produto = DBCreate('ecommerce', $data, true);
  
  $zero = 0;
  $data2 = array('id_produto' => $id_produto, );
  DBUpdate('ecommerce_prod_termos', $data2, "id_produto = {$zero}");
  if( file_exists('estoque.php')){
    DBCreate('ecommerce_estoque',  ['ref'=>$id_produto,'min'=>5]);
  }
  if(!$id_produto) { Redireciona('?ListarProduto&erro'); }

  // Cadastra todas categorias informadas
  if($_POST['categorias']){
    foreach(post('categorias') as $categoria){
      $query = DBCreate('ecommerce_prod_categorias', array(
        'id_produto'    => $id_produto,
        'id_categoria'  => $categoria
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  if(post('produtos_relacionados') ){
    foreach(post('produtos_relacionados') as $produto_relacionado){
      $query = DBCreate('ecommerce_prod_relacionados', array(
        'id_produto'              => $id_produto,
        'id_produto_relacionado'  => $produto_relacionado
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  if(isset($_POST['marcas'])){
    foreach(post('marcas') as $marcas){
      $query = DBCreate('ecommerce_prod_marcas', array(
        'id_produto'              => $id_produto,
        'id_marca'                => $marcas
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  // Fazendo upload das fotos arquivos
  foreach($_FILES as $chave => $valor){
    // Cria  um nome unico para o arquivo e pega ID da foto no form
    $nome_arquivo = md5(uniqid(rand(), true)).'.jpg';
    $id_foto_form = str_replace("foto_","",$chave);
      $picsource .=   '{ "source": "'.ConfigPainel('base_url').'wa/ecommerce/uploads/'.$nome_arquivo.'" },';

    // Tenta fazer upload da foto
    if (move_uploaded_file($_FILES[$chave]['tmp_name'], $produto_upload_folder.$nome_arquivo)) {

      // Cadastra no banco de dados o nome do arquivo e ID do produto
      $id_foto = DBCreate('ecommerce_prod_imagens', array(
        'id_produto'  => $id_produto,
        'uniq'        => $nome_arquivo
      ), true);

      if(!$id_foto) { Redireciona('?ListarProduto&erro'); }

      // Se a foto selecionada como capa for essa, atualiza o banco de dados com o seu ID
      if(post('capa') == $id_foto_form){
        $query = DBUpdate('ecommerce', array('id_imagem_capa' => $id_foto), "id = {$id_produto}");
        if(!$query) { Redireciona('?ListarProduto&erro'); }
      }
    }
    else{
      Redireciona('?ListarProduto&erro');
    }
  }
if( file_exists('mercadolivre.php')){
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.mercadolibre.com/items',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "title": "'.post('nome').'",
        "category_id": "'.$_POST['categoria_ml'].'",
        "price": '.post('preco').',
        "currency_id": "BRL",
        "available_quantity": 1,
        "buying_mode": "buy_it_now",
        "condition": "new",
        "listing_type_id": "gold_special",
        "pictures": [
            '.$picsource.'
        ],'.$_POST['atributos_ml'].'
        
    }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$MLtoken['token'],
        'Content-Type: application/json',
      ),
    ));
    
    $response = curl_exec($curl);
    $response = json_decode($response);
    curl_close($curl);
    DBUpdate('ecommerce', array('id_ml' => $response->id), "id = {$id_produto}");
}
  try{
    atualizarMatrizProduto($id_produto);
    var_dump($id_produto);
    #Redireciona('?ListarProduto&sucesso');
  } catch (\Exception $e) {
    Redireciona('?ListarProduto&erro');
  }
  


}

// Atualizar Produto
if (isset($_GET['AtualizarProduto'])) {
  $id_produto   = get('AtualizarProduto');
  $data = array(
    'nome'            => post('nome'),
    'descricao'       => post('descricao'),
    'codigo'          => post('codigo'),
    'url'             => post('url'),
    'palavras_chave'  => post('palavras_chave'),
    'preco'           => post('preco'),
    'resumo'           => post('resumo'),
    'etiqueta'        => post('etiqueta'),
    'etiqueta_cor'    => post('etiqueta_cor'),
    'estoque'         => post('estoque'),
    'btn_texto'       => post('btn_texto'),
    'ordem_manual'    => post('ordem_manual'),
    'diminuir_est'    => post('diminuir_est'),
    'peso'            => post('peso'),
    'comprimento'     => post('comprimento'),
    'altura'          => post('altura'),
    'status'          => post('status'),
    'largura'         => post('largura')
  );

  $query = DBUpdate('ecommerce', $data, "id = {$id_produto}");
  $zero = 0;
  $data2 = array('id_produto' => $id_produto, );
  DBUpdate('ecommerce_prod_termos', $data2, "id_produto = {$zero}");

  if(!$query) { Redireciona('?ListarProduto&erro'); }

  $produtos_relacionados = post('produtos_relacionados');
  $categorias = post('categorias');
  $marcas = post('marcas');

  /**
   * ATUALIZAÇÃO PRODUTOS RELACIONADOS E CATEGORIAS
   */
  // Carrega as variaveis e caso n exista insere uma array vazia para evitar erro
  $post_produtos_relacionados = !empty($produtos_relacionados) ? $produtos_relacionados : array();
  $post_categorias = !empty($categorias) ? $categorias : array();
  $post_marcas = !empty($marcas) ? $marcas : array();

  /**
   * ATUALIZAÇÃO CATEGORIAS
   */
  // Pegando todos os ID's da categoria do produto atual
  $lista_ids_categorias = DBRead('ecommerce_prod_categorias', 'id_categoria', "WHERE id_produto = {$id_produto}");
  $ids_categorias       = array();
  foreach ($lista_ids_categorias as $linha) {
    array_push($ids_categorias, $linha['id_categoria']);
  }

  $categorias_novas = array_diff($post_categorias, $ids_categorias);
  $categorias_para_excluir = array_diff($ids_categorias, $post_categorias);

  // Cadastra todas categorias informadas
  if(count($categorias_novas) > 0){
    foreach($categorias_novas as $categoria){
      $query = DBCreate('ecommerce_prod_categorias', array(
        'id_produto'    => $id_produto,
        'id_categoria'  => $categoria
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  if(count($categorias_para_excluir) > 0){
    foreach($categorias_para_excluir as $categoria){
      DBDelete('ecommerce_prod_categorias',"id_produto = {$id_produto} AND id_categoria ={$categoria}");
    }
  }
  /**
   * ATUALIZAÇÃO MARCAS
   */
  // Pegando todos os ID's da marcar do produto atual
  $lista_ids_marcas = DBRead('ecommerce_prod_marcas', 'id_marca', "WHERE id_produto = {$id_produto}");
  $ids_marcas       = array();
  if(is_array($lista_ids_marcas)){
  foreach ($lista_ids_marcas as $linha) {
    array_push($ids_marcas, $linha['id_marca']);
  }}

  $marcas_novas = array_diff($post_marcas, $ids_marcas);
  $marcas_para_excluir = array_diff($ids_marcas, $post_marcas);

  // Cadastra todas marcas informadas
  if(count($marcas_novas) > 0){
    foreach($marcas_novas as $marca){
      $query = DBCreate('ecommerce_prod_marcas', array(
        'id_produto'    => $id_produto,
        'id_marca'  => $marca
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  if(count($marcas_para_excluir) > 0){
    foreach($marcas_para_excluir as $marca){
      DBDelete('ecommerce_prod_marcas',"id_produto = {$id_produto} AND id_marca ={$marca}");
    }
  }
  /**
   * ATUALIZAÇÃO PRODUTO RELACIONADO
   */
  // Buscando as categorias do produto
  $lista_ids_prod_relacionado = DBRead('ecommerce_prod_relacionados', 'id_produto_relacionado', "WHERE id_produto = {$id_produto}");
  $ids_prod_relacionado = array();
  if(is_array($lista_ids_prod_relacionado)){
    foreach ($lista_ids_prod_relacionado as $linha) {
      array_push($ids_prod_relacionado, $linha['id_produto_relacionado']);
    }
  }

  $prod_relacionado_novo = array_diff($post_produtos_relacionados, $ids_prod_relacionado);
  $prod_relacionado_para_excluir = array_diff($ids_prod_relacionado, $post_produtos_relacionados);

  if(count($prod_relacionado_novo) > 0){
    foreach($prod_relacionado_novo as $produto_relacionado){
      $query = DBCreate('ecommerce_prod_relacionados', array(
        'id_produto'              => $id_produto,
        'id_produto_relacionado'  => $produto_relacionado
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  if(count($prod_relacionado_para_excluir) > 0){
    foreach($prod_relacionado_para_excluir as $produto_relacionado){
      DBDelete('ecommerce_prod_relacionados',"id_produto = {$id_produto} AND id_produto_relacionado ={$produto_relacionado}");
    }
  }

  /**
   * ATUALIZAÇÃO ARQUIVOS
   */
  // Fazendo upload das fotos arquivos
  foreach($_FILES as $chave => $valor){
    // Cria  um nome unico para o arquivo e pega ID da foto no form
    $nome_arquivo = md5(uniqid(rand(), true));
    $id_foto_form = str_replace("foto_","",$chave);

    // Tenta fazer upload da foto
    if (move_uploaded_file($_FILES[$chave]['tmp_name'], $produto_upload_folder.$nome_arquivo)) {

      // Cadastra no banco de dados o nome do arquivo e ID do produto
      $id_foto = DBCreate('ecommerce_prod_imagens', array(
        'id_produto'  => $id_produto,
        'uniq'        => $nome_arquivo
      ), true);

      if(!$id_foto) { Redireciona('?ListarProduto&erro'); }

      // Se a foto selecionada como capa for essa, atualiza o banco de dados com o seu ID
      if(post('capa') == $id_foto_form){
        $query = DBUpdate('ecommerce', array('id_imagem_capa' => $id_foto), "id = {$id_produto}");
        if(!$query) { Redireciona('?ListarProduto&erro'); }
      }
    }
    else{
      Redireciona('?ListarProduto&erro');
    }
  }

  /**
   * ATUALIZAÇÃO IMAGEM DE CAPA
   */
  // Confere se começa com "OLD", o que significa que vai alterar a capa para uma imagem já cadastrada
  if (strpos(post('capa'), 'old-') === 0) {
    $id_foto_capa = str_replace("old-","",post('capa'));

    $query = DBUpdate('ecommerce', array('id_imagem_capa' => $id_foto_capa), "id = {$id_produto}");
    if(!$query) { Redireciona('?ListarProduto&erro'); }
  }

  try{
    atualizarMatrizProduto($id_produto);
    Redireciona('?ListarProduto&sucesso');
  } catch (\Exception $e) {
    Redireciona('?ListarProduto&erro');
  }
}

if(isset($_GET['DuplicarProduto'])){
  $id         = get('DuplicarProduto');
  $query     = DBRead('ecommerce','*', "WHERE id = {$id}");
  $produto   = $query[0];
  $produto['nome'] = "Cópia de {$produto['nome']}";
  unset($produto['id']);

  // Cadastra produto e cria ID
  $id_produto = DBCreate('ecommerce', $produto, true);

  if(!$id_produto) { Redireciona('?ListarProduto&erro'); }


  /**
   * ATUALIZAÇÃO CATEGORIAS
   */
  // Pegando todos os ID's da categoria do produto atual
  $lista_ids_categorias = DBRead('ecommerce_prod_categorias', 'id_categoria', "WHERE id_produto = {$id}");
  $ids_categorias       = array();
  foreach ($lista_ids_categorias as $linha) {
    array_push($ids_categorias, $linha['id_categoria']);
  }

  foreach($ids_categorias as $categoria){
    $query = DBCreate('ecommerce_prod_categorias', array(
      'id_produto'    => $id_produto,
      'id_categoria'  => $categoria
    ), true);

    if(!$query) { Redireciona('?ListarProduto&erro'); }
  }

  /**
   * ATUALIZAÇÃO MARCAS
   */
  // Pegando todos os ID's da marca do produto atual
  $lista_ids_marcas = DBRead('ecommerce_prod_marcas', 'id_marca', "WHERE id_produto = {$id}");
  $ids_marcas       = array();
  foreach ($lista_ids_marcas as $linha) {
    array_push($ids_marcas, $linha['id_marca']);
  }

  foreach($ids_marcas as $marca){
    $query = DBCreate('ecommerce_prod_marcas', array(
      'id_produto'    => $id_produto,
      'id_marca'  => $marca
    ), true);

    if(!$query) { Redireciona('?ListarProduto&erro'); }
  }

  /**
   * ATUALIZAÇÃO PRODUTO RELACIONADO
   */
  // Buscando as categorias do produto
  $lista_ids_prod_relacionado = DBRead('ecommerce_prod_relacionados', 'id_produto_relacionado', "WHERE id_produto = {$id}");
  $ids_prod_relacionado = array();
  if(is_array($lista_ids_prod_relacionado)){
    foreach ($lista_ids_prod_relacionado as $linha) {
      array_push($ids_prod_relacionado, $linha['id_produto_relacionado']);
    }
  }

  if(count($ids_prod_relacionado) > 0){
    foreach($ids_prod_relacionado as $produto_relacionado){
      $query = DBCreate('ecommerce_prod_relacionados', array(
        'id_produto'              => $id_produto,
        'id_produto_relacionado'  => $produto_relacionado
      ), true);

      if(!$query) { Redireciona('?ListarProduto&erro'); }
    }
  }

  /**
   * ATUALIZAÇÃO ARQUIVOS
   */
  // Copia fotos do produto
  $lista_ids_fotos = DBRead('ecommerce_prod_imagens', 'uniq', "WHERE id_produto = {$id}");
  $ids_fotos = array();
  if(is_array($lista_ids_fotos)){
    foreach ($lista_ids_fotos as $foto) {
      // Cria  um nome unico para o arquivo e pega ID da foto no form
      $nome_arquivo = md5(uniqid(rand(), true));

      // Copia arquivo de uma pasta para outra
      copy($produto_upload_folder.$foto['uniq'], $produto_upload_folder.$nome_arquivo);

      // Cadastra no banco de dados o nome do arquivo e ID do produto
      $id_foto = DBCreate('ecommerce_prod_imagens', array(
        'id_produto'  => $id_produto,
        'uniq'        => $nome_arquivo
      ), true);

      if(!$id_foto) { Redireciona('?ListarProduto&erro'); }

      // Se a foto selecionada como capa for essa, atualiza o banco de dados com o seu ID
      if($produto['id_imagem_capa'] == $foto['id']){
        $query = DBUpdate('ecommerce', array('id_imagem_capa' => $id_foto), "id = {$id_produto}");
        if(!$query) { Redireciona('?ListarProduto&erro'); }
      }
    }
  }

  try{
    atualizarMatrizProduto($id_produto);
    Redireciona('?ListarProduto&sucesso');
  } catch (\Exception $e) {
    Redireciona('?ListarProduto&erro');
  }
}

// Excluir Produto
if (isset($_GET['DeletarProduto'])) {
  $id     = get('DeletarProduto');
   $id_ml = DBRead('ecommerce','*', "WHERE id = {$id}")[0];
    if( file_exists('mercadolivre.php')){
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.mercadolibre.com/items/'.$id_ml['id_ml'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS =>'{
          "status":"paused"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$MLtoken['token']
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
    }
  try{
    deletarProduto($id);
    #var_dump($response);
    Redireciona('?ListarProduto&sucesso');
  } catch (\Exception $e) {
    Redireciona('?ListarProduto&erro');
  }
}

// Excluir Produtos
if (isset($_GET['ActionProduto'])) {
  $lista_ids = post('ids');
  $curl = curl_init();
  
  try{
    foreach($lista_ids as $id){    
        if( file_exists('mercadolivre.php')){
            $id_ml = DBRead('ecommerce','*', "WHERE id = {$id}")[0];
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.mercadolibre.com/items/'.$id_ml['id_ml'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'PUT',
              CURLOPT_POSTFIELDS =>'{
              "status":"paused"
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$MLtoken['token']
              ),
            ));
            
            $response = curl_exec($curl);    
        }
            
    deletarProduto($id);
    }
    Redireciona('?ListarProduto&sucesso');
  } catch (\Exception $e) {
    Redireciona('?ListarProduto&erro');
  }
     curl_close($curl);
}


// Excluir Foto
if (isset($_GET['DeletarFotoProduto'])) {
  $id     = get('DeletarFotoProduto');

  try{
    // Exclui foto no backend e no BD
    $foto = DBRead('ecommerce_prod_imagens','*', "WHERE id = {$id}");
    @unlink($produto_upload_folder.$foto['uniq']);

    DBDelete('ecommerce_prod_imagens',"id = {$id}");

    http_response_code(200);
    exit();
  } catch (\Exception $e) {
    http_response_code(500);
    exit();
  }
}


if(isset($_GET['AddProdutoTermo'])){
header('Access-Control-Allow-Origin: *');
  require_once('../../includes/funcoes.php');
  require_once('../../database/config.database.php');
  require_once('../../database/config.php'); 
 
   foreach(post('id_termo') as $termo){
    $zero = 0;
   $data = array(
    'id_termo'         => $termo,
    'id_produto'      => $zero,
    'id_atributo'      => post('id_atributo'),
    'valor'            => post('valor'),   
  );
  DBCreate('ecommerce_prod_termos', $data);
   }
}

if(isset($_GET['DeletarProdutoTermo'])){
  header('Access-Control-Allow-Origin: *');
  require_once('../../includes/funcoes.php');
  require_once('../../database/config.database.php');
  require_once('../../database/config.php'); 
  $id =  get('DeletarProdutoTermo');
  DBDelete('ecommerce_prod_termos',"id = {$id}");

}

if(isset($_GET['AddCupom'])){
    
    
if(isset($_POST['produtos'])){
        $resources = array_combine(array_keys($_POST['produtos']), array_map(function ($id) {
        return compact('id');
        }, $_POST['produtos']));
        $_POST['produto'] =    json_encode($resources, JSON_FORCE_OBJECT); 
  }
if(isset($_POST['ex_produtos'])){
        $resources1 = array_combine(array_keys($_POST['ex_produtos']), array_map(function ($id) {
        return compact('id');
        }, $_POST['ex_produtos']));
        $_POST['ex_produto'] =    json_encode($resources1, JSON_FORCE_OBJECT);
}
if(isset($_POST['categorias'])){    
        $resources2 = array_combine(array_keys($_POST['categorias']), array_map(function ($id) {
        return compact('id');
        }, $_POST['categorias']));
        $_POST['categoria'] =    json_encode($resources2, JSON_FORCE_OBJECT);
}
if(isset($_POST['ex_categorias'])){ 
        $resources3 = array_combine(array_keys($_POST['ex_categorias']), array_map(function ($id) {
        return compact('id');
        }, $_POST['ex_categorias']));
        $_POST['ex_categoria'] =    json_encode($resources3, JSON_FORCE_OBJECT);
}
   
     $data = array(
      'codigo'         => post('codigo'),
      'descricao'      => post('descricao'),
      'tipo'           => post('tipo'),
      'valor'          => post('valor'),   
      'frete'          => post('frete'),   
      'produtos'       => $_POST['produto'],   
      'ex_produtos'    => $_POST['ex_produto'],   
      'categorias'     => $_POST['categoria'],   
      'ex_categorias'  => $_POST['ex_categoria'],   
      'limite_cupom'   => post('limite_cupom'),   
      'gasto_mi'       => post('gasto_mi'),   
      'gasto_ma'       => post('gasto_ma'),   
      'uso'            => post('uso'),   
      'ex_oferta'      => post('ex_oferta'),   
      'emails'         => post('emails'),   
      'limite_cliente' => post('limite_cliente'),     
      'data'           => post('data') 
    );
    $query = DBCreate('ecommerce_cupom', $data);
    if(!$query) { Redireciona('?AdicionarCupom&erro'); }else{
      Redireciona('?ListarCupons&sucesso');
    }
  }

  if(isset($_GET['DeletarCupom'])){
    $id =  get('DeletarCupom');
    $query = DBDelete('ecommerce_cupom',"id = {$id}");
    if(!$query) { Redireciona('?AdicionarCupom&erro'); }else{
      Redireciona('?ListarCupons&sucesso');
    }
  }
if(isset($_GET['EditCupom'])){

if(isset($_POST['produtos'])){
        $resources = array_combine(array_keys($_POST['produtos']), array_map(function ($id) {
        return compact('id');
        }, $_POST['produtos']));
        $_POST['produto'] =    json_encode($resources, JSON_FORCE_OBJECT); 
  }
if(isset($_POST['ex_produtos'])){
        $resources1 = array_combine(array_keys($_POST['ex_produtos']), array_map(function ($id) {
        return compact('id');
        }, $_POST['ex_produtos']));
        $_POST['ex_produto'] =    json_encode($resources1, JSON_FORCE_OBJECT);
}
if(isset($_POST['categorias'])){    
        $resources2 = array_combine(array_keys($_POST['categorias']), array_map(function ($id) {
        return compact('id');
        }, $_POST['categorias']));
        $_POST['categoria'] =    json_encode($resources2, JSON_FORCE_OBJECT);
}
if(isset($_POST['ex_categorias'])){ 
        $resources3 = array_combine(array_keys($_POST['ex_categorias']), array_map(function ($id) {
        return compact('id');
        }, $_POST['ex_categorias']));
        $_POST['ex_categoria'] =    json_encode($resources3, JSON_FORCE_OBJECT);
} 
 
   

    
  $id =  get('EditCupom');
  $data = array(
    'codigo'         => post('codigo'),
    'descricao'      => post('descricao'),
    'tipo'           => post('tipo'),
    'valor'          => post('valor'),   
    'frete'          => post('frete'),   
    'produtos'       =>  $_POST['produto'],   
    'ex_produtos'    =>  $_POST['ex_produto'],   
    'categorias'     =>  $_POST['categoria'] ,   
    'ex_categorias'  =>  $_POST['ex_categoria'],   
    'limite_cupom'   => post('limite_cupom'),   
    'gasto_mi'       => post('gasto_mi'),   
    'gasto_ma'       => post('gasto_ma'),   
    'uso'            => post('uso'),   
    'ex_oferta'      => post('ex_oferta'),   
    'emails'         => post('emails'),   
    'limite_cliente' => post('limite_cliente'),     
    'data'           => post('data')  
  );
  $query = DBUpdate('ecommerce_cupom', $data, "id = {$id}");

    if(!$query) { Redireciona('?AdicionarCupom&erro'); }else{
      Redireciona('?ListarCupons&sucesso');
    }
  }