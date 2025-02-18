function openSettings(event) {
    let settingsMenu = document.getElementById("settings");

    // Toggle visibilitas
    if (settingsMenu.style.display === "flex") {
        settingsMenu.style.display = "none";
    } else {
        settingsMenu.style.display = "flex";
    }

    // Biar klik di luar menu nutup settings
    document.addEventListener("click", function closeMenu(e) {
        if (!settingsMenu.contains(e.target) && !event.target.closest("button")) {
            settingsMenu.style.display = "none";
            document.removeEventListener("click", closeMenu);
        }
    });
}

function openModal() {
    document.getElementById("modal").style.display = "flex";
}
function closeModal() {
    document.getElementById("modal").style.display = "none";
}

