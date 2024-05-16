function closeImage() {
    var enlagedImageView = document.getElementById("enlargedImageView");
    enlargedImageView.style.display = "none";
}

function enlargeImage(imageSrc) {
    var bg = document.getElementById("enlargedImageView");
    bg.style.display = "block";
    let img = document.getElementById("enlargedImage");
    img.src = imageSrc;
}

/////////////////////////////////////////////////////////////////////

function activateLoginBox() {
    let infoBox = document.getElementById("loginInfoBox");
    let icon = document.getElementById("loginBoxIcon");
    if (infoBox.style.visibility == "visible") {
        infoBox.style.visibility = "hidden";
        infoBox.style.right = "-280px";
        icon.style.right = "0px";
    } else {
        infoBox.style.visibility = "visible";
        infoBox.style.right = "0px";
        icon.style.right = "280px";
    }
}

/////////////////////////////////////////////////////////////////////

var images = 1;

function addImage() {

    var html =  `<tr>
                    <!-- Input fields for image data -->
                    <td data-label="Kuvatiedosto (.jpg/.png/.jpeg):" scope="row"><input type="file" name="images[${images}]" required></td>
                    <!---->
                    <!-- Remove images button -->
                    <td id="buttonBox"><input type="button" class="imageButton" onclick="removeImage.call(this)" id="removeImageButton" name="removeImageButton" value="Poista"></td>
                    <!---->
                </tr>
                `;

    var maxImages = 3;

    var table = document.getElementById("imagesForm");
    if (images <= maxImages) {
        table.insertAdjacentHTML("beforeend",html);
        images++;
    }
}

var removeImage = function () {
    this.parentNode.parentNode.remove();
    images--;
}
