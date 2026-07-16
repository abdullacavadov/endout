document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("errorModal");
    const modalMsg = document.getElementById("modalMessage");
    const okBtn = document.getElementById("modalOk");

    function showError(message) {

        return new Promise((resolve) => {

            modalMsg.innerText = message || "Naməlum xəta baş verdi";
            modal.style.display = "flex";

            function close(result = false) {
                modal.style.display = "none";

                okBtn.removeEventListener("click", okHandler);
                modal.removeEventListener("click", outsideHandler);

                resolve(result);
            }

            function okHandler() {
                close(true);
            }

            function outsideHandler(e) {
                if (e.target === modal) {
                    close(false);
                }
            }

            okBtn.addEventListener("click", okHandler);
            modal.addEventListener("click", outsideHandler);

        });
    }

    window.showError = showError;

});