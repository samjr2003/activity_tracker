document.addEventListener("DOMContentLoaded", () => {

    trackActivity("Page Visit", window.location.pathname);

    document.addEventListener("click", function (e) {
        let el = e.target.closest("button, a");
        if (el) {
            trackActivity("BUTTON_CLICK", el.innerText.trim() || "button");
        }
    });
});

function trackActivity(type, page) {
    fetch("../app/controllers/ActivityController.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            activity_type: type,
            page_name: page
        })
    });
}
