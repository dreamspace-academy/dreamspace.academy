<!-- header-menu -->
<?php include('../master-page/header.php') ?>

<!-- Content -->
<div class="template-content">

	<!-- Header and subheader -->
	<div class="template-component-header-subheader">
		<h2>Maker Education</h2>
		<h6></h6>
		<div></div>
	</div>

	<section id="electronics-lab">
		<?php include('electronics-lab.php'); ?>
	</section>

	<section id="software-lab">
		<?php include('software-lab.php'); ?>
	</section>

	<section id="mechanics-lab">
		<?php include('mechanics-lab.php'); ?>
	</section>

	<section id="business-lab">
		<?php include('business-lab.php'); ?>
	</section>

	<section id="design-lab">
		<?php include('design-lab.php'); ?>
	</section>

	<section id="art-lab">
		<?php include('art-lab.php'); ?>
	</section>

	<section id="wet-lab">
		<?php include('wet-lab.php'); ?>
	</section>

	<?php include('enroll.php'); ?>

</div>

<!-- footer-menu -->
<?php include('../master-page/footer.php') ?>
