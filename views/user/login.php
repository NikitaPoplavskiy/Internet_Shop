<?php include ROOT . '/views/layouts/header.php'; ?>

<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4 padding-right">
                <?php if ($result) : ?>
                    <h1>Вы зарегистированы!</h1>
                <?php else : ?>
                    <?php if (isset($errors) && is_array($errors)) : ?>
                        <ul>
                            <?php foreach ($errors as $error) : ?>
                                <li> - <?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="signup-form">
                        <!--sign up form-->
                        <h2>Вход на сайт</h2>
                        <form action="#" method="post">
                            <input type="email" name="email" placeholder="E-mail" value="<?php echo $email ?>" />
                            <div class="password_field">
                                <input id="password_field" type="password" name="password" placeholder="Пароль" value="<?php echo $password ?>" />
                                <i id="show_password" class="fa fa-eye fa-2x"></i>
                            </div>
                            <input type="submit" name="submit" class="btn btn-default" value="Вход" />
                        </form>
                    </div>
                    <!--/sign up form-->
                    <br />
                    <br />
            </div>
        <?php endif; ?>
        </div>
    </div>
</section>

<?php include ROOT . '/views/layouts/footer.php'; ?>