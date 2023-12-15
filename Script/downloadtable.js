// download-table.js
function downloadtable() {
    // Hide the download button before capturing the image
    document.getElementById('demo').style.display = 'none';

    var node = document.getElementById('mycard');

    domtoimage.toPng(node)
        .then(function (dataUrl) {
            // Create an image element
            var img = new Image();

            // Set the source of the image to the captured data URL
            img.src = dataUrl;

            // Attach an onload event to the image
            img.onload = function () {
                // Show the download button after the image is loaded
                document.getElementById('demo').style.display = 'block';

                // Trigger the download after the image is loaded
                downloadURI(dataUrl, "dhvsu-card.png");
            };
        })
        .catch(function (error) {
            console.error('Oops, something went wrong', error);

            // Ensure the button is shown even if there's an error
            document.getElementById('demo').style.display = 'block';
        });
}
