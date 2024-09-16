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

function addScrollIcons() {
    var html = `<div class="rightScrollIcon">
                    <button onclick="rightScroll.call(this)">></button>
                </div>
                <div class="leftScrollIcon">
                    <button onclick="leftScroll.call(this)"><</button>
                </div>
                `;
    var rows = document.getElementsByClassName("gallery");
    for (const row of rows) {
        var images = row.getElementsByTagName("img");
        if (images.length > 3) {
            row.insertAdjacentHTML("beforeend", html);
        }
    }
}

const scrollLength = 300;
var scrolled = 0;

var rightScroll = function () {
    this.parentNode.parentNode.scrollLeft += scrollLength;
    scrolled += scrollLength;
    this.parentNode.nextElementSibling.childNodes[1].style.display = "block";
}

var leftScroll = function () {
    this.parentNode.parentNode.scrollLeft -= scrollLength;
    scrolled -= scrollLength;
    if (scrolled == 0) {
        this.style.display = "none";
    }
}

var removeRightScroll = function () {
    if ((this.scrollWidth - (this.scrollLeft + this.offsetWidth)) <= 10) {
        let child = this.querySelector('.rightScrollIcon');
        child.style.display = "none";
    }
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

document.addEventListener('click', function(event) {
    let infoBox = document.getElementById("loginInfoBox");
    let icon = document.getElementById("loginBoxIcon");
    var outsideInfoBox = !(infoBox.contains(event.target) || icon.contains(event.target));
    if (outsideInfoBox) {
        infoBox.style.visibility = "hidden";
        infoBox.style.right = "-280px";
        icon.style.right = "0px";
    }
});

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

    var maxImages = 10;

    var table = document.getElementById("imagesForm");
    if (images <= maxImages) {
        table.insertAdjacentHTML("beforeend", html);
        images++;
    }
}

var removeImage = function () {
    this.parentNode.parentNode.remove();
    images--;
}
