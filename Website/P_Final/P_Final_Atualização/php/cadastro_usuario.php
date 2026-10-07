<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: ../index.php');
    exit;
}

$erro = '';
$valores = [
    'nome' => '',
    'cpf' => '',
    'datanascimento' => '',
    'telefone' => '',
    'email' => '',
    'deficiencia' => 'nao',
    'qual_deficiencia' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($valores) as $campo) {
        $valores[$campo] = trim($_POST[$campo] ?? '');
    }

    $nome = $valores['nome'];
    $cpf = preg_replace('/\D/', '', $valores['cpf']);
    $dataNascimento = $valores['datanascimento'];
    $telefone = $valores['telefone'];
    $email = $valores['email'];
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmarSenha'] ?? '';
    $deficiencia = $valores['deficiencia'];

    $dataValida = DateTime::createFromFormat('!Y-m-d', $dataNascimento);
    $cpfValido = strlen($cpf) === 11 && !preg_match('/^(\d)\1{10}$/', $cpf);
    if ($cpfValido) {
        $somaPrimeiroDigito = 0;
        for ($i = 0; $i < 9; $i++) {
            $somaPrimeiroDigito += (int) $cpf[$i] * (10 - $i);
        }
        $primeiroDigito = ($somaPrimeiroDigito * 10) % 11;
        $primeiroDigito = $primeiroDigito === 10 ? 0 : $primeiroDigito;

        $somaSegundoDigito = 0;
        for ($i = 0; $i < 10; $i++) {
            $somaSegundoDigito += (int) $cpf[$i] * (11 - $i);
        }
        $segundoDigito = ($somaSegundoDigito * 10) % 11;
        $segundoDigito = $segundoDigito === 10 ? 0 : $segundoDigito;
        $cpfValido = (int) $cpf[9] === $primeiroDigito && (int) $cpf[10] === $segundoDigito;
    }

    if (
        mb_strlen($nome) < 2 ||
        mb_strlen($nome) > 25 ||
        !$cpfValido ||
        !$dataValida ||
        $dataValida->format('Y-m-d') !== $dataNascimento ||
        $dataNascimento > date('Y-m-d') ||
        strlen(preg_replace('/\D/', '', $telefone)) < 10 ||
        strlen($telefone) > 30 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 40 ||
        strlen($senha) < 6 ||
        $senha !== $confirmarSenha ||
        !in_array($deficiencia, ['sim', 'nao'], true) ||
        !isset($_POST['termos'])
    ) {
        $erro = 'invalid_data';
    } else {
        $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        $tiposDeficiencia = [
            'deficiencia_visual',
            'deficiencia_auditiva',
            'deficiencia_motora',
            'deficiencia_cardiaca',
            'deficiencia_intelectual',
            'deficiencia_neurologica',
            'deficiencia_psiquica',
            'deficiencia_outros'
        ];
        $acessibilidade = [];
        foreach ($tiposDeficiencia as $tipo) {
            $acessibilidade[] = $deficiencia === 'sim' && isset($_POST[$tipo]) ? 1 : 0;
        }

        require_once __DIR__ . '/conexao.php';

        $check = $conn->prepare('SELECT 1 FROM cadastro_usuario WHERE email = ? OR cpf = ? LIMIT 1');
        $check->bind_param('ss', $email, $cpf);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erro = 'duplicate';
            $check->close();
            $conn->close();
        } else {
            $check->close();
            $conn->begin_transaction();

            try {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $insertLogin = $conn->prepare('INSERT INTO login (usuario, senha) VALUES (?, ?)');
                $insertLogin->bind_param('ss', $email, $senhaHash);
                $insertLogin->execute();
                $idLogin = $conn->insert_id;
                $insertLogin->close();

                $insertUsuario = $conn->prepare(
                    'INSERT INTO cadastro_usuario (nome, cpf, data_nascimento, telefone, email, id_login) VALUES (?, ?, ?, ?, ?, ?)'
                );
                $insertUsuario->bind_param('sssssi', $nome, $cpf, $dataNascimento, $telefone, $email, $idLogin);
                $insertUsuario->execute();
                $idCadastroUsuario = $conn->insert_id;
                $insertUsuario->close();

                $insertAcessibilidade = $conn->prepare(
                    'INSERT INTO acessibilidade (d_visual, d_auditiva, d_motora, d_cardiaca, d_intelectual, d_neurologica, d_psiquica, outros, id_cadastro_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $insertAcessibilidade->bind_param(
                    'iiiiiiiii',
                    $acessibilidade[0],
                    $acessibilidade[1],
                    $acessibilidade[2],
                    $acessibilidade[3],
                    $acessibilidade[4],
                    $acessibilidade[5],
                    $acessibilidade[6],
                    $acessibilidade[7],
                    $idCadastroUsuario
                );
                $insertAcessibilidade->execute();
                $insertAcessibilidade->close();

                $conn->commit();
                $conn->close();

                session_regenerate_id(true);
                $_SESSION['loggedin'] = true;
                $_SESSION['tipo_usuario'] = 'User';
                $_SESSION['id'] = $idCadastroUsuario;
                $_SESSION['nome'] = $nome;
                $_SESSION['id_cadastro_usuario'] = $idCadastroUsuario;

                header('Location: endereco.php');
                exit;
            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $conn->close();
                throw $exception;
            }
        }
    }
}

