<h3>Вход</h3>
<form action="/account/login" method="POST">
    <p>Логин</p>
    <p><?= $age ?></p>
    <p><?= 'id = ' . $id ?></p>
    <input type="text" name="login" <?php if(isset($last)) echo 'value=\'' . $last . '\'' ?>>
    <p>Пароль</p>
    <input type="text" name="password">
    <input type="submit" name="enter" value="Вход">
</form>