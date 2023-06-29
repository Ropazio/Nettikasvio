var colour_dropdown = document.getElementById("colour_dropdown");

function activate_dropdown() {
	colour_dropdown.classList.toggle("show_dropdown");
	colour_dropdown.classList.toggle("arrow_down");
}

window.onclick = function(misclick) {
	if (!misclick.target.matches(".dropdown")) {
		if (colour_dropdown.classList.contains("show_dropdown")) {
			colour_dropdown.classList.remove("show_dropdown");
		}
	}
}