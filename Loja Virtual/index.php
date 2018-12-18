<!DOCTYPE>
<!--
*   Data: 18/12/2018
*   Autor: Thelsandro Costa Antunes
*   Universidade do Estado do Amazonas
*   Engenharia de Computação
*   Projeto de Loja Virtual em PHP (Protótipo)
*   2ª Versão
-->

<html xmlns="http://www.w3.org/">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Loja Virtual</title>
<link rel="stylesheet" href="style/style1.css" type="text/css"  />
<style>
ul.pagination {
    text-align:center;
    color:#5DBCD2;
}
ul.pagination li {
    display:inline;
    padding:0 3px;
}
ul.pagination a {
    color:#5DBCD2;
    display:inline-block;
    padding:5px 10px;
    border:1px solid #5DBCD2;
    text-decoration:none;
}
ul.pagination a:hover,
ul.pagination a.current {
    background:#5DBCD2;
    color:#fff;
}

</style>

</head>
<body>

<div id="main2">
<div id="header"><img src="images/logo.png"></div>
<div id="manu">
  <ul>
    <li><a style="background:#5DBCD2; color:#fff;" href="index.php">HOME</a></li>
    <li><a href="pedido.php">MEUS PEDIDOS</a></li>
    <li><a href="atendimento.php">ATENDIMENTO</a></li>
    <li><a href="login/login.php">ENTRAR</a></li>
  </ul>
</div>


<div id="content">
  <?php
  require_once 'conexao/dbconfig.php';

  include_once('conexao/conexaox.php');
  include_once('funcao/funcaox.php');


  //**********************************************
    echo "No Record";
  //**********************************************
  ?>
  <br>
  <br>
</div>


<div id="footer">
<center>
  <p> Acesse Nossas Mídias Sociais </p>
  <p><img src="images/facebook.png"> <img src="images/twitter.png">  <img src="images/youtube.png"></p>
</center>
  <b>  Copyrights©2018 Protótipo de Loja Virtual - Thelsandro Antunes.</b>
</div>
</div>

</body>
</html>
