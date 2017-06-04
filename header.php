<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
	<!-- Stylesheets
	============================================= -->
	<script src="https://use.fontawesome.com/1c4863d08b.js"></script>
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/bootstrap/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/owl.carousel.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/bootstrap-select.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/magnific-popup.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.css" type="text/css" />
	<link class="skin" rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/skin-1.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/templete.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/switcher.css" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!--[if lt IE 9]>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->
	<!-- Document Title
	============================================= -->
	<title>AlphaTechSchool</title>
</head>
<?php $url= home_url(); ?>

<body class="stretched">

	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper" class="clearfix">
		

		<!-- Header
		============================================= -->
		<header id="header" class="full-header">

			<div id="header-wrap" class="">

				<div class="container clearfix">

					<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>

					<!-- Logo
					============================================= -->
					<div id="logo">
						<a href="<?php echo $url; ?>" class="standard-logo" data-dark-logo="http://canvashtml-cdn.semicolonweb.com/images/logo-dark.png"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="AlphaTechSchool Logo"></a>
					</div><!-- #logo end -->

					<!-- Primary Navigation
					============================================= -->
				
					<nav id="primary-menu">
	<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<?php
									wp_nav_menu( array(
										'theme_location' => 'primary',										
										'container_class'     => 'sf-js-enabled',
                                                                                'echo'          => true,
   
 
    'walker'        => new themeslug_walker_nav_menu
									 ) );
								?>
													<?php endif; ?>
					

					</nav><!-- #primary-menu end -->

				</div>

			</div>

		</header><!-- #header end -->
