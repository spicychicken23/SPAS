<?php if (isset($_SESSION['auth'])) { ?>


<footer class="header fixed-bottom text-LIGHT text-center p-2 mt-2">
    <div class="container">
        Copyright © 2023 4MG
    </div>    
</footer>


<?php } ?>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="../../js/app.js"></script>

<?php if(isset($_SESSION['auth'])) { ?> 

<script src="../../js/check_inactive.js"></script>
    
<?php } ?>


<?php

if (isset($_SESSION['ERRORS']))
    $_SESSION['ERRORS'] = NULL;
if (isset($_SESSION['STATUS']))
    $_SESSION['STATUS'] = NULL;

?>