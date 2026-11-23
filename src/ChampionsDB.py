import json
import os
from pathlib import Path

import bcrypt
import mysql.connector


class ChampionsDB:
    def __init__(self, config_path=None):
        if config_path is None:
            config_path = Path(__file__).resolve().parent.parent / "db_config.json"
        with open(config_path, "r", encoding="utf-8") as arquivo:
            self.config = json.load(arquivo)

    def conectar(self):
        return mysql.connector.connect(**self.config)

    def autenticar(self, usuario, senha):
        with self.conectar() as conexao:
            cursor = conexao.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT l.id_login, l.usuario, l.senha, l.tipo_usuario,
                       c.nome
                FROM login l
                LEFT JOIN cadastro_usuario c ON c.id_login = l.id_login
                WHERE l.usuario = %s AND l.situacao = 'A'
                """,
                (usuario,),
            )
            registro = cursor.fetchone()
            cursor.close()

        if not registro:
            return None

        senha_hash = registro["senha"]
        if isinstance(senha_hash, str):
            senha_hash = senha_hash.encode("utf-8")
        try:
            valida = bcrypt.checkpw(senha.encode("utf-8"), senha_hash)
        except ValueError:
            valida = False
        if not valida:
            return None
        return registro

    def existe_usuario(self, usuario):
        with self.conectar() as conexao:
            cursor = conexao.cursor()
            cursor.execute("SELECT 1 FROM login WHERE usuario = %s", (usuario,))
            existe = cursor.fetchone() is not None
            cursor.close()
        return existe

    def obter_nome(self, usuario):
        with self.conectar() as conexao:
            cursor = conexao.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT c.nome
                FROM login l
                LEFT JOIN cadastro_usuario c ON c.id_login = l.id_login
                WHERE l.usuario = %s
                """,
                (usuario,),
            )
            registro = cursor.fetchone()
            cursor.close()
        return registro["nome"] if registro and registro["nome"] else usuario

    def cadastrar_usuario(self, usuario, senha, nome, dados=None):
        dados = dados or {}
        if self.existe_usuario(usuario):
            return False, "Este usuário já está cadastrado."

        senha_hash = bcrypt.hashpw(
            senha.encode("utf-8"), bcrypt.gensalt()
        ).decode("utf-8")

        try:
            with self.conectar() as conexao:
                cursor = conexao.cursor()
                cursor.execute(
                    """
                    INSERT INTO login(usuario, senha, tipo_usuario, situacao)
                    VALUES (%s, %s, 'User', 'A')
                    """,
                    (usuario, senha_hash),
                )
                id_login = cursor.lastrowid
                cursor.execute(
                    """
                    INSERT INTO cadastro_usuario(
                        nome, cpf, data_nascimento, telefone, email,
                        situacao, id_login
                    ) VALUES (%s, %s, %s, %s, %s, 'A', %s)
                    """,
                    (
                        nome,
                        dados.get("cpf", ""),
                        dados.get("nascimento") or "2000-01-01",
                        dados.get("telefone", ""),
                        dados.get("email", ""),
                        id_login,
                    ),
                )
                id_cadastro = cursor.lastrowid

                cursor.execute(
                    """
                    INSERT INTO acessibilidade(
                        d_visual, d_auditiva, d_motora, d_cardiaca,
                        d_intelectual, d_neurologica, d_psiquica, outros,
                        id_cadastro_usuario
                    ) VALUES (0, 0, 0, 0, 0, 0, 0, 0, %s)
                    """,
                    (id_cadastro,),
                )
                conexao.commit()
                cursor.close()
            return True, "Cadastro realizado com sucesso!"
        except mysql.connector.Error as erro:
            return False, f"Erro ao cadastrar no banco: {erro}"

    def listar_usuarios(self):
        with self.conectar() as conexao:
            cursor = conexao.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT l.usuario, c.nome, c.cpf, c.data_nascimento,
                       c.telefone, c.email, c.situacao,
                       l.tipo_usuario
                FROM login l
                LEFT JOIN cadastro_usuario c ON c.id_login = l.id_login
                ORDER BY l.id_login
                """
            )
            usuarios = cursor.fetchall()
            cursor.close()
        return usuarios

    def cadastrar_endereco(self, dados):
        with self.conectar() as conexao:
            cursor = conexao.cursor()
            cursor.execute(
                "SELECT id_cadastro_usuario FROM cadastro_usuario LIMIT 1"
            )
            registro = cursor.fetchone()
            if not registro:
                cursor.close()
                return False, "Nenhum usuário cadastrado."
            cursor.execute(
                """
                INSERT INTO endereco(
                    cep, rua, bairro, cidade, uf, complemento,
                    situacao, id_cadastro_usuario
                ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                """,
                (
                    dados["cep"], dados["rua"], dados["bairro"],
                    dados["cidade"], dados["uf"], dados["complemento"],
                    dados["situacao"].upper()[:1], registro[0],
                ),
            )
            conexao.commit()
            cursor.close()
        return True, "Endereço salvo com sucesso."

    def listar_eventos(self):
        with self.conectar() as conexao:
            cursor = conexao.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT e.id_eventos, e.tipo_eventos, e.categorias_esportes,
                       e.localizacao, e.data_eventos, e.informacao,
                       e.link_localizacao, e.situacao, e.id_categoria
                FROM eventos e
                ORDER BY e.id_eventos
                """
            )
            eventos = cursor.fetchall()
            cursor.close()
        return eventos

    def cadastrar_evento(self, dados):
        try:
            with self.conectar() as conexao:
                cursor = conexao.cursor()
                cursor.execute(
                    "SELECT id_categoria FROM categoria ORDER BY id_categoria LIMIT 1"
                )
                registro = cursor.fetchone()
                if not registro:
                    cursor.execute(
                        """
                        INSERT INTO categoria(
                            ciclismo, futebol, voleibol, academia, caminhada,
                            natacao, lazer, pcd, favoritos, id_cadastro_usuario
                        ) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 'A', NULL)
                        """
                    )
                    id_categoria = cursor.lastrowid
                else:
                    id_categoria = registro[0]

                cursor.execute(
                    """
                    INSERT INTO eventos(
                        tipo_eventos, categorias_esportes, localizacao,
                        data_eventos, informacao, link_localizacao,
                        situacao, id_categoria
                    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                    """,
                    (
                        dados["tipo"], dados["categoria"],
                        dados["localizacao"], dados["data"] or "2025-10-29",
                        dados["informacao"], dados["link_web"],
                        dados["situacao"].upper()[:1], id_categoria,
                    ),
                )
                conexao.commit()
                cursor.close()
            return True, "Evento cadastrado com sucesso."
        except mysql.connector.Error as erro:
            return False, f"Erro ao cadastrar evento: {erro}"

    def excluir_evento(self, id_evento):
        with self.conectar() as conexao:
            cursor = conexao.cursor()
            cursor.execute(
                "DELETE FROM eventos WHERE id_eventos = %s",
                (id_evento,),
            )
            conexao.commit()
            cursor.close()
