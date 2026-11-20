document.addEventListener('DOMContentLoaded', () => {
    const moeda = (v) => v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

    function mensagemErro(campo) {
        const v = campo.validity;
        if (v.valueMissing) return 'Campo obrigatório.';
        if (v.typeMismatch && campo.type === 'email') return 'Informe um e-mail válido.';
        if (v.tooShort) return `Mínimo de ${campo.minLength} caracteres.`;
        if (v.tooLong) return `Máximo de ${campo.maxLength} caracteres.`;
        if (v.rangeUnderflow) return `O valor mínimo é ${campo.min}.`;
        if (v.rangeOverflow) return `O valor máximo é ${campo.max}.`;
        if (v.stepMismatch) return 'Valor inválido.';
        if (v.patternMismatch) {
            if (campo.dataset.mascara === 'telefone') return 'Use o formato (11) 99999-9999.';
            return 'Formato inválido.';
        }
        if (v.customError) return campo.validationMessage;
        return 'Valor inválido.';
    }

    function validarCampo(campo) {
        if (campo.dataset.igual) {
            const outro = campo.form.querySelector(`[name="${campo.dataset.igual}"]`);
            campo.setCustomValidity(outro && outro.value !== campo.value ? 'As senhas não conferem.' : '');
        }

        let aviso = campo.parentElement.querySelector('.erro-campo');
        if (campo.checkValidity()) {
            campo.classList.remove('invalido');
            aviso?.remove();
            return true;
        }
        campo.classList.add('invalido');
        if (!aviso) {
            aviso = document.createElement('small');
            aviso.className = 'erro-campo';
            campo.insertAdjacentElement('afterend', aviso);
        }
        aviso.textContent = mensagemErro(campo);
        return false;
    }

    document.querySelectorAll('form[data-validar]').forEach((form) => {
        form.setAttribute('novalidate', '');
        const campos = () => form.querySelectorAll('input, select, textarea');

        form.addEventListener('submit', (ev) => {
            let ok = true;
            campos().forEach((c) => { if (!validarCampo(c)) ok = false; });
            if (!ok) {
                ev.preventDefault();
                form.querySelector('.invalido')?.focus();
            }
        });
        form.addEventListener('input', (ev) => {
            if (ev.target.classList.contains('invalido') || ev.target.dataset.igual) validarCampo(ev.target);
        });
    });

    document.querySelectorAll('[data-mascara="telefone"]').forEach((campo) => {
        campo.addEventListener('input', () => {
            const d = campo.value.replace(/\D/g, '').slice(0, 11);
            let r = d;
            if (d.length > 2) r = `(${d.slice(0, 2)}) ${d.slice(2)}`;
            if (d.length > 6) {
                const meio = d.length === 11 ? 7 : 6;
                r = `(${d.slice(0, 2)}) ${d.slice(2, meio)}-${d.slice(meio)}`;
            }
            campo.value = r;
        });
    });

    document.querySelectorAll('form[data-confirmar]').forEach((form) => {
        form.addEventListener('submit', (ev) => {
            if (!confirm(form.dataset.confirmar)) ev.preventDefault();
        });
    });

    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const arquivo = input.files[0];
            const alvo = document.querySelector(input.dataset.preview);
            if (!arquivo || !alvo) return;
            if (arquivo.size > 2 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 2 MB.');
                input.value = '';
                return;
            }
            const img = document.createElement('img');
            img.className = 'capa';
            img.src = URL.createObjectURL(arquivo);
            alvo.replaceChildren(img);
        });
    });

    const tabelaItens = document.getElementById('itens-pedido');
    if (tabelaItens) {
        const corpo = tabelaItens.querySelector('tbody');
        const modelo = corpo.querySelector('.item-linha').cloneNode(true);

        const recalcular = () => {
            let total = 0;
            corpo.querySelectorAll('.item-linha').forEach((linha) => {
                const opcao = linha.querySelector('select').selectedOptions[0];
                const preco = parseFloat(opcao?.dataset.preco || 0);
                const qtd = parseInt(linha.querySelector('input').value || 0, 10);
                const subtotal = preco * (qtd > 0 ? qtd : 0);
                linha.querySelector('.item-subtotal').textContent = moeda(subtotal);
                total += subtotal;
            });
            document.getElementById('total-pedido').textContent = moeda(total);
        };

        document.getElementById('adicionar-item').addEventListener('click', () => {
            const nova = modelo.cloneNode(true);
            nova.querySelector('select').value = '';
            nova.querySelector('input').value = 1;
            corpo.appendChild(nova);
            recalcular();
        });

        corpo.addEventListener('click', (ev) => {
            if (!ev.target.matches('[data-remover-item]')) return;
            if (corpo.querySelectorAll('.item-linha').length > 1) {
                ev.target.closest('tr').remove();
                recalcular();
            }
        });
        corpo.addEventListener('input', recalcular);
        corpo.addEventListener('change', recalcular);
        recalcular();
    }

    document.querySelectorAll('.alerta-sucesso').forEach((a) => {
        setTimeout(() => a.classList.add('sumindo'), 4000);
        setTimeout(() => a.remove(), 4600);
    });
});
