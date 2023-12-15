 function validateForm() {
        var name = document.getElementsByName('name')[0].value;
        var grade = document.getElementsByName('grade')[0].value;
        var dob = document.getElementsByName('dob')[0].value;
        var address = document.getElementsByName('address')[0].value;
        var email = document.getElementsByName('email')[0].value;
        var exp_date = document.getElementsByName('exp_date')[0].value;
        var id_no = document.getElementsByName('id_no')[0].value;
        var phone = document.getElementsByName('phone')[0].value;
        var signature = document.getElementsByName('signature')[0].value;
        var imageInput = document.getElementById('imageInput');
        var generalError = document.getElementById('generalError');
        var imageError = document.getElementById('imageError');

        // Reset error messages
        generalError.style.display = 'none';
        imageError.style.display = 'none';

        if (name && grade !== 'Choose...' && dob && address && email && exp_date && id_no && phone && signature) {
            // Check if an image is selected
            if (imageInput.files.length === 0) {
                imageError.style.display = 'block';
            } else {
                // All fields are filled, submit the form
                document.getElementById('myForm').submit();
            }
        } else {
            // Display a general error message if any required field is empty
            generalError.style.display = 'block';
        }
    }
    document.getElementById('addCardButton').addEventListener('click', validateForm);
