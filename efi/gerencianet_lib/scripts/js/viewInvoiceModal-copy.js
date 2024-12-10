window.onload = function() {



    function loadBtnModal() {

        $.ajax({
            url: "modules/gateways/efi/gerencianet_lib/html/btnViewModal.html",
            dataType: "html",
            cache: false,
            success: function(btn) {
                btnModalActions(btn);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro ao carregar o botão do modal:", textStatus, errorThrown);
            }
        });

    }


    function carregaModal() {
        const verificaSeFaturaFoiCarregada = setInterval(() => {
            const url = new URL(window.location.href);
            const id = url.searchParams.get("id");


            // Verifica se o ID existe
            if (id !== null) {
                $.ajax({
                    url: "modules/gateways/efi/gerencianet_lib/frontend/html/modal.html",
                    dataType: "html",
                    cache: false,
                    success: function(modalHtml) {
                        clearInterval(verificaSeFaturaFoiCarregada); // Para o intervalo se o modal for carregado com sucesso
                        addModalToInvoice(modalHtml);

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Erro ao carregar o modal:", textStatus, errorThrown);
                    }
                });
            }
        }, 1000);
    }


    function btnModalActions(btn) {
        document.getElementById("modal_content").insertAdjacentHTML('beforeend', btn);

        let buttonFinalizar = $('.botao');

        $(buttonFinalizar).css('display', 'inline-block');
        $(buttonFinalizar).prop('disabled', true).addClass('disabled');
        carregaModal();


        $(buttonFinalizar).click(loadModal);

    }

    function addModalToInvoice(modal) {
        const buttonFinalizar = $('.botao');
        document.getElementById("modal_content").insertAdjacentHTML('beforebegin', modal);


        let divModal = $('.optionPaymentEfi');
        loadScriptsModalEfi();
        // Attach the close handler for the close button
        $('.fechar').off('click').on('click', function() {
            hideModal(divModal);
        });

        // Use an anonymous function to properly bind the click event
        $(divModal).click(function(e) {
            handlerModal(e, divModal);
        });
        $(buttonFinalizar).removeAttr('disabled').removeClass('disabled');
    }

    function loadModal() {
        // Verifica se o modal existe
        let modalLoaded = $("div").hasClass("optionPaymentEfi");
        let modal = modalLoaded ? $('.optionPaymentEfi') : null;
        let styleModal = $('#stylePaymentMehtodEfi');

        // Se o modal existir, faz a manipulação
        if (modal) {

            // Mostra o modal com animação e habilita o estilo após a animação
            modal.show(700, () => {
                    if (styleModal.length) {
                        styleModal.prop('disabled', false);
                    }
                })
                .css('display', 'block')
                .removeClass('d-none');
        }
    }


    function hideModal(divModal) {
        let styleModal = $('#stylePaymentMehtodEfi');
        styleModal.prop('disabled', true);
        $(divModal).hide(700);
    }

    function handlerModal(e, divModal) {
        if (e.target.className.includes('optionPaymentEfi')) {
            hideModal(divModal);
        }


    }

    loadBtnModal();



}