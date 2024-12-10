import '../../../libs/jquery/jquery.mask.min.js';

export function applyMasks(inputArray) {
    inputArray.each((i, input) => {
        const $input = $(input);

        switch ($input.attr('data-mask')) {
            case 'cpf-cnpj':
                $input.mask('000.000.000-00', {
                    clearIfNotMatch: true,
                });
                // Passa uma função para o evento keydown
                $input.on('keydown', function() {
                    verifyKeyPressDocument($input);
                });
                break;
            case 'phone':
                $input.mask('(00) 00000-0000');
                break;
            case 'cep':
                $input.mask('00000-000');
                break;
            case 'credit-card':
                $input.mask('0000 0000 0000 0000');
                break;
            case 'expiry-date':
                $input.mask('00/00', { placeholder: 'MM/AA' });
                break;
            case 'cvv':
                $input.mask('000');
                break;
            default:
                console.warn(`No mask found for input: ${input}`);
        }
    });
}

function verifyKeyPressDocument($input) {
    if ($input.length) { // Verifica se $input não está vazio
        let value = $input.val();
        try {
            $input.unmask();
        } catch (error) {
            console.error(error); // Exibe o erro, se houver
        }
        const masks = ['000.000.000-000', '00.000.000/0000-00'];
        const mask = value.length <= 13 ? masks[0] : masks[1];

        $input.mask(mask);

        // Ajustando foco
        setTimeout(() => {
            $input[0].setSelectionRange(10000, 10000); // Usa setSelectionRange para ajustar o foco
        }, 0);

        // Reaplica o valor para mudar o foco
        var currentValue = $input.val();
        $input.val(''); // Limpa o valor
        $input.val(currentValue); // Reaplica o valor
    }
}