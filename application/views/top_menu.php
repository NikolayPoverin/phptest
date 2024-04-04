
<!-- Static navbar -->
 <div class="navbar navbar-default navbar-fixed-top disable">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		  <?= HTML::anchor('rubic', __('City'),  array('class'=>'navbar-brand')) ?>
    </div>
	<div class="navbar-collapse collapse">
		
		<?php
		if(Auth::Instance()->logged_in())
		{?>
				<ul class="nav navbar-nav">
					<li><?php if (Arr::get($_SESSION,'menu_active')=='park') echo 'class="active"'; echo  HTML::anchor('rubic/cardList', __('Машиноместа')); ?></li>
					<li><?php if (Arr::get($_SESSION,'menu_active')=='park') echo 'class="active"'; echo  HTML::anchor('garage', __('Гараж')); ?></li>
					<li><?php if (Arr::get($_SESSION,'menu_active')=='park') echo 'class="active"'; echo  HTML::anchor('rubic/event', __('События')); ?></li>
					<li><?php if (Arr::get($_SESSION,'menu_active')=='gate') echo 'class="active"'; echo  HTML::anchor('gate', __('gate_menu')); ?></li>
			   </ul>
		<?php };?>
            
		<ul class="nav navbar-nav navbar-right">
			<li>
			<?
			//echo Debug::vars('5.05.2017 Пример подготовки пароля для 123', Auth::instance()->hash_password('123'));
					
			if(Auth::Instance()->logged_in())
			{
				echo 'Пользователь '.Auth::instance()->get_user();
					//echo Debug::vars('5.05.2017 Пример подготовки пароля для 123', Auth::instance()->hash_password('123'));
					echo '<div>'.HTML::anchor('logout', __('logout'), array('onclick' => 'return confirm(\'' . __('confirm.delete').'\')')).'</div>';
			} else {
			echo Form::open('rubic', array('method' => 'post', 'class'=>'form-inline'));?>
				<div class="form-group">
					<label for="inputEmail" class="sr-only">Имя</label>
					<input type="text" class="form-control input-sm" id="inputEmail" placeholder="Имя" name="username">
					
				</div>
				<div class="form-group">	    
					<label for="inputPassword" class="sr-only">Пароль</label>
					<input type="password" class="form-control input-sm" id="inputPassword" placeholder="Пароль" name="password">
				</div>
				<div class="checkbox input-sm">
						<label><input type="checkbox" name="remember"> Запомнить</label>
				</div>
					<button type="submit" class="btn btn-primary input-sm">Войти</button>
			<?echo Form::close();
			}

		?>
			</li>
		</ul>
						
    </div>
	<div class="navbar-collapse collapse">
      <?php 
	  
	  if(Auth::Instance()->logged_in())
	  {
      echo __('string_about', array(
      		'db'=> Arr::get(
      			Arr::get(
      					Kohana::$config->load('database')->fb,
      					'connection'
      					),
      		'dsn'),
      		'ver'=> Kohana::$config->load('artonitcity_config')->ver,
      		'developer'=> Kohana::$config->load('artonitcity_config')->developer,
      		)).'<br>';
			echo __('timerefresh', array ('tr'=> date("d.m.Y H:i",time())));
			echo '<br>'.__('Роль Администратор');
	  } else {
		  echo __('Роль Контролёр');
	  }
      ?>
	  </div>
	  
</div>
