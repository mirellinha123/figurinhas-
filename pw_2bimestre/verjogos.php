<?php
try {
    include "conexao.php";

    if (isset($_GET['id']) && is_numeric(base64_decode($_GET['id']))) {
        $id = (int) base64_decode($_GET['id']);
    } else {
        header("Location: index.php");
        exit();
    }

    $stmt = $conexao->prepare("SELECT * FROM jogos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) throw new Exception("Jogo não encontrado!");
    $dados = $res->fetch_assoc();

    $nome = $dados["nome"];
    $plataforma = $dados["plataforma"];
    $descricao = $dados["descricao"];
    $foto = !empty($dados['foto']) ? $dados['foto'] : "SemImagem.png";
    $data_display = "";
    if (!empty($dados['dataCad'])) {
        $dt = new DateTime($dados['dataCad'], new DateTimeZone("America/Sao_Paulo"));
        $data_display = $dt->format("d/m/Y");
    }
} catch (Exception $e) {
    $erro = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Jogo</title>
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
        </div>
    </nav>

    <main class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="arcade-title text-center mb-4">Detalhes do Jogo</h1>
            </div>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4>Erro:</h4>
                <p><?= htmlspecialchars($erro); ?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <a href="index.php" class="btn btn-arcade mt-3">Voltar</a>
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="arcade-card arcade-card-red">
                        <img src="img/<?php echo htmlspecialchars($foto); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($nome); ?>"
                             style="border: 2px solid var(--vermelho-st);">

                        <h3 class="text-verde-neon mb-3"><?php echo htmlspecialchars($nome); ?></h3>

                        <div class="mb-3">
                            <p class="text-bege mb-2"><strong>Plataforma:</strong> <?php echo htmlspecialchars($plataforma); ?></p>
                            <p class="text-bege mb-2"><strong>Data de Lançamento:</strong> <?php echo htmlspecialchars($data_display); ?></p>
                        </div>

                        <div class="mb-4">
                            <p class="text-bege"><strong>Descrição:</strong></p>
                            <p class="text-bege"><?php echo nl2br(htmlspecialchars($descricao)); ?></p>
                        </div>

                        <a href="index.php" class="btn btn-arcade w-100">Voltar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="text-center py-4 mt-5">
        <p>ARCADE GIRL'S 🎀</p>
    </footer>


    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>