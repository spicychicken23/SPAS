document.addEventListener("DOMContentLoaded", function () {
    const studentsBtn = document.getElementById("StudentBtn");
    const teachersBtn = document.getElementById("TeacherBtn");
    const adminsBtn = document.getElementById("AdminBtn");
    const classesBtn = document.getElementById("ClassBtn");
    const userTypeForm = document.getElementById("userTypeForm");
    const userListContainer = document.getElementById("userListContainer");
    const searchButton = document.getElementById("searchButton");
    const deleteInput = document.getElementById("deleteValue");
    const roleInput = document.getElementById("inputRole");
  
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
  
    studentsBtn.addEventListener("click", () => {
      userType = "students";
      setDeleteInputValue("students");
    });
  
    teachersBtn.addEventListener("click", () => {
      userType = "teachers";
      setDeleteInputValue("teachers");
    });
  
    adminsBtn.addEventListener("click", () => {
      userType = "admin";
      setDeleteInputValue("Admin");
    });
  
    classesBtn.addEventListener("click", () => {
      userType = "class";
      setDeleteInputValue("class");
    });
  
    function setDeleteInputValue(value) {
      deleteInput.value = value;
      roleInput.value = value;
    }
  
    function searchUserData(userType, searchQuery, query) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/SPAS/pages/admin/includes/accManage.inc.php", true);
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
  
    userTypeForm.addEventListener("click", function (event) {
      if (event.target.tagName === "BUTTON") {
        const userType = event.target.value;
        loadUserData(userType);
      }
    });
  
    function loadUserData(userType) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "/SPAS/pages/admin/includes/accManage.inc.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          userListContainer.innerHTML = xhr.responseText;
        }
      };
      xhr.send("name=" + userType);
    }
  
    function isValidSearchQuery(userType, query) {
      switch (userType) {
        case "students":
          return query !== "standard";
        case "teachers":
          return query !== "standard" && query !== "class";
        case "admin":
          return query !== "standard" && query !== "class" && query !== "email";
        case "class":
          return query !== "username" && query !== "email" && query !== "class";
        default:
          return true;
      }
    }
  
    function saveUserList() {
      var userListContent = userListContainer.innerHTML;
  
      var newWindow = window.open("", "_blank");
      newWindow.document.write('<html><head><title>User List</title></head><body>');
      newWindow.document.write('<div id="userListContainer" class = "bg-light rounded">' + userListContent + "</div>");
      newWindow.document.write("</body></html>");
  
      newWindow.document.close();
  
      newWindow.print();
    }
  
    const saveUserListButton = document.getElementById("printBtn");
    saveUserListButton.addEventListener("click", saveUserList);
  });
  