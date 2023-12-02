<?php
if (isset($_GET['cod'])) :
    $cod = $_GET['cod'];
    include('include/configpdo.php');
    try {
        $query = "SELECT `nome`,`cognome` FROM `user` WHERE `controllo` =:cod";
        $stmt = $db->prepare($query);
        $stmt->bindParam('cod', $cod, PDO::PARAM_STR);
        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $nome = $row['nome'];
            $cognome = $row['cognome'];
        } else {
            header('location: 404.php');
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
else :
    header('location: 404.php');
endif;
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4">

    <!-- Title -->
    <title> Valex - Premium dashboard ui bootstrap rwd admin html5 template </title>

    <!-- Favicon -->
    <link rel="icon" href="admin/assets/img/brand/favicon.png" type="image/x-icon">

    <!-- Icons css -->
    <link href="admin/assets/css/icons.css" rel="stylesheet">

    <!-- Bootstrap css -->
    <link id="style" href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- style css -->
    <link href="admin/assets/css/style.css" rel="stylesheet">
    <link href="admin/assets/css/plugins.css" rel="stylesheet">

</head>

<body class="ltr main-body bg-primary-transparent error-page1 error-2 login-img">

    <!-- Page -->
    <div class="page">

        <!-- Main-error-wrapper -->
        <div class="main-error-wrapper page-h">
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Salve <?= $nome ?>, scegli della password per accedere al tuo account</p>

                    <form class="needs-validation form-signin" novalidate>

                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input class="form-control" type="password" id="loginpassword" name="loginpassword" placeholder="Password" minlength="8" required>
                            <span class="input-group-text"><i class="far fa-eye-slash" id="togglePassword"></i></span>
                            <div class="invalid-feedback">
                                Min 8 caratteri
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input class="form-control" type="password" id="loginrpassword" name="loginrpassword" placeholder="Ripeti Password" minlength="8" required>
                            <span class="input-group-text"><i class="far fa-eye-slash" id="togglePasswordRep"></i></span>
                            <div class="invalid-feedback">
                                Le password non corrispondono.
                            </div>
                        </div>
                        <!-- <input type="hidden" id="codice" name="codice" value="<?= $cod ?>"> -->


                        <div class="row">
                            <!-- /.col -->
                            <div class="col-12">
                                <div id="messaggio" class="alert alert-danger text-center" style="display: none;" role="alert">Errore</div>
                                <div id="messaggiook" class="alert alert-success text-center" style="display: none;" role="alert">Account attivato</div>
                                <button type="submit" id="attiva" class="btn btn-primary btn-block" disabled>Salva</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /Main-error-wrapper -->

    </div>
    <!-- End Page -->

    <!-- JQuery min js -->
    <script src="admin/assets/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Bundle js -->
    <script src="admin/assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="admin/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- Moment js -->
    <script src="admin/assets/plugins/moment/moment.js"></script>

    <!-- P-scroll js -->
    <script src="admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <!-- eva-icons js -->
    <script src="admin/assets/js/eva-icons.min.js"></script>

    <!-- Rating js-->
    <script src="admin/assets/plugins/ratings-2/jquery.star-rating.js"></script>
    <script src="admin/assets/plugins/ratings-2/star-rating.js"></script>

    <!--themecolor js-->
    <script src="admin/assets/js/themecolor.js"></script>

    <!-- custom js -->
    <!-- <script src="admin/assets/js/custom.js"></script> -->

    <!-- switcher-styles js -->
    <script src="admin/assets/js/swither-styles.js"></script>

</body>

</html>
<script>
    $(document).ready(function() {
        // se validazione ok attiva il bottone
        $("#loginpassword, #loginrpassword").on("keyup", function() {
            if (
                $("#loginpassword").val() != ""
            ) {
                loginrpassword.setCustomValidity(loginrpassword.value === loginpassword.value ? '' : false);
                $("#attiva").prop("disabled", false);
            } else {
                $("#attiva").prop("disabled", true);
            }
        });

        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            "use strict";
            window.addEventListener(
                "load",
                function() {
                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    var forms = document.getElementsByClassName("needs-validation");
                    // Loop over them and prevent submission
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener(
                            "submit",
                            function(event) {
                                if (form.checkValidity() === false) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                } else {
                                    event.preventDefault();
                                    //disabilito il bottone #attiva
                                    $("#attiva").prop("disabled", true);
                                    $.ajax({
                                        type: "post",
                                        url: "include/attiva.php",
                                        data: {
                                            codice: '<?= $cod ?>',
                                            password: $("#loginpassword").val()
                                        },
                                        dataType: "json",
                                        success: function(response) {
                                            if (response.status == "ok") {
                                                $("#messaggiook").show();
                                                $("#messaggio").hide();
                                                $("#attiva").hide();
                                                setTimeout(function() {
                                                    window.location.href = "admin/index.php";
                                                }, 2000);
                                            } else {
                                                $("#messaggio").show();
                                                $("#messaggiook").hide();
                                                $("#attiva").prop("disabled", false);
                                            }
                                        }
                                    });
                                }
                                form.classList.add("was-validated");
                            },
                            false
                        );
                    });
                },
                false
            );
        })();
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#loginpassword");
        const togglePasswordRep = document.querySelector("#togglePasswordRep");
        const passwordrep = document.querySelector("#loginrpassword");
        togglePassword.addEventListener("click", function() {
            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            // toggle the eye icon
            this.classList.toggle('fa-eye');
        });

        togglePasswordRep.addEventListener("click", function() {
            // toggle the type attribute
            const type = passwordrep.getAttribute("type") === "password" ? "text" : "password";
            passwordrep.setAttribute("type", type);

            // toggle the eye icon
            this.classList.toggle('fa-eye');
        });
    });
</script>