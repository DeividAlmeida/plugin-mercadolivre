<?php
    header('Access-Control-Allow-Origin: *');
  if(!checkPermission($PERMISSION, $_SERVER['SCRIPT_NAME'], 'produto', 'adicionar')){ Redireciona('./index.php'); }
?>
<?php $categorias = DBRead('ecommerce_categorias','*'); ?>
<?php $marcas = DBRead('ecommerce_marcas','*'); ?>
<?php $atributos = DBRead('ecommerce_atributos','*'); ?>
<?php $produtos = DBRead('ecommerce','*'); ?>
<?php $ML = DBRead('ecommerce_mercadolivre','*')[0]; ?>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<form method="post" action="?AddProduto" enctype="multipart/form-data" id="main">
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
            <select class="form-control produto-categorias" name="marcas[]" multiple="multiple" id="marca">
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
              <select class="form-control custom-select" v-model="ml">
              <option :value=false selected>Não</option>
              <option :value=true>Sim</option>
            </select>            
          </div>
        </div>        
    </div>
    <div class="row" v-if="ml">
        <div class="col-md-6">
            <div class="form-group">
                <label>Categoria:</label>
                <select name="categoria_ml" class="form-control custom-select" v-model="categoria" required>
                    <option value="MLB1055">Celulares e Smartphones</option>
                </select> 
            </div>
        </div>
        
        <!-- Celulares e Smartphones-->
        
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
                <label>Modelo:</label>
                <input  onchange="atributo()" class="form-control" name="MODEL" required>            
            </div>
        </div>
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
                <label>Dual Chip:</label>
                 <select  onchange="atributo()" class="form-control custom-select" name="IS_DUAL_SIM" required>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                </select> 
            </div>
        </div>
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
                <label>Operadora:</label>
                 <select  onchange="atributo()" class="form-control custom-select" name="CARRIER" required>
                    <option value="Desbloqueado">Desbloqueado</option>
                    <option value="Claro">Claro</option>
                    <option value="Nextel">Nextel</option>
                    <option value="TIM">TIM</option>
                    <option value="Oi">Oi</option>
                    <option value="Vivo">Vivo</option>
                </select> 
            </div>
        </div>
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
                <label>Cor:</label>
                 <select  onchange="atributo()" class="form-control custom-select" name="COLOR" required>
                    <option value="Verde-escuro">Verde-escuro</option>
                    <option value="Azul-turquesa">Azul-turquesa</option>
                    <option value="Índigo">Índigo</option>
                    <option value="Lavanda">Lavanda</option>
                    <option value="Rosa-chiclete">Rosa-chiclete</option>
                    <option value="Bordô">Bordô</option>
                    <option value="Nude">Nude</option>
                    <option value="Branco">Branco</option>
                    <option value="Azul-marinho">Azul-marinho</option>
                    <option value="Creme">Creme</option>
                    <option value="Rosa-pálido">Rosa-pálido</option>
                    <option value="Palha">Palha</option>
                    <option value="Laranja-claro">Laranja-claro</option>
                    <option value="Azul">Azul</option>
                    <option value="Rosa-claro">Rosa-claro</option>
                    <option value="Coral-claro">Coral-claro</option>
                    <option value="Coral">Coral</option>
                    <option value="Azul-celeste">Azul-celeste</option>
                    <option value="Azul-aço">Azul-aço</option>
                    <option value="Cáqui">Cáqui</option>
                    <option value="Bege">Bege</option>
                    <option value="Vermelho">Vermelho</option>
                    <option value="Terracota">Terracota</option>
                    <option value="Cinza">Cinza</option>
                    <option value="Violeta">Violeta</option>
                    <option value="Marrom-claro">Marrom-claro</option>
                    <option value="Preto">Preto</option>
                    <option value="Marrom-escuro">Marrom-escuro</option>
                    <option value="Prateado">Prateado</option>
                    <option value="Violeta-escuro">Violeta-escuro</option>
                    <option value="Rosa">Rosa</option>
                    <option value="Amarelo">Amarelo</option>
                    <option value="Dourado-escuro">Dourado-escuro</option>
                    <option value="Verde-claro">Verde-claro</option>
                    <option value="Laranja-escuro">Laranja-escuro</option>
                    <option value="Azul-petróleo">Azul-petróleo</option>
                    <option value="Cinza-escuro">Cinza-escuro</option>
                    <option value="Chocolate">Chocolate</option>
                    <option value="Verde">Verde</option>
                    <option value="Dourado">Dourado</option>
                    <option value="Laranja">Laranja</option>
                    <option value="Azul-escuro">Azul-escuro</option>
                    <option value="Ocre">Ocre</option>
                    <option value="Verde-musgo">Verde-musgo</option>
                    <option value="Marrom">Marrom</option>
                    <option value="Lilás">Lilás</option>
                    <option value="Fúcsia">Fúcsia</option>
                    <option value="Ciano">Ciano</option>
                    <option value="Azul-claro">Azul-claro</option>
                </select> 
            </div>
        </div>
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
              <label>Memória interna:</label>
                <div class=" input-group" >
                  <input onchange="atributo()" class="form-control" type="number" min="1" v-model="rom" required>
                  <input name="INTERNAL_MEMORY" type="hidden" :value="rom+' '+un_rom">
                  <div class="input-group-prepend">
                      <select onchange="atributo()" class="custom-select" v-model="un_rom" required>
                        <option value="kB">KB</option>
                        <option value="MB">MB</option>
                        <option value="GB">GB</option>
                        <option value="TB">TB</option>
                      </select>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
              <label>Memória RAM:</label>
                <div class=" input-group" >
                  <input onchange="atributo()" class="form-control" type="number" min="1" v-model="ram" required>
                  <input name="RAM" type="hidden" :value="ram+' '+un_ram">
                  <div class="input-group-prepend">
                      <select onchange="atributo()" class="custom-select" v-model="un_ram" required>
                        <option value="kB">KB</option>
                        <option value="MB">MB</option>
                        <option value="GB">GB</option>
                        <option value="TB">TB</option>
                      </select>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-3" v-if="categoria=='MLB1055'">
            <div class="form-group">
                <label>Código universal de produto (GTIN):</label>
                <input onchange="atributo()" class="form-control" name="GTIN" required>            
            </div>
        </div>
        <input name="atributos_ml" type="hidden">
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
<script>
    const vue = new Vue({
        el: '#main',
        data:{
            ml:false,
            categoria:null,
            un_rom:null,
            un_ram:null,
            rom:null,
            ram:null,
        }
    })
    function atributo(){
        var categoria = document.getElementsByName('categoria_ml')[0].value
        var e = document.getElementById("marca");
        var strUser = e.options[e.selectedIndex].text;
        var att
        switch(categoria){
            case 'MLB1055':
               att = '"attributes": ['+
                        '{'+
                           '"id": "MODEL",'+
                            '"value_name": "'+String(document.getElementsByName('MODEL')[0].value)+'"'+
                        '},'+
                        '{'+
                           ' "id": "IS_DUAL_SIM",'+
                           '"value_name": "'+(document.getElementsByName('IS_DUAL_SIM')[0].value)+'"'+
                       ' },'+
                       ' {'+
                            '"id": "COLOR",'+
                           '"value_name": "'+String(document.getElementsByName('COLOR')[0].value)+'"'+
                        '},'+
                       ' {'+
                           '"id": "INTERNAL_MEMORY",'+
                            '"value_name": "'+String(document.getElementsByName('INTERNAL_MEMORY')[0].value)+'"'+
                      ' },'+
                        '{'+
                            '"id": "BRAND",'+
                            '"value_name": "'+strUser+'"'+
                       ' },'+
                        '{'+
                            '"id": "RAM",'+
                            '"value_name": "'+String(document.getElementsByName('RAM')[0].value)+'"'+
                        '},'+
                        '{'+
                            '"id": "CARRIER",'+
                            '"value_name": "'+String(document.getElementsByName('CARRIER')[0].value)+'"'+
                        '},'+
                        '{'+
                            '"id": "GTIN",'+
                            '"value_name": "'+String(document.getElementsByName('GTIN')[0].value)+'"'+
                        '}'+
                ']'
               document.getElementsByName('atributos_ml')[0].value = att
            break;
        }
    }
</script>