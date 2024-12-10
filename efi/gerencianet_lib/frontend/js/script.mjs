const $ = window.jQuery;
import { handlerMenuClasses } from "./components/sideBar/handlerClasses.mjs";
import { addAnimationToIconsHover } from './animations/icons/sideBar.mjs';
import { applyMasks } from "./components/form/masks/inputMasks.mjs";
import { addValidationForms } from './components/form/validation/addValidationForms.mjs';
import { addAutoCompleteForms } from './components/form/autoComplete/addCustomerValues.mjs';

import { handlerShowMethods } from './components/form/paymentMethods/handlerShowMethods/handlerShowMethods.mjs';
import { addTotalForPaymentMethods } from "./components/form/total/addTotalForPaymentMethods.mjs";



window.loadScriptsModalEfi = () => {

    $('input, textarea, select').addClass('focus');
    addTotalForPaymentMethods();
    applyMasks($('input[data-mask]'));
    handlerMenuClasses();
    addAnimationToIconsHover();
    addValidationForms();
    addAutoCompleteForms();
    handlerShowMethods();



}