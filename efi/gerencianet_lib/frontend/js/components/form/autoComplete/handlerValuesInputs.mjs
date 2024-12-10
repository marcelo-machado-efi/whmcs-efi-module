import { addBillet } from './billet.mjs';
import { addCard } from './card.mjs';
import { addPix } from './pix.mjs';
import { addOF } from './openFinance.mjs';
export const handlerValuesInputs = () => {
    const paymentMethods = ['billet', 'card', 'pix', 'OF'];

    paymentMethods.forEach(paymentMethod => {
        let buttonCheckBox = $(`#auto-complete-${paymentMethod}`);


        $(buttonCheckBox).click(function(e) {

            const isChecked = !($(buttonCheckBox).prop('checked'));

            const form = $(buttonCheckBox).closest('form');

            if (isChecked) {
                switch (paymentMethod) {
                    case 'billet':
                        addBillet()
                        break;
                    case 'card':
                        addCard()
                        break;
                    case 'pix':
                        addPix()
                        break;
                    case 'OF':
                        addOF()
                        break;
                    default:
                        break;
                }
            } else {
                form.find('input:not([type="submit"]), textarea, select').val('');
                form.find('input:not([type="submit"]), textarea, select').closest('.col-12').removeClass('d-none').fadeIn(400);
            }
        });
    });

}