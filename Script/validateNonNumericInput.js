function validateNonNumericInput(inputField) {
    // para letters lang ang ma input
    inputField.value = inputField.value.replace(/[0-9]/g, '');
}