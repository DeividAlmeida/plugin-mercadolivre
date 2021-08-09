<script>
let id  = "2441586670587751"
let secret = "7eIfzSARzJVSiPe9aE1qRFiiCR9cQJKN"
let code = prompt("codigo");
var myHeaders = new Headers();
myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
myHeaders.append("Cookie", "_d2id=158348cf-9209-4017-aa35-0ee8cb0ab3a1-n");

var urlencoded = new URLSearchParams();
urlencoded.append("grant_type", "authorization_code");
urlencoded.append("client_id", id);
urlencoded.append("client_secret", secret);
urlencoded.append("redirect_uri", "https://www.templateswebacappella.com.br/loja-virtual-web-acappella/");
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
         alert(res.access_token) 
      })
  })
  .catch(error => console.log('error', error));
//auth.mercadolibre.com.ar/authorization?response_type=code&client_id=2441586670587751&state=ABC1234&redirect_uri=https://www.templateswebacappella.com.br/loja-virtual-web-acappella/
</script>