<?php include('pages/index/header.php'); ?>
<!-- Content -->
<div class="template-content">

	<!-- Main -->
	<div class="template-content-section template-main" style="padding-top: 0px">

		<!-- Header and subheader -->
		<div class="template-component-header-subheader">
			<h2>Contact Us</h2>
			<h6></h6>
			<div></div>
		</div>


		<!-- Feature -->
		<div class="template-component-feature template-component-feature-style-1 template-component-feature-position-left template-component-feature-size-medium">

			<!-- Layout 33x33x33 -->
			<ul class="template-layout-33x33x33 template-clear-fix">

				<!-- Left column -->
				<li class="template-layout-column-left">
					<div class="template-icon-feature template-icon-feature-name-envelope-alt"></div>
					<h5>Address</h5>
					<p>
						DreamSpace Academy<br />
						7A, Saravana Road, Kallady<br />
						Batticaloa, 30000<br />
						Sri Lanka
					</p>
				</li>

				<!-- Center column -->
				<li class="template-layout-column-center">
					<div class="template-icon-feature template-icon-feature-name-mobile-alt"></div>
					<h5>Contact</h5>
					<p>
						Phone: +94 (0) 65 222 6525<br />
						Email: <a href="mailto:info@dreamspace.academy">info@dreamspace.academy</a>
					</p>
				</li>

				<!-- Right column -->
				<li class="template-layout-column-right template-margin-bottom-5">
					<div class="template-icon-feature template-icon-feature-name-clock-alt"></div>
					<h5>Opening Hours</h5>
					<p>
						Monday – Saturday<br />
						9.00 am – 6.00 pm<br />
					</p>
				</li>

			</ul>

		</div>

		<!-- Contact form -->
<?php 
 $soucre= "https://docs.google.com/forms/d/e/1FAIpQLSeHlS5ahdvqTF9k2utdVqZ1gxTrABzQu1MLOZJKHhkN4B43Lg/viewform?embedded=true";
 $soucre = base64_encode($soucre);
 
 ?>
		<center><iframe src=<?php echo base64_decode($soucre); ?>  width="100%" height="520" frameborder="1" marginheight="0" marginwidth="0">Loading…</iframe>
		</center>
	</div>
	

	<div class="template-content-section template-padding-top-reset template-padding-bottom-reset">

		<!-- Google map -->


		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.6915723382!2d81.70887991427057!3d7.716204910290341!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3afacd9e33341515%3A0xa716d85248eadbf9!2sDreamSpace+Academy!5e0!3m2!1sen!2slk!4v1559904172390!5m2!1sen!2slk"
		  width="100%" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>

	</div>

</div>

<?php include('pages/index/footer.php'); ?>
