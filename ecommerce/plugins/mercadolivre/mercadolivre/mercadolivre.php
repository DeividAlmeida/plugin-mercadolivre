<?php 
$read =  DBRead('ecommerce_mercadolivre','*')[0];
$uri = ConfigPainel('base_url').'ecommerce.php?MercadoLivre';
$produtos   = DBRead('ecommerce','*');
?>
<style>
    .select2-container--default .select2-search--dropdown::before{
        content:""!important;
    }
</style>
<div class="card">    
    <div class="card-header white">
      <strong>Configurar integração com o Mercado Livre</strong>
    </div>

    <div class="card-body">
        <div class="row m-3">
            <button id="btnCopiarCodSite1" class="btn btn-primary btn-xs m-1" onclick="CopiadoCodSite(1)" data-clipboard-text='<?php echo $uri; ?>' type="button">
                <i class="icon icon-code"></i> Copiar URI de redirect
            </button>                
        </div>
        <form action="?Ml_Config" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usuario">App ID:</label>
                        <input name="appid"  class="form-control" value="<? echo $read['appid'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usuario">Client Secret:</label>
                        <input name="clientsecret"  class="form-control" value="<? echo $read['clientsecret'] ?>">
                    </div>
                </div>
            </div>
            <div class="card-footer white">
                <button style="margin-bottom: 7px;" class="btn btn-primary float-right" type="submit"><i class="icon icon-save" aria-hidden="true"></i> Salvar</button>
            </div>
        </form>
        <br>
        <hr>
        <form action="?integrar" method="post">
            <strong>Integrar anuncio já existente</strong>
            <br>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                      <label>Produtos: </label>
                      <select class="form-control produto-categorias" name="produto"  required>
                        <?php foreach($produtos as $produto){ ?>
                          <option value="<?php echo $produto['id']; ?>"><?php echo $produto['nome']; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                      <label>Id do anúncio : </label>
                      <input class="form-control" name="cod" required step="0" type="number" >
                  </div>
                    <button style="margin-bottom: 7px;" class="btn btn-primary float-right" type="submit"><i class="icon icon-refresh" aria-hidden="true"></i> Integrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<? if(isset($_GET['code'])){?>
<script>
swal({
  title: "Aguarde um instante!",
  text: "Estamos gerando o token de validação !",
  icon: "info",
});
let id  = "<? echo $read['appid'] ?>"
let secret = "<? echo $read['clientsecret'] ?>"
let code = "<? echo $_GET['code'] ?>"
var myHeaders = new Headers();
myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
myHeaders.append("Cookie", "_d2id=158348cf-9209-4017-aa35-0ee8cb0ab3a1-n");

var urlencoded = new URLSearchParams();
urlencoded.append("grant_type", "authorization_code");
urlencoded.append("client_id", id);
urlencoded.append("client_secret", secret);
urlencoded.append("redirect_uri", "<? echo $uri ?>");
urlencoded.append("code", code);

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: urlencoded,
  redirect: 'follow'
};

fetch("https://api.mercadolibre.com/oauth/token", requestOptions)
  .then(response => response.json())
  .then(result => {
      urlencoded.set("grant_type", "refresh_token");
      urlencoded.delete("redirect_uri")
      urlencoded.delete("code")
      urlencoded.append("refresh_token", result.refresh_token)
      fetch("https://api.mercadolibre.com/oauth/token", requestOptions)
      .then(req => req.json())
      .then(res =>{
            if(res.access_token != void(0)){
                fetch('?Ml_Token='+res.access_token)
                .then(ver=>{
                    swal({
                        title: "Gerado!",
                        text: "Token gerado e salvo com sucesso !",
                        icon: "success",
                    }) 
                }) 
            }else{
                swal({
                    title: "Erro!",
                    text: res.message,
                    icon: "error",
                });
            }
        })
    })
  .catch(error => console.log('error', error));
</script>
<? }

