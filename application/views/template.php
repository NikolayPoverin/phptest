<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.png">

    <title>Artonit City <?
		echo  isset(Kohana::$config->load('artonitcity_config')->city_name)? Kohana::$config->load('artonitcity_config')->city_name : '';
		echo isset($title)? $title : '';
		?></title>

    <!-- Bootstrap core CSS -->
    <?= HTML::style('static/css/bootstrap.css'); ?>
	<?= HTML::style('static/css/modal.css'); ?>
    <?//= HTML::style('static/css/admin.css'); ?>
	<?//= HTML::style('static/css/timesheet.css'); ?>
	<?= HTML::style('static/css/city.css'); ?>
	<?//= HTML::style('static/css/modal.css'); ?>
	<link rel="stylesheet" href="/city/static/css/themes/blue/style.css" type="text/css" media="print, projection, screen" />
	 
<!-- ... -->
  <!-- 1. Подключить библиотеку jQuery -->
  <!-- <script type="text/javascript" src="/city/static/js/jquery-1.11.1.min.js"></script>  --> 
   <script type="text/javascript" src="/city/static/js/jquery-2.2.4.js"></script>
  
  <!-- 2. Подключить скрипт moment-with-locales.min.js для работы с датами -->
  <script type="text/javascript" src="/city/static/js/moment-with-locales.min.js"></script>
  <!-- 3. Подключить скрипт платформы Twitter Bootstrap 3 -->
  <script type="text/javascript" src="/city/static/js/bootstrap.min.js"></script>
  <!-- 4. Подключить скрипт виджета "Bootstrap datetimepicker" -->
  <script type="text/javascript" src="/city/static/js/bootstrap-datetimepicker.min.js"></script>
  <!-- 5. Подключить CSS платформы Twitter Bootstrap 3 -->  
  <link rel="stylesheet" href="/city/static/css/bootstrap.min.css" />
  <!-- 6. Подключить CSS виджета "Bootstrap datetimepicker" -->  
  <link rel="stylesheet" href="/city/static/css/bootstrap-datetimepicker.min.css" />
  
  
    

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
    
    
   <!--  Скрипты для сортировки таблицы 
     <script type="text/javascript" src="/city/static/js/sort/jquery-latest.js"></script> --> 
	<script type="text/javascript" src="/city/static/js/sort/jquery.tablesorter.js"></script>
	 
  </head>

  <body>
  <div class="container">
				<!-- Static navbar -->
			 <div class="navbar navbar-default">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					  <?= HTML::anchor('dashboard', __('City'),  array('class'=>'navbar-brand')) ?>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li><?php //echo HTML::anchor('skud', __('сводная'));?></li>
					</ul>
				</div>
			</div>
			

				  <!--container-fluid -->
				<div class="container">
					<div class="row">
						<?
							//include Kohana::find_file('views','top_menu');
							echo $content;?>
							<button onclick="topFunction()" id="myBtn" title="Go to top"><?php echo __('top'); ?></button> 
					</div>
				</div>  
		</div>
	
  </body>
  <script type="text/javascript">
  window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
    } else {
        document.getElementById("myBtn").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
} 
</script>
</html>
