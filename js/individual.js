document.addEventListener("DOMContentLoaded", function () {
    const Form1 = document.getElementById("Form1");
    const Form2 = document.getElementById("Form2");
    const Form3 = document.getElementById("Form3");
    const Form4 = document.getElementById("Form4");
    const Form5 = document.getElementById("Form5");
    const Teachers = document.getElementById("Teachers");

    const formSection = document.getElementById("userListContainer");
    const searchButton = document.getElementById("searchButton");


    let userType = "";

    searchButton.addEventListener("click", () => {
        const searchQuery = document.getElementById("search_query").value;
        const query = document.querySelector("#query_selection").value;
    
        if (!isValidSearchQuery(userType, query)) {
          alert("Invalid search query for the selected user type.");
          return;
        }
    
        searchUserData(userType, searchQuery, query);
      });

    Form1.addEventListener("click", () => {
        userType = "Form1";
        loadUserData(userType);
    });

    Form2.addEventListener("click", () => {
        userType = "Form2";
        loadUserData(userType);
    });

    Form3.addEventListener("click", () => {
        userType = "Form3";
        loadUserData(userType);
    });

    Form4.addEventListener("click", () => {
        userType = "Form4";
        loadUserData(userType);
    });

    Form5.addEventListener("click", () => {
        userType = "Form5";
        loadUserData(userType);
    });

    Teachers.addEventListener("click", () => {
        userType = "Teachers";
        loadUserData(userType);
    });

    

    function loadUserData(userType) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/SPAS/pages/Analysis/includes/individual.inc.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                formSection.innerHTML = xhr.responseText;
            }
        };
        xhr.send("name=" + userType);
    }

    function searchUserData(userType, searchQuery, query) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/SPAS/pages/Analysis/includes/individual.inc.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4 && xhr.status === 200) {
            userListContainer.innerHTML = xhr.responseText;
          }
        };
        xhr.send(
          "name=" + userType + "&search=true&query=" + query + "&search_query=" + searchQuery
        );
    }

    function isValidSearchQuery(userType, query) {
        switch (userType) {
          case "Teachers":
            return query !== "class" && query !== "id";
          default:
            return true;
        }
    }

    $('.details-button').click(function () {
        var id = $(this).find('input[name="id"]').val();
        var name = $(this).find('input[name="name"]').val();
        var table = $(this).find('input[name="table_name"]').val();

        $.ajax({
            url: 'includes/individual.det.php', 
            type: 'POST',
            data: { id: id, name: name, table_name: table },
            success: function (response) {
                $('#details').html(response);
                $('#detailsModal').modal('show');
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

});
