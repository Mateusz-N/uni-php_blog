<!DOCTYPE html>
<html>
    <head>
        <title>console.blog()</title>
        <link rel = "stylesheet" href = "./style.css" type = "text/css">
    </head>
    <body>
        <main id = "contentPreview">
            <form action = "index.php" method = "post" id = "signinForm">
                <div class = "formField">
                    <label for = "userName">
                        <span class = "required">*&nbsp;</span>Nazwa użytkownika:&nbsp;
                    </label>
                    <input name = "userName" placeholder = "JohnDoe2" required>
                </div>
                <div class = "formField">
                    <label for = "password">
                        <span class = "required">*&nbsp;</span>Hasło:&nbsp;
                    </label>
                    <input type = "password" name = "password" placeholder = "***** ***" required>
                </div>
                <div id = "formButtons">
                    <input type = "reset" value = "Wyczyść" class = "button">
                    <input type = "submit" name = "signIn" value = "Zaloguj" class = "button">
                </div>
            </form>
        </main>
        <footer>
            <p>Uwaga! Moduł kont użytkowników w trakcie budowy (jak zresztą widać)!</p>
        </footer>
    </body>
</html>