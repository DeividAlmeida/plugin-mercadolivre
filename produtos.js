var myHeaders = new Headers();
myHeaders.append("Authorization", "Bearer APP_USR-2441586670587751-081017-411c44aa4167fac676d8187f30420e3c-177309895");
myHeaders.append("Content-Type", "application/json");
myHeaders.append("Cookie", "_d2id=158348cf-9209-4017-aa35-0ee8cb0ab3a1-n");

var raw = "{\r\n    \"title\": \"Item de teste - Não Comprar\",\r\n    \"category_id\": \"MLB1051\",\r\n    \"price\": 10,\r\n    \"currency_id\": \"BRL\",\r\n    \"available_quantity\": 1,\r\n    \"listing_type_id\": \"free\",\r\n    \"condition\": \"new\",\r\n    \"pictures\": [\r\n         {\r\n    \"source\": \"https://www.motorino.com.br/site/wp-content/uploads/2018/01/produto_de_teste_amarelo_4_2_20171020224326-400x400.jpg\"}\r\n\r\n    ],\r\n}";

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("https://api.mercadolibre.com/items", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));