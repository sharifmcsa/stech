// IT Care Website JavaScript

document.addEventListener("DOMContentLoaded", function () {

    // Mobile Menu / Navigation (if available)
    const menuButton = document.querySelector(".menu-btn");
    const navigation = document.querySelector(".navigation");

    if (menuButton && navigation) {
        menuButton.addEventListener("click", function () {
            navigation.classList.toggle("active");
        });
    }

    // Service Request Form
    const requestForm = document.querySelector(".request-form");

    if (requestForm) {
        requestForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const name = requestForm.querySelector(
                'input[placeholder="Your Full Name"]'
            ).value.trim();

            const mobile = requestForm.querySelector(
                'input[placeholder="Mobile Number"]'
            ).value.trim();

            const address = requestForm.querySelector(
                'input[placeholder="Your Address"]'
            ).value.trim();

            const service = requestForm.querySelector("select").value;

            const problem = requestForm.querySelector("textarea").value.trim();

            if (!name || !mobile || !address || !service || !problem) {
                alert("Please fill in all fields.");
                return;
            }

            if (!/^[0-9+\-\s]{10,15}$/.test(mobile)) {
                alert("Please enter a valid mobile number.");
                return;
            }

            alert(
                "Thank you, " + name +
                "! Your service request has been received."
            );

            requestForm.reset();
        });
    }

    // Smooth Scrolling
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener("click", function (event) {
            const target = document.querySelector(
                this.getAttribute("href")
            );

            if (target) {
                event.preventDefault();

                target.scrollIntoView({
                    behavior: "smooth"
                });
            }
        });
    });

});