<?php if ($_SERVER["REQUEST_METHOD"]) { ?>

<script src="../../js/accRegister.js"></script>

<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Add New Users</h5>
      </div>
      <div class="modal-body">
        <div class="container">
          <form action="includes/accManage.add.php" method="post">
            <div class="mb-3 row">
              <label for="role" class="col-sm-2 col-form-label">Role:</label>
              <div class="col-sm-10">
                <select class="form-select" id="role" name="role" required>
                  <option value="" disabled selected>Choose Which User</option>
                  <option value="Students">Students</option>
                  <option value="Teachers">Teachers</option>
                  <option value="Admin">Admin</option>
                  <option value="Class">Class</option>
                </select>
              </div>
            </div>

            <div id="register">
            </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <input type="submit" class="btn btn-secondary" value="Submit" name="submit">
      </div>
      </form>
    </div>
  </div>
</div>

<?php } ?>
