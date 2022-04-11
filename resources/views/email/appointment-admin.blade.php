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
    <div class="">¡Usuario ha pedido contigo la cita!</div>
    <div class="">
      <h3 class="">Numero de cita № {{$appointment->id}}</h3>
      <p class="">Dia de la cita: {{$appointment->date}} </p>
      <p class="">Hora de la cita: {{$appointment->time}} </p>
      <p style="margin-top: 1rem;">Por favor, no olvida sobre la cita!</p>
    </div>
    <div style="margin-top: 2rem;">Fecha de procesamiento de la cita: {{date('d-m-y h:i:s')}}</div>
</div>
