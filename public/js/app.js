// Máscaras simples de CNPJ e telefone + confirmação de exclusão. Sem bibliotecas.
(function () {
  'use strict';

  const masks = {
    cnpj(digits) {
      return digits
        .slice(0, 14)
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
    },
    phone(digits) {
      digits = digits.slice(0, 11);
      if (digits.length <= 2) return digits.replace(/^(\d+)/, '($1');
      let middle = 4;
      if (digits.length === 11) {
        middle = 5;
      }
      return digits
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(new RegExp('(\\d{' + middle + '})(\\d{1,4})$'), '$1-$2');
    },
  };

  document.querySelectorAll('[data-mask]').forEach((input) => {
    const mask = masks[input.dataset.mask];
    const apply = () => { input.value = mask(input.value.replace(/\D/g, '')); };
    input.addEventListener('input', apply);
    apply();
  });

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
  });
})();
