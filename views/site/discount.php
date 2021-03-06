<?php include ROOT . "/views/layouts/header.php"; ?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				<div class="left-sidebar">
					<h2>Категория</h2>
					<div class="panel-group category-products" id="accordian">
						<!--category-productsr-->
						<?php foreach ($categories as $categoryItem) : ?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title"><a href="/category/<?php echo $categoryItem["id"]; ?>">
											<?php echo $categoryItem["name"]; ?>
										</a>
									</h4>
								</div>
							</div>
						<?php endforeach; ?>
					</div>				

				</div>
			</div>

			<div class="col-sm-9 padding-right">
				<div class="features_items">
					<!--features_items-->
					<h2 class="title text-center">Товары со скидкой</h2>
					<?php foreach ($discounts as $discount) : ?>
						<div class="col-sm-4">
							<div class="product-image-wrapper" style="display: flex; justify-content: center;">
								<div class="single-products">
									<div class="productinfo text-center">
										<a href="/product/<?php echo $discount["id"]; ?>">
											<img src="<?php echo Product::getProductImage($discount["id"]); ?>" alt="" />
											<h2>
												<?php echo $discount["discount_price"]; ?> грн
											</h2>
											<p>
												<?php echo $discount["name"]; ?>
											</p>
										</a>										
										<a href="#" class="btn btn-default add-to-cart" data-id=<?php echo $discount["id"]; ?>><i class="fa fa-shopping-cart"></i>Добавить в корзину</a>
									</div>
									<?php if ($discount["is_new"]) : ?>
										<img src="/template/images/home/new.png" class="new" alt="" />
									<?php endif; ?>									
								</div>								
							</div>
						</div>
					<?php endforeach; ?>					
				</div>
			</div>
		</div>
</section>
<?php include ROOT . "/views/layouts/footer.php"; ?>