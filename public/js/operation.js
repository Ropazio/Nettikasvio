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
