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
    <div class="">¡Muchas gracias por crear la cuenta de usuario!</div>
    <a href="https://mueblesint.es/#/products" class="button">VER LOS PRODUCTOS DE MI</a>
    <div style="margin-top: 2rem;">Fecha de creacion de la cuenta de usuario: {{date('d-m-y h:i:s')}}</div>
</div>
