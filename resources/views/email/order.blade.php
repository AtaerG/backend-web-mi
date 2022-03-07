<?php
date_default_timezone_set('Europe/Madrid');
?>
<style>
.button {
  background-color: blue;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}

.gradient{
    color: #2575fc;
}
.centered{
    text-align: center;
}
</style>
<div class="container centered" style="padding: 2rem; background: #f5f5f5;">
    <h1 class="gradient ">MUEBLES INTELIGENTES</h1>
    <div class="">¡Muchas gracias por el pedido!</div>
    <div class="">
      <h3 class="">Pedido № {{$order->id}}</h3>
      <p class="">Precio total del pedido: {{$order->total_price}}€ </p>
      <a href="#" class="button">MAS INFORMACION</a>
      <p style="margin-top: 1rem;">Por favor, guarda el numero del pedido</p>
    </div>
    <div style="margin-top: 2rem;">Fecha de procesamiento del pedido: {{date('d-m-y h:i:s')}}</div>
</div>
