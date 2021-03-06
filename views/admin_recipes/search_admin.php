<?php include ROOT . "/views/layouts/header_admin.php"; ?>
<section>
	<div style="display:flex; justify-content:center;">
		<div class="col-sm-5 padding-right">
			<div class="features_items">
			</div>
			<br>
			<h2 class="title text-center">Найдены такие рецепты</h2>
			<div class="col-sm-3_">
				<div class="search_box" style="display:flex;">
					<form class="container" id="search" action="/admin/search/page-1" method="post" enctype="multipart/form-data">
						<div class="row" style="display: flex; justify-content: space-around">
							<input class="col-md-4 col-sm-6 col-xs-6" id="search_box" type="text" placeholder="Search" name="search" />
							<select class="col-md-4 col-sm-6 col-xs-6" name="select">
								<option value="name">По имени</option>
								<option value="email">По email</option>
							</select>
						</div>
						<!--button type="submit"><i class="fa fa-search"></i></button-->
					</form>
				</div>
			</div>
			<?php if (count($foundRecipes) == 0) : ?>
				<h1>Ничего не найдено</h1>
			<?php else : ?>
				<div style="overflow: auto;">
					<table class="table-bordered table-striped table">
						<tr>
							<th>Id рецепта</th>
							<th>Имя пользователя</th>
							<th>Email</th>
							<th>Дата добавления</th>
							<th>Статус</th>
						</tr>
						<?php foreach ($foundRecipes as $recipe) : ?>
							<tr>
								<td><a data-toggle="modal" data-target="#exampleModal" href="#" class="recipe_image" data-id="<?php echo $recipe["id"]; ?>"><?php echo $recipe["id"]; ?></a></td>
								<td>
									<?php echo $recipe["user_name"]; ?>
								</td>
								<td>
									<?php echo $recipe["user_email"]; ?>
								</td>
								<td>
									<?php echo $recipe["date_created"]; ?>
								</td>
								<form action="" method="post">
									<input type="hidden" name="recipe_id" value="<?php echo $recipe["id"]; ?>">
									<td>
										<select name="status" onchange="this.form.submit()">
											<option value="0" <?php echo $recipe["status"] == 0 ? "selected" : "";  ?>>Рецепт в обработке</option>
											<option value="1" <?php echo $recipe["status"] == 1 ? "selected" : "";  ?>>Рецепт действителен</option>
											<option value="2" <?php echo $recipe["status"] == 2 ? "selected" : "";  ?>>Рецепт не действителен</option>
										</select>
									</td>
								</form>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</table>
				</div>
				<div style="display:flex; justify-content:center;">
					<?php echo $pagination->get(); ?>
				</div>
		</div>
		<!--MODAL-->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Рецепт</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" style="overflow-y:auto;">
						<img id="recipe_image" src="" alt="">
					</div>
					<!--div class="modal-footer">
					<button type="button" class="btn btn-primary" style="margin-top: 0px" data-dismiss="modal" id="confirm-delete" data-id="">Да</button>
				</div-->
				</div>
			</div>
		</div>
	</div>
</section>
<?php include ROOT . "/views/layouts/footer_admin.php"; ?>