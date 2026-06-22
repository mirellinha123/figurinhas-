<?php

try {
    include "conexao.php"; 

    if (isset($_GET['id']) && is_numeric(base64_decode($_GET['id']))) {
        $id = (int) base64_decode($_GET['id']);
    } else {
        throw new Exception("Jogo não existe!");
    }

    $nome = $plataforma = $descricao = $dataCad = $foto = "";
    $mensagem_sucesso = "";

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $stmt = $conexao->prepare("SELECT * FROM jogos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0)
            throw new Exception("Jogo não encontrado!");
        $dados = $res->fetch_assoc();

        $nome = $dados['nome'];
        $plataforma = $dados['plataforma'];
        $descricao = $dados['descricao'];
        $foto = $dados['foto'];

        if (!empty($dados['dataCad'])) {
            $dt = new DateTime($dados['dataCad'], new DateTimeZone("America/Sao_Paulo"));
            $dataCad = $dt->format("Y-m-d");
        }
    } else {
        $nome = trim($_POST['nome'] ?? '');
        $plataforma = trim($_POST['plataforma'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $data = $_POST['data'] ?? '';

        if ($nome === '' || $plataforma === '' || $descricao === '' || $data === '') {
            throw new Exception("Preencha todos os campos obrigatórios.");
        }

        $dataCad_db = $data . " 00:00:00";
        $dtObj = DateTime::createFromFormat('Y-m-d H:i:s', $dataCad_db, new DateTimeZone("America/Sao_Paulo"));
        if (!$dtObj)
            throw new Exception("Data inválida.");
        $dataCad_db = $dtObj->format('Y-m-d H:i:s');

        $stmt = $conexao->prepare("SELECT foto FROM jogos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0)
            throw new Exception("Jogo não encontrado.");
        $row = $res->fetch_assoc();
        $foto_atual = $row['foto'];

        $nova_foto_nome = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK)
                throw new Exception("Erro ao enviar a imagem.");
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $orig_name = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_ext))
                throw new Exception("Formato de imagem não permitido. Use JPG, PNG ou GIF.");

            $nova_foto_nome = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $orig_name);
            $dest = __DIR__ . '/img/' . $nova_foto_nome;
            if (!is_dir(dirname($dest)))
                @mkdir(dirname($dest), 0755, true);
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest))
                throw new Exception("Falha ao salvar a imagem no servidor.");

            if (!empty($foto_atual) && $foto_atual !== 'SemImagem.png') {
                $caminho_antigo = __DIR__ . '/img/' . $foto_atual;
                if (file_exists($caminho_antigo))
                    @unlink($caminho_antigo);
            }
        }

        if ($nova_foto_nome) {
            $stmt = $conexao->prepare("UPDATE jogos SET nome = ?, plataforma = ?, descricao = ?, dataCad = ?, foto = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $nome, $plataforma, $descricao, $dataCad_db, $nova_foto_nome, $id);
            $foto = $nova_foto_nome;
        } else {
            $stmt = $conexao->prepare("UPDATE jogos SET nome = ?, plataforma = ?, descricao = ?, dataCad = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nome, $plataforma, $descricao, $dataCad_db, $id);
            $foto = $foto_atual;
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar o jogo: " . $conexao->error);
        }

        $dataCad = (new DateTime($dataCad_db, new DateTimeZone("America/Sao_Paulo")))->format("Y-m-d");
        $mensagem_sucesso = "Jogo atualizado com sucesso!";
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
    <title>Editar Jogo</title>
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
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <h1 class="arcade-title text-center mb-4">Editar Jogo</h1>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h4>Sucesso!</h4>
                        <p><?= htmlspecialchars($mensagem_sucesso); ?></p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <a href="index.php" class="btn btn-arcade mt-3">Voltar</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h4>Erro:</h4>
                        <p><?= htmlspecialchars($erro); ?></p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <a href="index.php" class="btn btn-arcade mt-3">Voltar</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($nome) || $_SERVER["REQUEST_METHOD"] === "GET"): ?>
                    <?php $id_encoded = base64_encode($id); ?>
                    <form id="formJogo" action="editar.php?id=<?= $id_encoded; ?>" method="post"
                        enctype="multipart/form-data" class="arcade-card arcade-card-red">
                        <div class="mb-3">
                            <label for="nome" class="form-label text-verde-neon">Nome do Jogo:</label>
                            <input type="text" class="form-control arcade-input" id="nome" name="nome"
                                value="<?= htmlspecialchars($nome); ?>" maxlength="50" required>
                        </div>

                        <div class="mb-3">
                            <label for="plataforma" class="form-label text-verde-neon">Plataforma:</label>
                            <input type="text" class="form-control arcade-input" id="plataforma" name="plataforma"
                                value="<?= htmlspecialchars($plataforma); ?>" maxlength="80" required>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label text-verde-neon">Descrição:</label>
                            <textarea class="form-control arcade-input" id="descricao" name="descricao" rows="4"
                                required><?= htmlspecialchars($descricao); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="data" class="form-label text-verde-neon">Data de Lançamento:</label>
                            <input type="date" class="form-control arcade-input" id="data" name="data"
                                value="<?= htmlspecialchars($dataCad); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label text-verde-neon">Imagem (opcional — altere para
                                atualizar):</label>
                            <input type="file" class="form-control arcade-input" id="foto" name="foto" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <p class="text-verde-neon">Pré-visualização:</p>
                            <img src="img/<?= htmlspecialchars($foto ?: 'SemImagem.png'); ?>" id="preview"
                                class="img-fluid img-thumbnail" alt="imagem"
                                style="border: 2px solid var(--verde-neon); width: 200px;">
                        </div>

                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-arcade flex-grow-1 text-uppercase">Cancelar</a>
                            <button type="button" class="btn btn-arcade-green flex-grow-1 text-uppercase"
                                id="btnLimpar">Limpar</button>
                            <button type="submit" class="btn btn-arcade flex-grow-1 text-uppercase">Atualizar</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5">
        <p>ARCADE GIRL'S 🎀</p>
    </footer>


    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnLimpar').addEventListener('click', function () {
            document.getElementById('formJogo').reset();
        });

        const inputFoto = document.getElementById('foto');
        const preview = document.getElementById('preview');
        if (inputFoto) {
            inputFoto.addEventListener('change', function (e) {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function (ev) {
                    preview.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
</body>

</html>