var myHeaders = new Headers();
myHeaders.append("Cookie", "_d2id=158348cf-9209-4017-aa35-0ee8cb0ab3a1-n");

var requestOptions = {
  method: 'GET',
  headers: myHeaders,
  redirect: 'follow'
};

fetch("https://api.mercadolibre.com/sites/MLB/categories", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));