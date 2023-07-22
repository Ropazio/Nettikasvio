var filter_dropdowns = document.getElementsByClassName("filter_dropdown");
var arrows_down = document.getElementsByClassName("arrow_right");

function activate_dropdown(dropdown_name) {
	if (dropdown_name == 0) {
		filter_dropdowns.item(0).classList.toggle("show_dropdown");
		arrows_down.item(0).classList.toggle("turn_arrow");
	}
	if (dropdown_name == 1) {
		filter_dropdowns.item(1).classList.toggle("show_dropdown");
		arrows_down.item(1).classList.toggle("turn_arrow");
	}
	
}

window.onclick = function(misclick) {
	if (!misclick.target.matches(".dropdown")) {
		for (let i = 0; filter_dropdowns.length; i++) {
			console.log(filter_dropdowns.item(i));
			if (filter_dropdowns.item(i).classList.contains("show_dropdown")) {
				filter_dropdowns.item(i).classList.remove("show_dropdown");
				arrows_down.item(i).classList.remove("turn_arrow");
			}
		}
	}
}