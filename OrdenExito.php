<?php
if (!isset($_REQUEST['id'])) {
  header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <title>Pedido Realizado!</title>
  <meta charset="utf-8">
  <style>
    .container {
      padding: 20px;
    }

    p {
      color: #34a853;
      font-size: 18px;
    }
  </style>
</head>
</head>

<body>
  <div class="container">
    <div class="panel panel-default">
      <div class="panel-heading">

        <ul class="nav nav-pills">
          <li role="presentation" class="active"><a href="index.php">Volver a la tienda</a></li>
          
        </ul>
      </div>

      <div class="panel-body">

        <h1>Estado del pedido</h1>
        <p>Tu pedido se ha realizado satisfactoreamente. El ID de tu pedido es <?php echo $_GET['id']; ?></p>
      </div>
      <div class="panel-footer">Desarrollado por Harol Criollo</div>
    </div>
  </div>
</body>

</html>