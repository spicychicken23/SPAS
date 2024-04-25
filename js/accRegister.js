document.addEventListener("DOMContentLoaded", function() {
  const roleDropdown = document.getElementById("role");
  const regContent = document.getElementById("register");

  roleDropdown.addEventListener("change", function () {
    if (roleDropdown.value == "Students") {
      regContent.innerHTML = `
        <div class="mb-3 row">
          <label for="email" class = "col-sm-2 col-form-label">Email:</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="name" class="col-sm-2 col-form-label">Name:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="class" class="col-sm-2 col-form-label">Class:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="class" name="class" required>
          </div>
        </div>
          `;
    }
    else if (roleDropdown.value == "Teachers") {
      regContent.innerHTML = `
        <div class="mb-3 row">
          <label for="email" class = "col-sm-2 col-form-label">Email:</label>
           <div class="col-sm-10">
             <input type="email" class="form-control" id="email" name="email" required>
           </div>
         </div>

         <div class="mb-3 row">
           <label for="name" class="col-sm-2 col-form-label">Name:</label>
           <div class="col-sm-10">
             <input type="text" class="form-control" id="name" name="name" required>
           </div>
         </div>
      `;    
    }
    else if (roleDropdown.value == "Admin") {
      regContent.innerHTML = `
        <div class="mb-3 row">
          <label for="name" class="col-sm-2 col-form-label">Name:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
        </div>

        <div class="mb-3 row">
         <label for="username" class="col-sm-2 col-form-label">Username:</label>
         <div class="col-sm-10">
           <input type="text" class="form-control" id="username" name="username" required>
         </div>
        </div>

        <div class="mb-3 row">
        <label for="password" class="col-sm-2 col-form-label">password:</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
       </div>
      `;
    
      const usernameInput = document.getElementById("username");
      usernameInput.addEventListener("input", function () {
        const usernameValue = usernameInput.value;
        if (!usernameValue.startsWith("Admin")) {
          usernameInput.value = "Admin" + usernameValue;
        }
      });

    }
    else {
      regContent.innerHTML = `
        <div class="mb-3 row">
          <label for="name" class="col-sm-2 col-form-label">Name:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
        </div>

        <div class="mb-3 row">
          <label for="standard" class="col-sm-2 col-form-label">Standard:</label>
          <div class="col-sm-10">
           <input type="text" class="form-control" id="standard" name="standard" required>
          </div>
        </div>
      `;    
    }
  });
});