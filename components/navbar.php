<?php
    if(isset($_SESSION["userSession"])) {
        $avatar = "data:image/jpeg;base64," . base64_encode($_SESSION["userSession"]["avatar"]);
        $name = $_SESSION["userSession"]["name"];
        $signUp = "";
        $sessionAction = "<form method = 'post'><input type = 'submit' name = 'signOut' value = 'Wyloguj się' class = 'userSessionBtn'></form>";
    }
    else {
        $avatar = "../resources/profile.svg";
        $name = "Nie jesteś zalogowany(a)";
        $signUp = "<a href = 'signUp.php' class = 'userSessionBtn'>Utwórz konto</a>";
        $sessionAction = "<a href = 'signIn.php' class = 'userSessionBtn'>Zaloguj się</a>";
    }
?>
<nav>
    <div id = "userPanel">
        <span id = "loggedUser">
            <img src = "<?php echo $avatar ?>" alt = "Awatar" id = "loggedUser_avatar">
            <span id = "loggedUser_name"><?php echo $name ?></span>
        </span>
        <?php echo $signUp ?>
        <?php echo $sessionAction ?>
    </div>
    <ul>
        <li><a href = "">Strona główna</a></li>
        <li><a href = "">Aktualności</a></li>
        <li><a href = "">Katalog</a></li>
        <li><a href = "">O mnie</a></li>
    </ul>
</nav>