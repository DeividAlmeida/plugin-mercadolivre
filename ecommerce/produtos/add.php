<?php
    header('Access-Control-Allow-Origin: *');
  if(!checkPermission($PERMISSION, $_SERVER['SCRIPT_NAME'], 'produto', 'adicionar')){ Redireciona('./index.php'); }
?>
<?php $categorias = DBRead('ecommerce_categorias','*'); ?>
<?php $marcas = DBRead('ecommerce_marcas','*'); ?>
<?php $atributos = DBRead('ecommerce_atributos','*'); ?>
<?php $produtos = DBRead('ecommerce','*'); ?>
<?php $ML = DBRead('ecommerce_mercadolivre','*')[0]; ?>
<form method="post" action="?AddProduto" enctype="multipart/form-data">
  <div class="card">
    <div class="card-header  white">
      <strong>Cadastrar Produto</strong>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <!-- `nome` varchar(255) NOT NULL -->
          <div class="form-group">
            <label>Nome: </label>
            <input class="form-control produto-nome" name="nome" required>
          </div>

          <!-- `descricao` text DEFAULT NULL -->
          <div class="form-group">
            <label>Descrição: </label>
            <textarea class="form-control tinymce" name="descricao"></textarea>
          </div>

    </div>
        <div class="col-md-6">

          <!-- `resumo` text DEFAULT NULL -->
          <div class="form-group">
            <label>Resumo: </label>
            <textarea class="form-control" name="resumo"></textarea>
          </div>

          <!-- `codigo` varchar(255) NOT NULL -->
          <div class="form-group">
            <label>Código do Produto: </label>
            <input class="form-control" name="codigo" required>
          </div>

          <!-- `url` varchar(255) NOT NULL -->
          <div class="form-group">
            <label>URL amigável: </label>
            <input class="form-control produto-url" name="url" required>
          </div>

          <!-- `categorias` -->
          <div class="form-group">
            <label>Categorias: </label>
            <select class="form-control produto-categorias" name="categorias[]" multiple="multiple" required>
              <?php foreach($categorias as $categoria){ ?>
                <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nome']; ?></option>
              <?php } ?>
            </select>
          </div>

          <!-- `marcas` -->
          <div class="form-group">
            <label>Marca: </label>
            <select class="form-control produto-categorias" name="marcas[]" multiple="multiple">
              <?php foreach($marcas as $marcas){ ?>
                <option value="<?php echo $marcas['id']; ?>"><?php echo $marcas['nome']; ?></option>
              <?php } ?>
            </select>
          </div>

          <!-- `atributos` -->
          <div class="form-group">            
            <a id="produto-add-atb" class="btn btn-primary" data-target="#Modal" data-toggle="modal" onclick="showDetails(this)">Adcionar atributo</a>             
          </div>
            <!-- `preco` decimal(10,2) NOT NULL -->
          <div class="form-group">
            <label>Preço: </label>
            <input class="form-control" name="preco" step="0.01" type="number" min="0" required>
          </div>

          <div class="form-group">
            <label>Produtos Relacionados: </label>
            <select class="form-control produto-categorias" name="produtos_relacionados[]" multiple="multiple">
              <?php foreach($produtos as $produtos){ ?>
                <option value="<?php echo $produtos['id']; ?>"><?php echo $produtos['nome']; ?></option>
              <?php } ?>
            </select>
          </div>

          <!-- `estoque` int(11) DEFAULT NULL-->
          <div class="form-group hidden">
            <label>Estoque:</label>            
              <input class="form-control" name="estoque" type="number" >            
          </div>

          <!-- `estoque` int(11) DEFAULT NULL-->
          <div class="form-group">
            <label>Diminuir estoque:</label>            
              <select name="diminuir_est" required class="form-control custom-select">
              <option value="sim" selected>Sim</option>
              <option value="não">Não</option>
            </select>            
          </div> 
          
          <!-- `estoque` int(11) DEFAULT NULL-->
          <div class="form-group">
            <label>Publicar no Mercado Livre:</label>            
              <select class="form-control custom-select" onchange="sugestao(this)">
              <option value="0" selected>Não</option>
              <option value="1">Sim</option>
            </select>            
          </div> 
           <div class="form-group ">
            <label>Categoria ML:</label>            
              <input class="form-control" id="categoriaml" disabled codigo="">            
          </div>
          <script>
              function sugestao(i){
                        let nome = document.getElementsByName('nome')[0].value
                        let cod = document.getElements 
                    if(i.value == 1){
                        fetch('ecommerce/plugins/mercadolivre/mercadolivre/apis/sugestao.php?nome='+nome)
                        .then(a=>a.json())
                        .then(b=>{
                            console.log(b)
                        })
                    }
                }
          </script>
        </div>        
    </div>
    <div class="row">
        <div class="col-md-3" >
                <!-- `peso` varchar(255) DEFAULT NULL-->
            <div class="form-group">
                <label>Peso: <i class="icon icon-question-circle tooltips" data-tooltip="Encomenda com embalagem." ></i></label>
                <input class="form-control" name="peso" placeholder="Unidade de media kg" required>          
            </div>
        </div>
        <div class="col-md-3">
            <!-- `comprimento` varchar(255) DEFAULT NULL-->
            <div class="form-group">
                <label>Comprimento: <i class="icon icon-question-circle tooltips" data-tooltip="Encomenda com embalagem." ></i></label>
                <input class="form-control" name="comprimento" placeholder="Unidade de media cm" required>          
            </div>
        </div>
        <div class="col-md-3">
           <!-- `altura` varchar(255) DEFAULT NULL-->
           <div class="form-group">
                <label>Altura: <i class="icon icon-question-circle tooltips" data-tooltip="Encomenda com embalagem." ></i></label>
                <input class="form-control" name="altura" placeholder="Unidade de media cm" required>          
            </div>
        </div>
        <div class="col-md-3">
            <!-- `largura` varchar(255) DEFAULT NULL-->
           <div class="form-group">
                <label>Largura: <i class="icon icon-question-circle tooltips" data-tooltip="Encomenda com embalagem." ></i></label>
                <input class="form-control" name="largura" placeholder="Unidade de media cm" required>          
            </div> 
        </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <!-- `palavras_chave` text NOT NULL -->
          <div class="form-group">
            <label>Palavras Chave: </label>
            <textarea class="form-control" name="palavras_chave"></textarea>
          </div>
          <!-- `etiqueta` varchar(255) DEFAULT NULL -->
            <div class="form-group">
              <label>Etiqueta: </label>
              <input class="form-control" name="etiqueta" value="">
            </div>

            <!-- `etiqueta_cor` varchar(255) DEFAULT NULL -->
            <div class="form-group">
              <label for="name">Cor da Etiqueta: </label>
              <div class="color-picker input-group colorpicker-element focused">
                <input type="text" class="form-control" name="etiqueta_cor" value="">
                <span class="input-group-append">
                  <span class="input-group-text add-on white">
                    <i class="circle"></i>
                  </span>
                </span>
              </div>
            </div>  

          <!-- `btn_texto` varchar(255) DEFAULT NULL -->
          <div class="form-group">            
            <input class="form-control" name="btn_texto" type="hidden" value="Comprar">
          </div>

          <!-- `ordem_manual` int(11) -->
          <div class="form-group">
            <label>Ordem Manual: </label>
            <input class="form-control" name="ordem_manual" type="number" value="1">
          </div>
        </div>
        
        <div class="col-md-12">
          <hr/>
          <a id="produto-add-foto" class="btn btn-primary">Adicionar foto</a>

          <table id="foto-wrapper" class="table mt-3 table-striped">
            <thead>
              <tr>
                <th>Arquivo</th>
                <th>Capa</th>
                <th width="53px">Ações</th>
              </tr>
            </thead>

            <tbody></tbody>
          </table>

          <button class="btnSubmit btn btn-primary float-right" type="submit">Cadastrar</button>
        </div>
      </div>
    </div>
   </div>
  </div>
</form>

<div class="modal fade"  id="Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div  class="modal-dialog" role="document">
    <div  class="modal-content">
      <div class="modal-content b-0">
          <div class="modal-header r-0 bg-primary">
            <h6 class="modal-title text-white" id="exampleModalLabel">Adicionar Atibutos no Produto</h6>
            <a href="#" data-dismiss="modal" aria-label="Close" class="paper-nav-toggle paper-nav-white active"><i></i></a>
          </div>
          <div class="modal-body no-b" id="no-b">
          </div>
          <div class="modal-body no-b" id="no-c">           
          </div>
          <div class="modal-body no-b" id="no-d">           
          </div>          
        </div>
    </div>
  </div>
</div>
<script type="text/javascript"> function showDetails(z){$("#no-b").load('<?php echo ConfigPainel('base_url'); ?>/ecommerce/produtos/processa_attributos.php?radar=0');}</script> 