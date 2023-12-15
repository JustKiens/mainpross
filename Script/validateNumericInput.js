function validateNumericInput(inputField) {
    // para numbers lang ang ma input
    inputField.value = inputField.value.replace(/\D/g, '');
}
