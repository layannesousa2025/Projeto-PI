import tkinter as tk
from tkinter import ttk, messagebox
import json
import os
import hashlib
import secrets
from PIL import Image, ImageTk, ImageOps
from ChampionsDB import ChampionsDB


class GerenciadorTarefas:
    ROXO_ESCURO = "#35205E"
    ROXO = "#5B2A86"
    ROXO_CLARO = "#E9D5FF"
    BRANCO = "#FFFFFF"
    FUNDO_LATERAL = "#F4F1F8"
    TEXTO = "#202020"

    def __init__(self, root, ao_sair=None):
        self.root = root
        self.ao_sair = ao_sair
        self.root.title("ChampionsSports")
        self.root.geometry("960x590")
        self.root.configure(bg=self.BRANCO)
        self.root.minsize(800, 520)

        self.arquivo = os.path.join(
            os.path.dirname(os.path.dirname(__file__)), "tarefas.json"
        )
        self.db = ChampionsDB()
        self.tarefas = self.carregar_tarefas()
        self.criar_layout()

    def carregar_tarefas(self):
        if os.path.exists(self.arquivo):
            try:
                with open(self.arquivo, "r", encoding="utf-8") as arquivo:
                    return json.load(arquivo)
            except Exception:
                return []
        return []

    def criar_layout(self):
        barra = tk.Frame(
            self.root,
            bg=self.BRANCO,
            height=48,
            highlightthickness=0,
        )
        barra.pack(fill="x")
        barra.pack_propagate(False)

        self.botao_barra(
            barra, "⌂", "Início", self.mostrar_inicio
        ).pack(side="left", padx=(16, 4), pady=8)
        self.botao_barra(
            barra, "▣", "Eventos", lambda: self.mostrar_modulo("Eventos")
        ).pack(side="left", padx=4, pady=8)
        self.botao_barra(
            barra, "≡", "Categorias", lambda: self.mostrar_modulo("Categoria")
        ).pack(side="left", padx=4, pady=8)

        lateral = tk.Frame(
            self.root,
            bg=self.FUNDO_LATERAL,
            width=220,
            highlightthickness=0,
        )
        lateral.pack(side="left", fill="y")
        lateral.pack_propagate(False)

        marca = tk.Frame(lateral, bg=self.FUNDO_LATERAL)
        marca.pack(fill="x", padx=18, pady=(24, 28), ipady=8)

        tk.Label(
            marca,
            text="🏆",
            font=("Segoe UI", 20),
            bg=self.FUNDO_LATERAL,
            fg=self.ROXO_ESCURO,
        ).pack(side="left", padx=(4, 8))
        tk.Label(
            marca,
            text="ChampionsSports",
            font=("Segoe UI", 10, "bold"),
            bg=self.FUNDO_LATERAL,
            fg=self.ROXO_ESCURO,
        ).pack(side="left")

        menu = [
            ("Cadastro", "📝", lambda: self.mostrar_modulo("Cadastro")),
            ("Usuário", "👤", lambda: self.mostrar_modulo("Usuário")),
            ("Categoria", "▦", lambda: self.mostrar_modulo("Categoria")),
            ("Eventos", "📅", lambda: self.mostrar_modulo("Eventos")),
            ("Endereço", "📍", lambda: self.mostrar_modulo("Endereço")),
        ]
        for texto, icone, comando in menu:
            self.botao_menu(lateral, texto, icone, comando).pack(
                padx=18, pady=(0, 10), fill="x"
            )

        tk.Button(
            lateral,
            text="Sair do sistema",
            font=("Segoe UI", 9, "bold"),
            bg=self.ROXO,
            fg=self.BRANCO,
            relief="flat",
            bd=0,
            padx=10,
            pady=10,
            cursor="hand2",
            activebackground="#7042A0",
            activeforeground=self.BRANCO,
            command=self.sair,
        ).pack(side="bottom", padx=18, pady=24, fill="x")

        self.conteudo = tk.Frame(self.root, bg=self.ROXO)
        self.conteudo.pack(side="left", fill="both", expand=True)
        self.mostrar_inicio()

    def botao_barra(self, pai, icone, texto, comando):
        return tk.Button(
            pai,
            text=f"{icone}  {texto}",
            font=("Segoe UI", 9, "bold"),
            bg=self.BRANCO,
            fg=self.ROXO_ESCURO,
            relief="flat",
            bd=0,
            padx=10,
            pady=8,
            cursor="hand2",
            activebackground=self.ROXO_CLARO,
            activeforeground=self.ROXO_ESCURO,
            command=comando,
        )

    def botao_menu(self, pai, texto, icone, comando):
        return tk.Button(
            pai,
            text=f"{icone}   {texto}",
            font=("Segoe UI", 10, "bold"),
            bg=self.BRANCO,
            fg=self.TEXTO,
            relief="flat",
            bd=0,
            height=2,
            anchor="w",
            padx=14,
            cursor="hand2",
            activebackground=self.ROXO_CLARO,
            activeforeground=self.ROXO_ESCURO,
            command=comando,
        )

    def limpar_conteudo(self):
        for widget in self.conteudo.winfo_children():
            widget.destroy()

    def mostrar_inicio(self):
        self.limpar_conteudo()
        caminho_banner = os.path.join(
            os.path.dirname(os.path.dirname(__file__)),
            "assets",
            "banner_login.png",
        )
        if not os.path.exists(caminho_banner):
            caminho_banner = os.path.join(
                os.path.dirname(__file__), "banner_login.png"
            )
        if not os.path.exists(caminho_banner):
            return

        self.atualizar_banner(caminho_banner)
        self.conteudo.bind(
            "<Configure>",
            lambda event: self.atualizar_banner(caminho_banner),
        )

    def atualizar_banner(self, caminho_banner):
        largura = max(200, self.conteudo.winfo_width())
        altura = max(200, self.conteudo.winfo_height())
        if largura <= 1 or altura <= 1:
            return

        imagem = Image.open(caminho_banner).convert("RGBA")
        imagem = ImageOps.fit(
            imagem,
            (largura, altura),
            method=Image.Resampling.LANCZOS,
        )
        self.banner_tk = ImageTk.PhotoImage(imagem)

        for widget in getattr(self, "_banner_widgets", []):
            widget.destroy()
        self._banner_widgets = []

        banner = tk.Label(
            self.conteudo,
            image=self.banner_tk,
            bg=self.ROXO,
            bd=0,
            highlightthickness=0,
        )
        banner.place(x=0, y=0, relwidth=1, relheight=1)
        self._banner_widgets = [banner]

    def mostrar_modulo(self, nome):
        self.limpar_conteudo()
        pagina = tk.Frame(self.conteudo, bg="#FFFFFF")
        pagina.pack(fill="both", expand=True, padx=28, pady=24)

        tk.Label(
            pagina,
            text=nome,
            font=("Segoe UI", 20, "bold"),
            bg="#FFFFFF",
            fg=self.ROXO_ESCURO,
            anchor="w",
        ).pack(fill="x", pady=(0, 16))

        if nome == "Usuário":
            self.mostrar_usuarios(pagina)
        elif nome == "Cadastro":
            self.mostrar_cadastro(pagina)
        elif nome == "Categoria":
            self.mostrar_categoria(pagina)
        elif nome == "Eventos":
            self.mostrar_eventos(pagina)
        elif nome == "Endereço":
            self.mostrar_endereco(pagina)

    def mostrar_usuarios(self, pai):
        cabecalho = tk.Frame(pai, bg="#FFFFFF")
        cabecalho.pack(fill="x", pady=(0, 12))

        tk.Label(
            cabecalho,
            text="Usuários cadastrados",
            font=("Segoe UI", 11, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
        ).pack(side="left")

        coluna = tk.Frame(pai, bg="#FFFFFF")
        coluna.pack(fill="both", expand=True)

        tabela = ttk.Treeview(
            coluna,
            columns=("usuario", "nome", "situacao"),
            show="headings",
        )
        tabela.heading("usuario", text="Usuário")
        tabela.heading("nome", text="Nome")
        tabela.heading("situacao", text="Situação")
        tabela.column("usuario", width=150)
        tabela.column("nome", width=300)
        tabela.column("situacao", width=100)

        barra = ttk.Scrollbar(coluna, orient="vertical", command=tabela.yview)
        tabela.configure(yscrollcommand=barra.set)
        tabela.pack(side="left", fill="both", expand=True)
        barra.pack(side="right", fill="y")

        try:
            usuarios = self.db.listar_usuarios()
        except Exception as erro:
            usuarios = []
            messagebox.showerror("Erro de banco", str(erro))

        if usuarios:
            for dados in usuarios:
                tabela.insert(
                    "",
                    "end",
                    values=(
                        dados.get("usuario", ""),
                        dados.get("nome", ""),
                        "Ativo" if dados.get("situacao") == "A" else "Inativo",
                    ),
                )
        else:
            tabela.insert("", "end", values=("Nenhum usuário", "-", "-"))

    def mostrar_cadastro(self, pai):
        form = tk.Frame(pai, bg="#FFFFFF")
        form.pack(fill="x")

        for coluna in range(2):
            form.columnconfigure(coluna, weight=1)

        self.cadastro_nome = self.campo_cadastro(form, 0, 0, "Nome")
        self.cadastro_usuario = self.campo_cadastro(form, 0, 1, "Usuário")
        self.cadastro_cpf = self.campo_cadastro(form, 1, 0, "CPF")
        self.cadastro_telefone = self.campo_cadastro(form, 1, 1, "Telefone")
        self.cadastro_email = self.campo_cadastro(form, 2, 0, "Email")
        self.cadastro_senha = self.campo_cadastro(form, 2, 1, "Senha", mostrar="•")

        tk.Button(
            form,
            text="Cadastrar usuário",
            font=("Segoe UI", 10, "bold"),
            bg=self.ROXO,
            fg=self.BRANCO,
            relief="flat",
            bd=0,
            padx=18,
            pady=9,
            cursor="hand2",
            activebackground="#7042A0",
            activeforeground=self.BRANCO,
            command=self.salvar_cadastro,
        ).grid(row=3, column=0, sticky="w", padx=(0, 12), pady=(18, 0))

    def campo_cadastro(self, pai, linha, coluna, rotulo, mostrar=None):
        bloco = tk.Frame(pai, bg="#FFFFFF")
        bloco.grid(
            row=linha,
            column=coluna,
            sticky="ew",
            padx=(0, 12) if coluna == 0 else (12, 0),
            pady=7,
        )
        tk.Label(
            bloco,
            text=rotulo,
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
            anchor="w",
        ).pack(fill="x", pady=(0, 4))
        entrada = tk.Entry(
            bloco,
            font=("Segoe UI", 10),
            bg="#FFFFFF",
            fg=self.TEXTO,
            relief="solid",
            bd=1,
            show=mostrar,
        )
        entrada.pack(fill="x", ipady=5)
        return entrada

    def salvar_cadastro(self):
        nome = self.cadastro_nome.get().strip()
        usuario = self.cadastro_usuario.get().strip().lower()
        senha = self.cadastro_senha.get()

        if not nome or not usuario or not senha:
            messagebox.showwarning("Aviso", "Informe nome, usuário e senha.")
            return

        dados = {
            "cpf": self.cadastro_cpf.get().strip(),
            "telefone": self.cadastro_telefone.get().strip(),
            "email": self.cadastro_email.get().strip(),
            "nascimento": "2000-01-01",
        }
        ok, mensagem = self.db.cadastrar_usuario(usuario, senha, nome, dados)
        if not ok:
            messagebox.showerror("Erro", mensagem)
            return

        messagebox.showinfo("Sucesso", mensagem)
        self.mostrar_modulo("Usuário")

    def mostrar_endereco(self, pai):
        pai.configure(bg="#F4F1F8")

        titulo = tk.Frame(pai, bg="#FFFFFF", height=48)
        titulo.pack(fill="x")
        titulo.pack_propagate(False)
        tk.Label(
            titulo,
            text="Complete o cadastro com seu Endereço",
            font=("Segoe UI", 12, "bold"),
            bg="#FFFFFF",
            fg="#202020",
            anchor="center",
        ).pack(expand=True)

        form = tk.Frame(pai, bg="#FFFFFF")
        form.pack(fill="both", expand=True, padx=20, pady=(12, 8))
        form.columnconfigure(0, weight=1)
        form.columnconfigure(1, weight=1)

        def campo(linha, coluna, rotulo):
            bloco = tk.Frame(form, bg="#FFFFFF")
            bloco.grid(
                row=linha,
                column=coluna,
                sticky="ew",
                padx=(0, 12) if coluna == 0 else (12, 0),
                pady=7,
            )
            tk.Label(
                bloco,
                text=rotulo,
                font=("Segoe UI", 9, "bold"),
                bg="#FFFFFF",
                fg="#202020",
                anchor="w",
            ).pack(fill="x", pady=(0, 4))
            entrada = tk.Entry(
                bloco,
                font=("Segoe UI", 10),
                bg="#FFFFFF",
                fg="#202020",
                relief="solid",
                bd=1,
            )
            entrada.pack(fill="x", ipady=5)
            return entrada

        self.endereco_cep = campo(0, 0, "CEP")
        self.endereco_rua = campo(1, 0, "Rua")
        self.endereco_cidade = campo(2, 0, "Cidade")

        uf_bloco = tk.Frame(form, bg="#FFFFFF")
        uf_bloco.grid(row=0, column=1, sticky="ew", padx=(12, 0), pady=7)
        tk.Label(
            uf_bloco,
            text="UF",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg="#202020",
            anchor="w",
        ).pack(fill="x", pady=(0, 4))
        self.endereco_uf = ttk.Combobox(
            uf_bloco,
            font=("Segoe UI", 10),
            state="readonly",
            values=[
                "Escolha seu Estado", "AC", "AL", "AP", "AM", "BA", "CE",
                "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB",
                "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC",
                "SP", "SE", "TO",
            ],
        )
        self.endereco_uf.set("Escolha seu Estado")
        self.endereco_uf.pack(fill="x", ipady=4)

        self.endereco_bairro = campo(1, 1, "Bairro")

        complemento_bloco = tk.Frame(form, bg="#FFFFFF")
        complemento_bloco.grid(
            row=2,
            column=1,
            rowspan=2,
            sticky="nsew",
            padx=(12, 0),
            pady=7,
        )
        tk.Label(
            complemento_bloco,
            text="Complemento",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg="#202020",
            anchor="w",
        ).pack(fill="x", pady=(0, 4))
        self.endereco_complemento = tk.Text(
            complemento_bloco,
            font=("Segoe UI", 10),
            bg="#FFFFFF",
            fg="#202020",
            relief="solid",
            bd=1,
            height=4,
        )
        self.endereco_complemento.pack(fill="both", expand=True)

        situacao = tk.Frame(form, bg="#FFFFFF")
        situacao.grid(row=3, column=0, sticky="w", pady=(12, 0))
        tk.Label(
            situacao,
            text="Situação",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg="#202020",
        ).pack(anchor="w", pady=(0, 6))
        self.endereco_situacao = tk.StringVar(value="ativo")
        tk.Radiobutton(
            situacao,
            text="Ativo",
            variable=self.endereco_situacao,
            value="ativo",
            bg="#FFFFFF",
            fg="#202020",
            font=("Segoe UI", 9),
        ).pack(side="left", padx=(0, 14))
        tk.Radiobutton(
            situacao,
            text="Inativo",
            variable=self.endereco_situacao,
            value="inativo",
            bg="#FFFFFF",
            fg="#202020",
            font=("Segoe UI", 9),
        ).pack(side="left")

        botoes = tk.Frame(pai, bg="#FFFFFF")
        botoes.pack(fill="x", pady=(8, 12))

        tk.Button(
            botoes,
            text="Cadastrar",
            font=("Segoe UI", 9, "bold"),
            bg="#EFEFEF",
            fg="#202020",
            relief="solid",
            bd=1,
            padx=24,
            pady=8,
            cursor="hand2",
            command=self.salvar_endereco,
        ).pack(side="left", padx=(260, 50))

        tk.Button(
            botoes,
            text="Salvar",
            font=("Segoe UI", 9, "bold"),
            bg="#EFEFEF",
            fg="#202020",
            relief="solid",
            bd=1,
            padx=24,
            pady=8,
            cursor="hand2",
            command=self.salvar_endereco,
        ).pack(side="left")

    def salvar_endereco(self):
        cep = self.endereco_cep.get().strip()
        rua = self.endereco_rua.get().strip()
        if not cep or not rua:
            messagebox.showwarning("Aviso", "Informe pelo menos o CEP e a rua.")
            return

        dados = {
            "cep": cep,
            "uf": self.endereco_uf.get(),
            "rua": rua,
            "bairro": self.endereco_bairro.get().strip(),
            "cidade": self.endereco_cidade.get().strip(),
            "complemento": self.endereco_complemento.get("1.0", "end").strip(),
            "situacao": self.endereco_situacao.get(),
        }
        ok, mensagem = self.db.cadastrar_endereco(dados)
        if not ok:
            messagebox.showerror("Erro", mensagem)
            return
        messagebox.showinfo("Sucesso", mensagem)

    def mostrar_categoria(self, pai):
        pai.configure(bg="#FFFFFF")

        tk.Label(
            pai,
            text="Categorias Esportivas",
            font=("Segoe UI", 13, "bold"),
            bg="#FFFFFF",
            fg="#AEB2B8",
            anchor="center",
        ).pack(pady=(18, 12))

        busca = tk.Frame(
            pai,
            bg="#FFFFFF",
            highlightthickness=1,
            highlightbackground="#C9CDD4",
        )
        busca.pack(pady=(0, 18), ipadx=8)

        pesquisa = tk.Entry(
            busca,
            font=("Segoe UI", 11),
            bg="#FFFFFF",
            fg=self.TEXTO,
            relief="flat",
            bd=0,
            justify="left",
            width=32,
        )
        pesquisa.pack(side="left", ipady=8, padx=(8, 4))

        tk.Label(
            busca,
            text="⌕",
            font=("Segoe UI", 14),
            bg="#FFFFFF",
            fg="#777777",
        ).pack(side="right", padx=(4, 8))

        categorias = [
            ("🚴", "Ciclismo"),
            ("🥊", "Futebol"),
            ("🏃", "Voleibol"),
            ("🏋", "Academia"),
            ("🏃", "Caminhada"),
            ("🏊", "Natação"),
            ("🎯", "Lazer"),
            ("♿", "PCD"),
        ]

        grade = tk.Frame(pai, bg="#FFFFFF")
        grade.pack(pady=(2, 0))

        for indice, (icone, nome) in enumerate(categorias):
            linha = indice // 4
            coluna = indice % 4

            card = tk.Frame(
                grade,
                bg="#FFFFFF",
                width=112,
                height=96,
            )
            card.grid(row=linha, column=coluna, padx=16, pady=10)
            card.grid_propagate(False)

            tk.Label(
                card,
                text=icone,
                font=("Segoe UI Emoji", 28),
                bg="#FFFFFF",
                fg="#202020",
            ).pack(pady=(2, 4))

            botao = tk.Button(
                card,
                text=nome.upper(),
                font=("Segoe UI", 8, "bold"),
                bg="#FFFFFF",
                fg="#202020",
                relief="solid",
                bd=1,
                cursor="hand2",
                width=13,
                padx=4,
                pady=4,
                command=lambda n=nome: self.filtrar_categoria(pesquisa, n),
                activebackground=self.ROXO_CLARO,
                activeforeground=self.ROXO_ESCURO,
            )
            botao.pack()

        def filtrar_categorias(event=None):
            texto = pesquisa.get().lower().strip()
            for card in grade.winfo_children():
                botao = card.winfo_children()[-1]
                nome = botao.cget("text").lower()
                card.grid_remove() if texto and texto not in nome else card.grid()

        pesquisa.bind("<KeyRelease>", filtrar_categorias)
        self._grade_categorias = grade

    def filtrar_categoria(self, pesquisa, nome):
        pesquisa.delete(0, "end")
        pesquisa.insert(0, nome)

    def mostrar_eventos(self, pai):
        pai.configure(bg="#F4F1F8")
        abas = tk.Frame(
            pai,
            bg="#FFFFFF",
            highlightthickness=1,
            highlightbackground="#D8D8D8",
        )
        abas.pack(fill="x", pady=(0, 12))

        tk.Button(
            abas,
            text="Pesquisa Eventos",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
            relief="flat",
            bd=0,
            padx=12,
            pady=7,
            command=lambda: self.mostrar_pesquisa_eventos(pai),
        ).pack(side="left")

        tk.Button(
            abas,
            text="Cadastro Eventos",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg=self.ROXO_ESCURO,
            relief="flat",
            bd=0,
            padx=12,
            pady=7,
        ).pack(side="left")

        form = tk.Frame(pai, bg="#FFFFFF", padx=18, pady=12)
        form.pack(fill="both", expand=True)
        form.columnconfigure(0, weight=1)
        form.columnconfigure(1, weight=1)

        self.evento_codigo = self.campo_cadastro(form, 0, 0, "Código")
        self.evento_nome = self.campo_cadastro(form, 1, 0, "Evento")
        self.evento_tipo = self.campo_cadastro(form, 2, 0, "Tipo de evento")
        self.evento_categoria = self.campo_cadastro(form, 3, 0, "Categoria do esporte")
        self.evento_local = self.campo_cadastro(form, 4, 0, "Localização")
        self.evento_data = self.campo_cadastro(form, 5, 0, "Data do evento")
        self.evento_link = self.campo_cadastro(form, 6, 0, "Link da localização")
        self.evento_link_mobile = self.campo_cadastro(form, 7, 0, "Link para celular")

        self.evento_informacao = tk.Text(
            form,
            font=("Segoe UI", 11),
            bg="#FFFFFF",
            fg=self.TEXTO,
            relief="solid",
            bd=1,
            height=8,
            wrap="word",
        )
        tk.Label(
            form,
            text="Informação",
            font=("Segoe UI", 10, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
            anchor="w",
        ).grid(row=0, column=1, sticky="w", padx=(18, 0), pady=(0, 4))
        self.evento_informacao.grid(
            row=1,
            column=1,
            rowspan=4,
            sticky="nsew",
            padx=(18, 0),
            pady=(0, 7),
            ipady=4,
        )

        situacao_bloco = tk.Frame(form, bg="#FFFFFF")
        situacao_bloco.grid(
            row=8,
            column=1,
            sticky="w",
            pady=(12, 0),
            padx=(18, 0),
        )
        tk.Label(
            situacao_bloco,
            text="Situação:",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
        ).pack(side="left", padx=(0, 12))
        self.evento_situacao = tk.StringVar(value="ativo")
        tk.Radiobutton(
            situacao_bloco,
            text="Ativo",
            variable=self.evento_situacao,
            value="ativo",
            bg="#FFFFFF",
            fg=self.TEXTO,
            font=("Segoe UI", 9),
        ).pack(side="left", padx=(0, 16))
        tk.Radiobutton(
            situacao_bloco,
            text="Inativo",
            variable=self.evento_situacao,
            value="inativo",
            bg="#FFFFFF",
            fg=self.TEXTO,
            font=("Segoe UI", 9),
        ).pack(side="left")

        botoes = tk.Frame(form, bg="#FFFFFF")
        botoes.grid(row=9, column=0, columnspan=2, sticky="ew", pady=(24, 0))

        tk.Button(
            botoes,
            text="Cadastrar evento",
            font=("Segoe UI", 10, "bold"),
            bg=self.ROXO,
            fg=self.BRANCO,
            relief="flat",
            bd=0,
            padx=18,
            pady=9,
            cursor="hand2",
            command=self.salvar_evento,
            activebackground="#7042A0",
            activeforeground=self.BRANCO,
        ).pack(side="left")

        tk.Button(
            botoes,
            text="Salvar",
            font=("Segoe UI", 9, "bold"),
            bg="#E9D5FF",
            fg=self.ROXO_ESCURO,
            relief="flat",
            bd=0,
            padx=18,
            pady=9,
            cursor="hand2",
            command=self.salvar_evento,
            activebackground="#F2E8FA",
            activeforeground=self.ROXO_ESCURO,
        ).pack(side="left", padx=(12, 0))

    def mostrar_pesquisa_eventos(self, pai=None):
        self.limpar_conteudo()
        pagina = tk.Frame(self.conteudo, bg="#FFFFFF")
        pagina.pack(fill="both", expand=True, padx=28, pady=24)

        tk.Label(
            pagina,
            text="Cadastro Eventos",
            font=("Segoe UI", 16, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
            anchor="w",
        ).pack(fill="x", pady=(0, 12))

        abas = tk.Frame(
            pagina,
            bg="#FFFFFF",
            highlightthickness=1,
            highlightbackground="#D8D8D8",
        )
        abas.pack(fill="x", pady=(0, 12))

        tk.Button(
            abas,
            text="Pesquisa Eventos",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg=self.ROXO_ESCURO,
            relief="flat",
            bd=0,
            padx=12,
            pady=7,
        ).pack(side="left")

        tk.Button(
            abas,
            text="Cadastro Eventos",
            font=("Segoe UI", 9),
            bg="#F2F2F2",
            fg=self.TEXTO,
            relief="flat",
            bd=0,
            padx=12,
            pady=7,
            command=lambda: self.mostrar_modulo("Eventos"),
        ).pack(side="left")

        busca = tk.Frame(pagina, bg="#FFFFFF")
        busca.pack(fill="x", pady=(8, 10))
        tk.Label(
            busca,
            text="Pesquisa de Eventos",
            font=("Segoe UI", 9, "bold"),
            bg="#FFFFFF",
            fg=self.TEXTO,
        ).pack(side="left", padx=(0, 8))
        self.pesquisa_evento = tk.Entry(
            busca,
            font=("Segoe UI", 10),
            relief="solid",
            bd=1,
            width=36,
        )
        self.pesquisa_evento.pack(side="left", ipady=4)

        tabela_area = tk.Frame(pagina, bg="#FFFFFF")
        tabela_area.pack(fill="both", expand=True)

        self.eventos_tabela = ttk.Treeview(
            tabela_area,
            columns=("codigo", "evento", "tipo", "categoria", "cidade",
                     "data", "link_web", "link_mobile", "informacao",
                     "situacao", "id_categoria"),
            show="headings",
        )
        colunas = [
            ("codigo", "Id. Evento", 80),
            ("evento", "Tipo Evento", 140),
            ("tipo", "Categoria esporte", 130),
            ("categoria", "Cidade", 100),
            ("cidade", "Data do Evento", 110),
            ("data", "Link Localização Web", 150),
            ("link_web", "Link Localização Mo.", 150),
            ("link_mobile", "Informação", 180),
            ("informacao", "Situação", 80),
            ("situacao", "Id.Categoria", 90),
            ("id_categoria", "", 30),
        ]
        for nome, titulo, largura in colunas:
            self.eventos_tabela.heading(nome, text=titulo)
            self.eventos_tabela.column(nome, width=largura, minwidth=60)

        barra_vertical = ttk.Scrollbar(
            tabela_area,
            orient="vertical",
            command=self.eventos_tabela.yview,
        )
        barra_horizontal = ttk.Scrollbar(
            tabela_area,
            orient="horizontal",
            command=self.eventos_tabela.xview,
        )
        self.eventos_tabela.configure(
            yscrollcommand=barra_vertical.set,
            xscrollcommand=barra_horizontal.set,
        )
        self.eventos_tabela.grid(
            row=0,
            column=0,
            sticky="nsew",
        )
        barra_vertical.grid(row=0, column=1, sticky="ns")
        barra_horizontal.grid(row=1, column=0, sticky="ew")
        tabela_area.rowconfigure(0, weight=1)
        tabela_area.columnconfigure(0, weight=1)

        botoes = tk.Frame(pagina, bg="#FFFFFF")
        botoes.pack(fill="x", pady=(18, 0))
        tk.Button(
            botoes,
            text="EDITAR",
            font=("Segoe UI", 9, "bold"),
            bg="#E8E8E8",
            fg=self.TEXTO,
            relief="solid",
            bd=1,
            padx=20,
            pady=8,
            command=self.editar_evento_selecionado,
        ).pack(side="left", padx=(260, 50))
        tk.Button(
            botoes,
            text="EXCLUIR",
            font=("Segoe UI", 9, "bold"),
            bg="#E8E8E8",
            fg=self.TEXTO,
            relief="solid",
            bd=1,
            padx=20,
            pady=8,
            command=self.excluir_evento_selecionado,
        ).pack(side="left")

        self.pesquisa_evento.bind(
            "<KeyRelease>",
            lambda event: self.carregar_tabela_eventos(
                self.pesquisa_evento.get()
            ),
        )
        self.carregar_tabela_eventos()

    def carregar_tabela_eventos(self, filtro=""):
        for item in self.eventos_tabela.get_children():
            self.eventos_tabela.delete(item)

        try:
            eventos = self.db.listar_eventos()
        except Exception as erro:
            eventos = []
            messagebox.showerror("Erro de banco", str(erro))

        texto = filtro.lower().strip()
        for evento in eventos:
            valores = (
                evento.get("id_eventos", ""),
                evento.get("tipo_eventos", ""),
                evento.get("categorias_esportes", ""),
                evento.get("localizacao", ""),
                str(evento.get("data_eventos", "")),
                evento.get("link_localizacao", ""),
                "",
                evento.get("informacao", ""),
                "Ativo" if evento.get("situacao") == "A" else "Inativo",
                evento.get("id_categoria", ""),
                evento.get("id_eventos", ""),
            )
            if not texto or texto in " ".join(str(v) for v in valores).lower():
                self.eventos_tabela.insert("", "end", values=valores)

    def excluir_evento_selecionado(self):
        selecao = self.eventos_tabela.selection()
        if not selecao:
            messagebox.showwarning("Aviso", "Selecione um evento.")
            return

        if not messagebox.askyesno("Confirmar", "Excluir evento selecionado?"):
            return

        valores = self.eventos_tabela.item(selecao[0], "values")
        id_evento = int(valores[-1])
        try:
            self.db.excluir_evento(id_evento)
        except Exception as erro:
            messagebox.showerror("Erro", f"Não foi possível excluir:\n{erro}")
            return

        self.carregar_tabela_eventos(self.pesquisa_evento.get())

    def editar_evento_selecionado(self):
        selecao = self.eventos_tabela.selection()
        if not selecao:
            messagebox.showwarning("Aviso", "Selecione um evento.")
            return
        messagebox.showinfo(
            "Editar",
            "Seleção encontrada. Use o cadastro de eventos para alterar os dados.",
        )

    def salvar_evento(self):
        nome = self.evento_nome.get().strip()
        tipo = self.evento_tipo.get().strip()
        categoria = self.evento_categoria.get().strip()

        if not nome:
            messagebox.showwarning("Aviso", "Informe o nome do evento.")
            self.evento_nome.focus_set()
            return

        dados = {
            "tipo": tipo,
            "categoria": categoria,
            "localizacao": self.evento_local.get().strip(),
            "data": self.evento_data.get().strip(),
            "informacao": self.evento_informacao.get("1.0", "end").strip(),
            "link_web": self.evento_link.get().strip(),
            "situacao": self.evento_situacao.get(),
        }
        ok, mensagem = self.db.cadastrar_evento(dados)
        if not ok:
            messagebox.showerror("Erro", mensagem)
            return

        messagebox.showinfo("Sucesso", mensagem)
        self.limpar_campos_evento()

    def limpar_campos_evento(self):
        campos = [
            self.evento_codigo,
            self.evento_nome,
            self.evento_tipo,
            self.evento_categoria,
            self.evento_local,
            self.evento_data,
            self.evento_link,
            self.evento_link_mobile,
        ]
        for campo in campos:
            campo.delete(0, "end")
        self.evento_informacao.delete("1.0", "end")
        self.evento_situacao.set("ativo")

    def aviso(self, nome):
        messagebox.showinfo(nome, f"A tela {nome} será aberta aqui.")

    def sair(self):
        if self.ao_sair:
            self.ao_sair()
        else:
            self.root.destroy()


if __name__ == "__main__":
    root = tk.Tk()
    app = GerenciadorTarefas(root)
    root.mainloop()
