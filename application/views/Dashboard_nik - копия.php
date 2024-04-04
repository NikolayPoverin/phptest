
<?php
//	echo Debug::vars('2', $deviceList);
?>	
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo __('bas_device_list').' ('.count($deviceList).')';?></h3>
	</div>
	<div class="panel-body">
		<?php
			echo '<div>';
			echo Form::open('dashboard/auth');
			?>
			
			<table id="tablesorter" class="table table-striped table-hover table-condensed tablesorter">
				<thead>
				<tr>
					<th><?php echo __('id_dev');?></th>
					<th><?php echo __('dev_name');?></th>
					<th><?php echo __('ip');?></th>
					<th><?php echo __('port');?></th>
					<th><?php echo __('dev_version');?></th>
					<th><?php echo __('protokol');?></th>
					<th><?php echo __('about');?></th>
					<th><?php echo __('lastevent');?></th>
					<th><?php echo __('lastequest');?></th>
					<th><?php echo __('to_do');?></th>
					
				</tr>
				</thead>
				<tbody>
				<?php 
				$data=array();
				if($deviceList){
				foreach($deviceList as $key=>$value)
				{
					echo '<tr>';
						echo '<td>'.Arr::get($value, 'ID_DEV').'</td>';
						echo '<td>'.iconv('windows-1251','UTF-8', Arr::get($value, 'NAME')).'</td>';
						echo '<td>'.long2ip(Arr::get($value, 'IP')).'</td>';
						echo '<td>'.Arr::get($value, 'PORT', '---').'</td>';
						echo '<td>'.Arr::get($value, 'DEV_VERSION', '---').'</td>';
						echo '<td>'.Arr::get($value, 'CONNECTIONSTRING', '---').'</td>';
						echo '<td>'.Arr::get($value, 'ABOUT', '---').'</td>';
						echo '<td>';
							//echo Debug::vars('46', $value);
							echo (is_null(Arr::get($value, 'LASTEVENT')))? '-' : date ('d.m.Y H:i:s', Arr::get($value, 'LASTEVENT', 0)/1000);
							echo '</td>';
						echo '<td>'.Arr::get($value, 'INSERTTIME', '---').'</td>';
						echo '<td>'.'<label class="btn btn-line dark btn-xs popup-contact" for="modalm-1" 
							org-name="'. iconv('windows-1251','UTF-8',Arr::get($value, 'NAME')).'" 
							org_id="'.Arr::get($value, 'ID_DEV', -1).'"
							login="'.Arr::get($value, 'LOGIN').'"
							pass="'.Arr::get($value, 'PASS').'"
							ip_dev="'.long2ip(Arr::get($value, 'IP', 0)).'"
							>Редактировать</label></td>';
					
					echo '</tr>';
					}
				}
				?>
				</tbody>
			</table>
			
			<?php
			

			
			echo '</div>';
			
			echo Form::close();
			
	?>
		
	
</div>
<?php 
	echo Kohana::version();
	echo phpversion();
	?>
</div>



<div class="modalm">


	<div class="panel-body">
	<input class="modalm-open" id="modalm-1" type="checkbox" hidden>
	<div class="modalm-wrap" aria-hidden="true" role="dialog">
		
		<div class="modalm-dialog">
			<div class="modalm-header">
				<h2 id="id_org1" >Редактирование параметров панели bas-ip</h2>
				<label class="btnm-close" for="modalm-1" aria-hidden="true">x</label>
			</div>
			<div class="modalm-body">
			<h4>Укажите IP  адрес, логин и парольдля панели </h4>
			
				<div class="row">
					<div class="kartka">
					  <h1></h1>
					  <h6>id_dev <ttt></ttt></h6>
					  
					  </div>
										  
			</div>
			
			<?php
 /*			echo Debug::vars('134', $deviceList);
			$patter_IP='^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$';// шаблон IP адреса
			 */
			?>
			
			<form action="dashboard/update" method="post" enctype="multipart/form-data">
					 
					IP адрес <input type="input" name="new_IP" id="ipp" placeholder="ipp" pattern="^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" >
					
				 <br></br>
				 Логин <input type="text" name="login" id="login" placeholder="Логин">
				 <br></br>
				 Пароль <input type="password" name="password" id="pass" placeholder="Пароль">

				 <input type="hidden" name="id_dev" id="id_org1" >
				 <input type="hidden" name="todo" value="bas_changeIP2" >
				 <input type="submit" name="submit" value="Сохранить">
				
			</form> 

			</div>
			<div class="modalm-footer">
				<h4>Редактирование свойств панели bas-IP</h4>
				
			</div>
		</div>
	</div>
	</div>

</div>

<script>
 //https://learn.javascript.ru/function-object
$(function() {
	
     $(".btn").click(
       function() {
         var bname = $(this).attr('org-name');
         var bprice = $(this).attr('org_id');
         var ip_deb = $(this).attr('ip_dev');

	
         $(".kartka h1").text(bname);
         $(".kartka ttt").html(bprice);
         $(".kartka  h2").html(ip_deb);
		 document.getElementById("id_org1").value = $(this).attr('org_id');
		 document.getElementById("ipp").value = $(this).attr('ip_dev');
		 document.getElementById("login").value = $(this).attr('login');
		 document.getElementById("pass").value = $(this).attr('pass');
		 
       });
	

   });
 
   	$(function() {		
  		$("#tablesorter").tablesorter({sortList:[[0,0]]});
  	});	
	
 
</script>
	