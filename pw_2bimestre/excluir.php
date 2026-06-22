<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Jogo</title>
    <link rel="icon" type="image/icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="container my-5">
        <?php
        try {
            include "conexao.php";

            if (isset($_GET['id']) && is_numeric(base64_decode($_GET['id']))) {
                $id = base64_decode($_GET['id']);
            } else {
                header("Location: index.php");
                exit();
            }

            $sql = "delete from jogos where id=$id";
            $resultado = $conexao->query($sql);

            echo "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n";
            echo "    <h4>Sucesso!</h4>\n";
            echo "    <p>Jogo excluído com sucesso!</p>\n";
            echo "    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>\n";
            echo "    <a href=\"index.php\" class=\"btn btn-arcade mt-3\">Voltar</a>\n";
            echo "</div>\n";
        } catch (Exception $e) {
            echo "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n";
            echo "    <h4>Erro:</h4>\n";
            echo "    <p>{$e->getMessage()}</p>\n";
            echo "    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>\n";
            echo "    <a href=\"index.php\" class=\"btn btn-arcade mt-3\">Voltar</a>\n";
            echo "</div>\n";
        }
        ?>
    </main>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>