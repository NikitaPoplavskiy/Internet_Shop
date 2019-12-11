<?php include ROOT . '/views/layouts/header.php'; ?>

<section>
    <div class="container">
        <div class="row">             
            <div class = "col-sm-4 col-sm-offset-4 padding-right">
            <?php if ($result): ?>
                <h1>Заказ оформлен!</h1>
            <?php else: ?> 
            <?php if (isset($errors) && is_array($errors)): ?>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li> - <?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
            <?php endif; ?>
                <div class="signup-form"><!--sign up form-->
                    <h2>Оформление заказа</h2>
                    <p>Вы заказали столько товара: <?php echo $totalQuantity; ?> На сумму <?php echo $totalPrice; ?>  </p>
                    <form action="#" method="post">
                        <input type="text" name="name" placeholder="Имя" value = "<?php echo $userName?>"/>
                        <input type="text" name="phone" placeholder="Ваш номер телефона" value = "<?php echo $userPhone?>"/>
                        <input type="text" name="comment" placeholder="Комментарий к заказу" value = "<?php echo $userComment?>"/>
                        <input type="submit" name="submit" class="btn btn-default" value="Оформить"/>
                    </form>
                </div><!--/sign up form-->                        
            <br/>
            <br/>            
            </div>
            <?php endif; ?>
        </div>        
    </div>
</section>

<?php include ROOT . '/views/layouts/footer.php'; ?>