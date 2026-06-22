<?php
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	$host = "localhost";
	$user = "root";
	$pass = "";
	$banco = "jogos";

	try {
		$conexao = new mysqli($host, $user, $pass, $banco);
		$conexao->set_charset("utf8");
	} catch (Exception $e) {
		throw new Exception("Problemas com a 
				conexão do Banco de Dados:<br> {$e->getMessage()}");
	}
?>