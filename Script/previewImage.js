function previewImage() {
    // for image preview
    var input = document.getElementById('imageInput');
    var preview = document.getElementById('imagePreview');

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = ''; // Clear the image if no file is selected
    }
}