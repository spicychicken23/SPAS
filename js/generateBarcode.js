document.addEventListener("DOMContentLoaded", function () {
    const Bulk = document.getElementById("Bulk");
    const Manual = document.getElementById("Manual");

    const generateContainer = document.getElementById("generateContainer");
    const bulkSection = document.getElementById("bulkSection");
    const manualSection = document.getElementById("manualSection");
    const downloadSection = document.getElementById("downloadSection");

    Bulk.addEventListener("click", () => {
        userType = "bulk";
        showSection("bulk");
    });

    Manual.addEventListener("click", () => {
        userType = "manual";
        showSection("manual");
    });

    function showSection(section) {
        if (section === "bulk") {
            bulkSection.style.display = "block";
            manualSection.style.display = "none";
            downloadSection.style.display = "block";
        } else {
            bulkSection.style.display = "none";
            manualSection.style.display = "block";
            downloadSection.style.display = "none";
        }
    }

    function saveUserList() {
        var userListContent = generateContainer.innerHTML;

        var newWindow = window.open("", "_blank");
        newWindow.document.write('<div id="userListContainer">' + userListContent + "</div>");
        newWindow.document.close();

        newWindow.print();
    }
    
});
