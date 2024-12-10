export const validateDocument = (value) => {
    const cleanedValue = value.replace(/\D/g, '');

    // Validação de CPF
    const validateCPF = (cpf) => {
        if (cpf.length !== 11) return false;
        let sum = 0;
        let remainder;

        for (let i = 1; i <= 9; i++) {
            sum += parseInt(cpf.charAt(i - 1)) * (11 - i);
        }

        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(9))) return false;

        sum = 0;
        for (let i = 1; i <= 10; i++) {
            sum += parseInt(cpf.charAt(i - 1)) * (12 - i);
        }

        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        return remainder === parseInt(cpf.charAt(10));



    };
    // Validação de CNPJ
    const validateCNPJ = (cnpj) => {
        if (cnpj.length !== 14) return false;
        let sum = 0;
        let remainder;

        const cnpjBase = cnpj.slice(0, 12);

        for (let i = 1; i <= 12; i++) {
            sum += parseInt(cnpjBase.charAt(i - 1)) * ((i <= 4) ? (5 - i) : (13 - i));
        }

        remainder = sum % 11;
        remainder = (remainder < 2) ? 0 : 11 - remainder;
        if (remainder !== parseInt(cnpj.charAt(12))) return false;

        sum = 0;
        for (let i = 1; i <= 13; i++) {
            sum += parseInt(cnpj.charAt(i - 1)) * ((i <= 5) ? (6 - i) : (14 - i));
        }

        remainder = sum % 11;
        remainder = (remainder < 2) ? 0 : 11 - remainder;
        return remainder === parseInt(cnpj.charAt(13));
    };


    if (cleanedValue.length === 11) {
        return validateCPF(cleanedValue); // CPF
    } else if (cleanedValue.length === 14) {
        return validateCNPJ(cleanedValue); // CNPJ
    }

    return false;

}

export const validateTelephone = (value) => {
    // Regex para validar o telefone (exemplo: (XX) XXXX-XXXX ou (XX) XXXXX-XXXX)
    const regex = /^\(\d{2}\) \d{4,5}-\d{4}$/;
    return regex.test(value);
};

export function validateCEP(value) {
    // Regex para validar o formato do CEP brasileiro (00000-000)
    const cepPattern = /^[0-9]{5}-?[0-9]{3}$/;
    return cepPattern.test(value);
}