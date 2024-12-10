import { addBillet } from './billet.mjs';
import { addCard } from './card.mjs';
import { addPix } from './pix.mjs';
import { addOF } from './openFinance.mjs';
import { handlerValuesInputs } from './handlerValuesInputs.mjs';

export async function addAutoCompleteForms() {
    addBillet();
    addCard();
    addPix();
    addOF();
    handlerValuesInputs();
}