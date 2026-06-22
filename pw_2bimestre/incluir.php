<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incluir Jogo</title>
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="incluir.php">Incluir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <h1 class="arcade-title text-center mb-4">Adicionar <br> Novo Jogo</h1>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    try {
                        include "conexao.php"; // espera $conexao (mysqli)

                        // campos do formulário (ajustados ao esquema do banco)
                        $nome = trim($_POST['nome'] ?? '');
                        $plataforma = trim($_POST['plataforma'] ?? '');
                        $descricao = trim($_POST['descricao'] ?? '');
                        $data = $_POST['data'] ?? ''; // input type="date" (YYYY-MM-DD)
                        
                        // validação básica
                        if ($nome === '' || $plataforma === '' || $descricao === '' || $data === '') {
                            throw new Exception("Preencha todos os campos obrigatórios.");
                        }

                        // converter para DATETIME (adiciona hora 00:00:00)
                        $dataCad = $data . " 00:00:00";
                        // opcional: validar formato
                        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $dataCad);
                        if (!$dt) {
                            throw new Exception("Data inválida.");
                        }

                        // tratar upload de imagem (obrigatório conforme tabela)
                        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
                            throw new Exception("Envie uma imagem do jogo.");
                        }
                        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                            throw new Exception("Erro no envio da imagem.");
                        }

                        $allowed_ext = ['jpg','jpeg','png','gif'];
                        $orig_name = $_FILES['foto']['name'];
                        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                        if (!in_array($ext, $allowed_ext)) {
                            throw new Exception("Formato de imagem não permitido. Use JPG, PNG ou GIF.");
                        }

                        // gerar nome único para evitar conflitos
                        $safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $orig_name);
                        $target_dir = __DIR__ . '/img/';
                        if (!is_dir($target_dir)) {
                            // tenta criar a pasta se não existir
                            @mkdir($target_dir, 0755, true);
                        }
                        $target_file = $target_dir . $safe_name;

                        if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                            throw new Exception("Erro ao salvar a imagem no servidor.");
                        }

                        // inserir no banco usando prepared statement
                        $stmt = $conexao->prepare("INSERT INTO jogos (nome, plataforma, descricao, dataCad, foto) VALUES (?, ?, ?, ?, ?)");
                        if (!$stmt) {
                            throw new Exception("Erro ao preparar consulta: " . $conexao->error);
                        }
                        $stmt->bind_param("sssss", $nome, $plataforma, $descricao, $dataCad, $safe_name);
                        $ok = $stmt->execute();
                        if (!$ok) {
                            // se falhar, tenta remover a imagem salva
                            if (file_exists($target_file)) @unlink($target_file);
                            throw new Exception("Erro ao inserir no banco: " . $stmt->error);
                        }

                        echo "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n";
                        echo "    <h4>Sucesso!</h4>\n";
                        echo "    <p>Jogo adicionado com sucesso!</p>\n";
                        echo "    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>\n";
                        echo "    <a href=\"index.php\" class=\"btn btn-arcade mt-3\">Voltar</a>\n";
                        echo "</div>\n";

                    } catch (Exception $e) {
                        echo "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n";
                        echo "    <h4>Erro:</h4>\n";
                        echo "    <p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
                        echo "    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>\n";
                        echo "</div>\n";
                    }
                }
                ?>

                <form name="produto" action="incluir.php" method="post" enctype="multipart/form-data"
                    class="arcade-card arcade-card-red">
                    <div class="mb-3">
                        <label for="nome" class="form-label text-verde-neon">Nome do Jogo:</label>
                        <input type="text" class="form-control arcade-input" id="nome" name="nome" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="plataforma" class="form-label text-verde-neon">Plataforma:</label>
                        <input type="text" class="form-control arcade-input" id="plataforma" name="plataforma" maxlength="80" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label text-verde-neon">Descrição:</label>
                        <textarea class="form-control arcade-input" id="descricao" name="descricao" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="data" class="form-label text-verde-neon">Data de Lançamento:</label>
                        <input type="date" class="form-control arcade-input" id="data" name="data" required>
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label text-verde-neon">Imagem:</label>
                        <input type="file" class="form-control arcade-input" id="imagemnova" name="foto" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <p class="text-verde-neon">Pré-visualização:</p>
                        <img src="img/SemImagem.png" id="preview" class="img-fluid img-thumbnail" alt="sem imagem"
                            style="border: 2px solid var(--verde-neon); width: 200px;">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-arcade flex-grow-1 text-uppercase">Adicionar</button>
                        <button type="reset" class="btn btn-arcade-green flex-grow-1 text-uppercase">Limpar</button>
                        <a href="index.php" class="btn btn-arcade flex-grow-1 text-uppercase">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5">
        <p>ARCADE GIRL'S 🎀</p>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('imagemnova').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>

</html>