<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Copa do Mundo 2026</title>
	<link rel="icon" type="image/icon" href="img/favicon.png">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark navbar-arcade sticky-top">
		<div class="container">
			<a class="navbar-brand arcade-title flicker" href="index.php">ARCADE GIRL'S</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link active" href="index.php">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="incluir.php">Incluir</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<main class="container my-5">
		<div class="row mb-4">
			<div class="col-12">
				<h1 class="arcade-title text-center">Coleção de Jogos</h1>
			</div>
		</div>

		<header class="mb-4">
			<div class="row">
				<div class="col-12">
					<form action="#" method="post" class="mb-3">
						<div class="input-group">
							<input type="search" class="form-control arcade-input"
								placeholder="Digite o nome do jogo..." id="busca" name="filtro">
							<button class="btn btn-arcade" type="submit">Pesquisar</button>
						</div>
					</form>
				</div>
			</div>
		</header>

		<?php
		try {
			include "conexao.php";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$filtro = $conexao->real_escape_string($_POST["filtro"]);
				$sql = "SELECT * FROM jogos WHERE nome LIKE '%$filtro%' ORDER BY nome";
			} else {
				$sql = "SELECT * FROM jogos ORDER BY nome";
			}

			$query = $conexao->query($sql);

			if ($query && $query->num_rows > 0) {
				echo "<div class=\"table-responsive\">\n";
				echo "    <table class=\"table table-arcade table-hover\">\n";
				echo "        <thead>\n";
				echo "            <tr>\n";
				echo "                <th width=\"50px\">ID</th>\n";
				echo "                <th width=\"250px\">Jogo</th>\n";
				echo "                <th width=\"200px\">Plataforma</th>\n";
				echo "                <th width=\"120px\">Lançamento</th>\n";
				echo "                <th width=\"100px\">Imagem</th>\n";
				echo "                <th width=\"300px\" class=\"text-end\">Ações</th>\n";
				echo "            </tr>\n";
				echo "        </thead>\n";
				echo "        <tbody>\n";

				while ($dados = mysqli_fetch_array($query)) {
					$id_raw = $dados['id'];
					$id = base64_encode($id_raw);
					$id_attr = htmlspecialchars($id, ENT_QUOTES);
					$foto = !empty($dados['foto']) ? $dados['foto'] : "SemImagem.png";
					$nome = htmlspecialchars($dados['nome']);
					$plataforma = htmlspecialchars($dados['plataforma']);
					$data_display = "";
					if (!empty($dados['dataCad'])) {
						$dt = new DateTime($dados['dataCad'], new DateTimeZone("America/Sao_Paulo"));
						$data_display = $dt->format("d/m/Y");
					}

					echo "            <tr>\n";
					echo "                <td>" . intval($id_raw) . "</td>\n";
					echo "                <td>{$nome}</td>\n";
					echo "                <td>{$plataforma}</td>\n";
					echo "                <td>{$data_display}</td>\n";
					echo "                <td>\n";
					echo "                    <a href=\"verjogos.php?id={$id_attr}\">\n";
					echo "                        <img src=\"img/" . htmlspecialchars($foto, ENT_QUOTES) . "\" class=\"foto-tabela\" alt=\"{$nome}\">\n";
					echo "                    </a>\n";
					echo "                </td>\n";
					echo "                <td class=\"text-end\">\n";
					echo "                    <a href=\"verjogos.php?id={$id_attr}\" class=\"btn btn-arcade btn-sm\">Ver</a>\n";
					echo "                    <a href=\"editar.php?id={$id_attr}\" class=\"btn btn-arcade btn-sm\">Editar</a>\n";
					echo "                    <button type=\"button\" class=\"btn btn-arcade btn-sm\" data-bs-toggle=\"modal\" data-bs-target=\"#excluirModal\" data-produto=\"{$id_attr}\">Apagar</button>\n";
					echo "                </td>\n";
					echo "            </tr>\n";
				}

				echo "        </tbody>\n";
				echo "    </table>\n";
				echo "</div>\n";
			} else {
				echo "<div class=\"alert alert-info text-center\" role=\"alert\">\n";
				echo "    <h4>Nenhum jogo encontrado!</h4>\n";
				echo "</div>\n";
			}
		} catch (Exception $e) {
			echo "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n";
			echo "    <h4>Erro:</h4>\n";
			echo "    <p>{$e->getMessage()}</p>\n";
			echo "    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>\n";
			echo "</div>\n";
		}
		?>
	</main>

	<?php include "modal.php"; ?>

	<footer class="text-center py-4 mt-5">
        <p>COPA DO MUNDO 26'</p>
    </footer>

	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/dialogo.js"></script>
</body>

</html>
