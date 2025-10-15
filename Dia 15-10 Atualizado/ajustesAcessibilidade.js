document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    // --- 1. Lógica do Acordeão (Abrir/Fechar Painéis) ---
    function setupAccordion(toggleBtnId, panelId) {
        const toggleButton = document.getElementById(toggleBtnId);
        const optionsPanel = document.getElementById(panelId);
        if (!toggleButton || !optionsPanel) return;

        const openCloseIcon = toggleButton.querySelector('span[style*="font-size"]');

        toggleButton.addEventListener('click', () => {
            const isOpen = optionsPanel.style.maxHeight !== '0px';
            if (isOpen) {
                optionsPanel.style.maxHeight = '0px';
                if (openCloseIcon) openCloseIcon.style.transform = 'rotate(0deg)';
            } else {
                // scrollHeight dá a altura total do conteúdo
                optionsPanel.style.maxHeight = optionsPanel.scrollHeight + 'px';
                if (openCloseIcon) openCloseIcon.style.transform = 'rotate(180deg)';
            }
        });
    }

    setupAccordion('pertoTextAdTop', 'pertoTextSizeOptions');
    setupAccordion('pertoTextColorAdTop', 'pertoTextColorOptions');


    // --- 2. Lógica dos Sliders de Texto (Tamanho, Espaçamento) ---
    function setupSlider(controlId, styleProperty, baseValue = 100, multiplier = 1) {
        const slider = document.getElementById(`perto_font_slider_${controlId}`);
        if (!slider) return;

        const label = document.getElementById(`perto_font_slider_label_${controlId}`);
        const minusBtn = document.getElementById(`perto_slider_${controlId}_minus`);
        const plusBtn = document.getElementById(`perto_slider_${controlId}_plus`);

        const updateStyle = (value) => {
            if (label) label.textContent = `${value}%`;

            if (styleProperty === 'fontSize') {
                body.style.fontSize = `${baseValue + (Number(value) * multiplier)}%`;
            } else if (styleProperty === 'lineHeight') {
                body.style.lineHeight = `${baseValue + (Number(value) * multiplier)}`;
            } else if (styleProperty === 'letterSpacing') {
                body.style.letterSpacing = `${Number(value) * multiplier}px`;
            }
        };

        slider.addEventListener('input', (e) => {
            updateStyle(e.target.value);
        });

        if (minusBtn) {
            minusBtn.addEventListener('click', () => {
                slider.stepDown();
                updateStyle(slider.value);
            });
        }

        if (plusBtn) {
            plusBtn.addEventListener('click', () => {
                slider.stepUp();
                updateStyle(slider.value);
            });
        }
    }

    setupSlider('letter_size', 'fontSize', 100, 0.5); // Aumenta 0.5% por passo
    setupSlider('line_spacing', 'lineHeight', 1.5, 0.01); // Aumenta 0.01 por passo
    setupSlider('letter_spacing', 'letterSpacing', 0, 0.1); // Aumenta 0.1px por passo


    // --- 3. Lógica dos Botões de Funções de Texto ---
    const textFunctionButtons = document.querySelectorAll('#pertoTextAdMain .pertoFunctionBtn');

    textFunctionButtons.forEach(button => {
        // Ignora os botões que terão lógica especial
        if (button.id === 'perto_func_align_text') return;

        button.addEventListener('click', () => {
            const functionName = button.id.replace('perto_func_', '');
            const className = `body-${functionName.replace(/_/g, '-')}`;

            body.classList.toggle(className);
            button.classList.toggle('active');
        });
    });


    // --- 4. Lógica Especial para Alinhamento de Texto ---
    const alignButton = document.getElementById('perto_func_align_text');
    if (alignButton) {
        const alignOptions = ['left', 'center', 'right', 'justify'];
        let currentAlignIndex = 0;

        // Define um alinhamento inicial
        body.style.textAlign = alignOptions[currentAlignIndex];
        updateAlignIndicators(currentAlignIndex);

        alignButton.addEventListener('click', () => {
            currentAlignIndex = (currentAlignIndex + 1) % alignOptions.length;

            // Aplica o novo alinhamento
            body.style.textAlign = alignOptions[currentAlignIndex];
            alignButton.classList.add('active'); // Mantém ativo

            updateAlignIndicators(currentAlignIndex);

            // Remove o estado ativo após um tempo para feedback visual
            setTimeout(() => {
                if (alignOptions[currentAlignIndex] === 'left') { // Desativa só se voltar ao padrão
                    alignButton.classList.remove('active');
                }
            }, 300);
        });

        function updateAlignIndicators(activeIndex) {
            const indicators = alignButton.querySelectorAll('.alignment-dot');
            if (indicators) {
                indicators.forEach((dot, index) => {
                    if (index === activeIndex) {
                        dot.style.backgroundColor = '#007bff'; // Cor ativa
                    } else {
                        dot.style.backgroundColor = '#ccc'; // Cor inativa
                    }
                });
            }
        }
    }

    // --- 5. Lógica dos Botões de Funções de Cor (Contraste, Saturação) ---
    const colorFunctionButtons = document.querySelectorAll('#pertoTextColorAdMain .pertoFunctionBtn');
    colorFunctionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const functionName = button.id.replace('perto_func_', '');
            const className = `body-${functionName.replace(/_/g, '-')}`;

            // Remove classes de outros botões de função de cor para não acumular
            colorFunctionButtons.forEach(btn => {
                if (btn !== button) {
                    const otherClassName = `body-${btn.id.replace('perto_func_', '').replace(/_/g, '-')}`;
                    body.classList.remove(otherClassName);
                    btn.classList.remove('active');
                }
            });

            body.classList.toggle(className);
            button.classList.toggle('active');
        });
    });

    // --- 6. Lógica dos Seletores de Cor ---
    const colorOptions = document.querySelectorAll('.pertoColorOpt');
    colorOptions.forEach(button => {
        button.addEventListener('click', () => {
            const color = button.dataset.color;
            const target = button.dataset.target;

            if (target === 'text') {
                body.style.color = color;
            } else if (target === 'background') {
                body.style.backgroundColor = color;
            }
        });
    });

    // --- 7. Lógica dos Botões de Restaurar Cor ---
    const resetButtons = document.querySelectorAll('.pertoResetColorButton');
    resetButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.dataset.target;

            if (target === 'text') {
                body.style.color = ''; // Volta ao padrão do CSS
            } else if (target === 'background') {
                body.style.backgroundColor = ''; // Volta ao padrão do CSS
            }
        });
    });
});