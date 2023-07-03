var colour_dropdown = document.getElementById("colour_dropdown");
var arrow_down = document.getElementById("turn_arrow");

function activate_dropdown() {
	colour_dropdown.classList.toggle("show_dropdown");
	arrow_down.classList.toggle("turn_arrow");
}

window.onclick = function(misclick) {
	if (!misclick.target.matches(".dropdown")) {
		if (colour_dropdown.classList.contains("show_dropdown")) {
			colour_dropdown.classList.remove("show_dropdown");
			arrow_down.classList.remove("turn_arrow");
		}
	}
}