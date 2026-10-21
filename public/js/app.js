/* CupCake Gourmet - comportamento do front-end (sem dependências). */
(function () {
    'use strict';

    var $ = function (sel, raiz) { return (raiz || document).querySelector(sel); };
    var $$ = function (sel, raiz) { return Array.prototype.slice.call((raiz || document).querySelectorAll(sel)); };
    var digitos = function (v) { return v.replace(/\D/g, ''); };

    /* Menu no celular */
    var botaoMenu = $('[data-menu-toggle]');
    var menu = $('#menu');
    if (botaoMenu && menu) {
        botaoMenu.addEventListener('click', function () {
            var aberto = menu.classList.toggle('aberto');
            botaoMenu.setAttribute('aria-expanded', aberto ? 'true' : 'false');
        });
    }

    /* Alertas somem sozinhos depois de alguns segundos */
    $$('.alerta').forEach(function (alerta) {
        setTimeout(function () { alerta.classList.add('oculto'); }, 7000);
    });

    /* Máscaras de campos */
    var mascaras = {
        cep: function (v) {
            var d = digitos(v).slice(0, 8);
            return d.length > 5 ? d.slice(0, 5) + '-' + d.slice(5) : d;
        },
        cartao: function (v) {
            return digitos(v).slice(0, 19).replace(/(\d{4})(?=\d)/g, '$1 ');
        },
        validade: function (v) {
            var d = digitos(v).slice(0, 4);
            return d.length > 2 ? d.slice(0, 2) + '/' + d.slice(2) : d;
        },
        cvv: function (v) { return digitos(v).slice(0, 4); }
    };
    $$('[data-mask]').forEach(function (campo) {
        var fn = mascaras[campo.getAttribute('data-mask')];
        if (!fn) { return; }
        campo.addEventListener('input', function () { campo.value = fn(campo.value); });
    });

    /* Botões − e + da quantidade */
    $$('[data-qty]').forEach(function (caixa) {
        var campo = $('input', caixa);
        var menos = $('[data-minus]', caixa);
        var mais = $('[data-plus]', caixa);
        if (!campo) { return; }
        var atual = function () { return parseInt(campo.value, 10) || 1; };
        if (menos) { menos.addEventListener('click', function () { campo.value = Math.max(1, atual() - 1); }); }
        if (mais) { mais.addEventListener('click', function () { campo.value = Math.min(99, atual() + 1); }); }
    });

    /* Alternância entre Cartão e Pix */
    var radios = $$('input[name="metodo"]');
    function metodoSelecionado() {
        var marcado = radios.filter(function (r) { return r.checked; })[0];
        return marcado ? marcado.value : '';
    }
    function atualizarPaineis() {
        var metodo = metodoSelecionado();
        $$('[data-painel]').forEach(function (painel) {
            painel.hidden = painel.getAttribute('data-painel') !== metodo;
        });
        $$('.opcao-pagto').forEach(function (rotulo) {
            var r = $('input', rotulo);
            rotulo.classList.toggle('ativa', !!(r && r.checked));
        });
    }
    if (radios.length) {
        radios.forEach(function (r) { r.addEventListener('change', atualizarPaineis); });
        atualizarPaineis();
    }

    /* Validação do checkout no navegador (o servidor valida de novo) */
    function luhn(numero) {
        var d = digitos(numero);
        if (d.length < 13 || d.length > 19) { return false; }
        var soma = 0, dobrar = false;
        for (var i = d.length - 1; i >= 0; i--) {
            var n = parseInt(d.charAt(i), 10);
            if (dobrar) { n *= 2; if (n > 9) { n -= 9; } }
            soma += n;
            dobrar = !dobrar;
        }
        return soma % 10 === 0;
    }
    function validadeOk(v) {
        var m = /^(0[1-9]|1[0-2])\/(\d{2})$/.exec(v);
        if (!m) { return false; }
        var fim = new Date(2000 + parseInt(m[2], 10), parseInt(m[1], 10), 0, 23, 59, 59);
        return fim >= new Date();
    }

    var form = $('[data-form-checkout]');
    if (form) {
        form.addEventListener('submit', function (ev) {
            var erros = {};
            var valor = function (nome) {
                var el = form.elements[nome];
                return el ? el.value.trim() : '';
            };

            if (!valor('nome')) { erros.nome = 'Informe seu nome.'; }
            var email = valor('email');
            if (!email) { erros.email = 'Informe seu e-mail.'; }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { erros.email = 'Informe um endereço de e-mail válido.'; }

            if (metodoSelecionado() === 'CARTAO') {
                if (!valor('nome_cartao')) { erros.nome_cartao = 'Informe o nome impresso no cartão.'; }
                if (!valor('numero_cartao')) { erros.numero_cartao = 'Informe o número do cartão.'; }
                else if (!luhn(valor('numero_cartao'))) { erros.numero_cartao = 'Número de cartão inválido.'; }
                if (!valor('validade')) { erros.validade = 'Informe a validade (MM/AA).'; }
                else if (!validadeOk(valor('validade'))) { erros.validade = 'Validade inválida ou cartão vencido.'; }
                if (!valor('cvv')) { erros.cvv = 'Informe o CVV.'; }
                else if (!/^\d{3,4}$/.test(valor('cvv'))) { erros.cvv = 'CVV inválido.'; }
            }

            $$('[data-erro-for]', form).forEach(function (span) {
                span.textContent = erros[span.getAttribute('data-erro-for')] || '';
            });

            var nomes = Object.keys(erros);
            if (nomes.length) {
                ev.preventDefault();
                var primeiro = form.elements[nomes[0]];
                if (primeiro && primeiro.focus) { primeiro.focus(); }
            }
        });
    }
})();