$mensagensErro = [
    'invalid_data' => 'Confira os campos: os dados estão incompletos ou inválidos.',
    'duplicate' => 'Este e-mail ou CPF já está cadastrado.'
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta - ChampionsSports</title>
    <?php require_once __DIR__ . '/tailwind.php'; ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#1a1a2e] via-[#2d2d46] to-[#6e00ff] px-4 py-8 text-white">
    <main class="mx-auto w-full max-w-2xl">
        <a href="../index.php" class="mb-6 mr-auto flex w-fit items-center gap-2 self-start text-left text-white/80 transition hover:text-white">
            <span aria-hidden="true" class="text-2xl">&larr;</span>
            <span>Voltar ao início</span>
        </a>

        <section class="rounded-2xl border border-white/10 bg-[#1a1a2e]/90 p-6 shadow-2xl backdrop-blur sm:p-8">
            <h1 class="mb-2 text-3xl font-bold">Criar conta</h1>
            <p class="mb-6 text-white/70">Preencha seus dados para se cadastrar.</p>

            <?php if (isset($mensagensErro[$erro])): ?>
                <p class="mb-5 rounded-lg border border-red-400/40 bg-red-500/15 p-3 text-sm text-red-100" role="alert">
                    <?= htmlspecialchars($mensagensErro[$erro], ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="cadastro_usuario.php" id="cadastroForm" method="POST" class="space-y-5">
                <div>
                    <label for="nome" class="mb-2 block text-sm font-medium">Nome completo</label>
                    <input type="text" id="nome" name="nome" required minlength="2" maxlength="25"
                           autocomplete="name" value="<?= htmlspecialchars($valores['nome'], ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="cpf" class="mb-2 block text-sm font-medium">CPF</label>
                        <input type="text" id="cpf" name="cpf" required inputmode="numeric" maxlength="14"
                               placeholder="000.000.000-00" value="<?= htmlspecialchars($valores['cpf'], ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                    <div>
                        <label for="datanascimento" class="mb-2 block text-sm font-medium">Data de nascimento</label>
                        <input type="date" id="datanascimento" name="datanascimento" required max="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($valores['datanascimento'], ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="telefone" class="mb-2 block text-sm font-medium">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" required maxlength="15"
                               placeholder="(00) 00000-0000" autocomplete="tel"
                               value="<?= htmlspecialchars($valores['telefone'], ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium">E-mail</label>
                        <input type="email" id="email" name="email" required maxlength="40" autocomplete="email"
                               value="<?= htmlspecialchars($valores['email'], ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="senha" class="mb-2 block text-sm font-medium">Senha</label>
                        <input type="password" id="senha" name="senha" required minlength="6" autocomplete="new-password"
                               class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                    <div>
                        <label for="confirmarSenha" class="mb-2 block text-sm font-medium">Confirmar senha</label>
                        <input type="password" id="confirmarSenha" name="confirmarSenha" required minlength="6" autocomplete="new-password"
                               class="w-full rounded-lg border border-white/15 bg-white/10 px-4 py-3 text-white outline-none focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                </div>

                <fieldset class="rounded-xl border border-white/15 p-4">
                    <legend class="px-2 font-medium">Possui alguma deficiência? *</legend>
                    <div class="flex flex-wrap items-center gap-5">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="deficiencia" value="sim" required <?= $valores['deficiencia'] === 'sim' ? 'checked' : '' ?>>
                            Sim
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="deficiencia" value="nao" <?= $valores['deficiencia'] === 'nao' ? 'checked' : '' ?>>
                            Não
                        </label>
                        <input type="text" id="qual_deficiencia" name="qual_deficiencia" placeholder="Qual?"
                               value="<?= htmlspecialchars($valores['qual_deficiencia'], ENT_QUOTES, 'UTF-8') ?>"
                               class="min-w-0 flex-1 rounded-lg border border-white/15 bg-white/10 px-3 py-2 text-white outline-none placeholder:text-white/40 focus:border-[#ff00aa] focus:ring-2 focus:ring-[#ff00aa]/40">
                    </div>
                    <div id="deficienciaOpcoesContainer" class="mt-4 hidden">
                        <p class="mb-3 text-sm text-white/80">Selecione os tipos, se desejar:</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <?php
                            $opcoesDeficiencia = [
                                'deficiencia_visual' => 'Visual',
                                'deficiencia_auditiva' => 'Auditiva',
                                'deficiencia_motora' => 'Motora',
                                'deficiencia_cardiaca' => 'Cardíaca',
                                'deficiencia_intelectual' => 'Intelectual',
                                'deficiencia_neurologica' => 'Neurológica',
                                'deficiencia_psiquica' => 'Psíquica',
                                'deficiencia_outros' => 'Outros'
                            ];
                            foreach ($opcoesDeficiencia as $campo => $rotulo):
                            ?>
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="<?= $campo ?>" class="accent-[#ff00aa]" <?= isset($_POST[$campo]) ? 'checked' : '' ?>>
                                    Deficiência <?= $rotulo ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </fieldset>

                <label class="flex items-start gap-3 text-sm text-white/80">
                    <input type="checkbox" id="termos" name="termos" required class="mt-1 accent-[#ff00aa]">
                    <span>Eu concordo com os Termos de Uso e a Política de Privacidade.</span>
                </label>

                <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-[#6e00ff] to-[#ff00aa] px-4 py-3 font-bold transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-white/70">
                    Criar conta
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-white/75">
                Já tem uma conta? <a href="login.php" class="font-semibold text-[#ff8bd5] hover:underline">Fazer login</a>
            </p>
        </section>
    </main>

    <script>
        const radiosDeficiencia = document.querySelectorAll('input[name="deficiencia"]');
        const containerDeficiencia = document.getElementById('deficienciaOpcoesContainer');
        const campoQualDeficiencia = document.getElementById('qual_deficiencia');

        function atualizarDeficiencia() {
            const possuiDeficiencia = document.querySelector('input[name="deficiencia"]:checked')?.value === 'sim';
            containerDeficiencia.classList.toggle('hidden', !possuiDeficiencia);
            campoQualDeficiencia.disabled = !possuiDeficiencia;
            if (!possuiDeficiencia) {
                campoQualDeficiencia.value = '';
            }
        }

        radiosDeficiencia.forEach((radio) => radio.addEventListener('change', atualizarDeficiencia));
        atualizarDeficiencia();

        document.getElementById('cpf').addEventListener('input', (event) => {
            const digits = event.target.value.replace(/\D/g, '').slice(0, 11);
            if (digits.length > 9) {
                event.target.value = `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
            } else if (digits.length > 6) {
                event.target.value = `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;
            } else if (digits.length > 3) {
                event.target.value = `${digits.slice(0, 3)}.${digits.slice(3)}`;
            } else {
                event.target.value = digits;
            }
        });

        document.getElementById('telefone').addEventListener('input', (event) => {
            const digits = event.target.value.replace(/\D/g, '').slice(0, 11);
            if (digits.length > 10) {
                event.target.value = digits.replace(/(\d{2})(\d{5})(\d{1,4})/, '($1) $2-$3');
            } else if (digits.length > 6) {
                event.target.value = digits.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
            } else if (digits.length > 2) {
                event.target.value = digits.replace(/(\d{2})(\d{1,5})/, '($1) $2');
            } else {
                event.target.value = digits;
            }
        });

        document.getElementById('cadastroForm').addEventListener('submit', (event) => {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmarSenha').value;
            if (senha !== confirmarSenha) {
                event.preventDefault();
                document.getElementById('confirmarSenha').setCustomValidity('As senhas não coincidem.');
                document.getElementById('confirmarSenha').reportValidity();
            }
        });
        document.getElementById('confirmarSenha').addEventListener('input', (event) => {
            event.target.setCustomValidity('');
        });
    </script>
</body>
</html>
