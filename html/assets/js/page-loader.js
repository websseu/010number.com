export function initPageLoader() {
    window.addEventListener("load", () => {
        const loader = document.getElementById("page-loader");

        document.body.classList.add("page-loaded");

        if (loader) {
            loader.classList.add("hidden");

            setTimeout(() => {
                if (loader && loader.parentNode) {
                    loader.remove();
                }
            }, 1000);
        }
    });
}
