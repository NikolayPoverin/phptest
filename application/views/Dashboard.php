
<?php
	//echo Debug::vars('2', $deviceList);
?>	
<?php
$data = Session::instance()->get('alertErr', null);
Session::instance()->delete('alertErr');
$IPError = Session::instance()->get('alertIPErr', null);
Session::instance()->delete('alertIPErr');
$data1 = Session::instance()->get('alertOk', null);
Session::instance()->delete('alertOk');
//echo Debug::vars('93 OK', $deviceList);exit;
//
if (!is_null($data)){
	
	$sd = ($data);
    echo "<div class='alert alert-danger'>$sd</div>";
	

}
if (!is_null($IPError)){
	
	$sd = ($IPError);
    echo "<div class='alert alert-danger'>$sd</div>";
	

}
$deviceName = '';

if (!is_null($data1)){
	$id_dev = $_GET['id_dev'];
	
	foreach ($deviceList as $device) {
		if ($device['ID_DEV'] == $id_dev) {
			$deviceName = $device['NAME'];
			break;
		}
	}
	
	echo "<div class='alert alert-success'>Изменения для устройства \"" . iconv('windows-1251', 'utf-8', $deviceName) . "\" успешно</div>";
}
    

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
							//echo Debug::vars(Arr::get($value, 'LASTEVENT'));
							echo (is_null(Arr::get($value, 'LASTEVENT')))? '-' : date ('d.m.Y H:i:s', Arr::get($value, 'LASTEVENT', 0)/1000);
							echo '</td>';
						echo '<td>'.Arr::get($value, 'INSERTTIME', '---').'</td>';
						echo '<td>'.'<label class="btn btn-line dark btn-xs popup-contact" for="modalm-1" 
							org_name="'. iconv('windows-1251','UTF-8',Arr::get($value, 'NAME')).'" 
							org_id="'.Arr::get($value, 'ID_DEV', -1).'"
							org_id1="'.Arr::get($value, 'ID_DEV', -1).'"
							ip_dev="'.long2ip(Arr::get($value, 'IP', 0)).'"
							login="'.(Arr::get($value, 'LOGIN', 0)).'"
							pass="'.(Arr::get($value, 'PASS', 0)).'"
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
				<h2>Редактирование параметров панели bas-ip</h2>
				<label class="btnm-close" for="modalm-1" aria-hidden="true">x</label>
			</div>
			<div class="modalm-body">
			<h2>Укажите IP адрес для панели</h2>
			
				<div class="row">
					<div class="kartka">
					  <h1></h1>
					  с адресом  
					  <h2></h2>
					  
					  </div>
			</div>
			
			<?php
 /*			echo Debug::vars('134', $deviceList);
			$patter_IP='^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$';// шаблон IP адреса
			 */
			?>
			
			<form action="oop/control_no_model" method="post" enctype="multipart/form-data">
					Изменения IP
					<h4></h4>
					<input type="input" name="new_IP" id="ipp" pattern="^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" >
					<input type="hidden" name="id_dev" id="id_org1" >
					<input type="hidden" name="todo" value="bas_changeIP2" >
					<input type="submit" name="submit" value="Новый IP адрес">
			</form> 
			 <br></br>
			  
			<form action="dashboard/update" method="post" enctype="multipart/form-data">	 
					Изменения Логина и Пароля
					<h4></h4>
				 Логин <input type="text" name="login" id="login1" placeholder="Логин">
				 <h4></h4>
				 Пароль <input type="password" name="password" id="pass1" placeholder="Пароль">

				 <input type="hidden" name="id_dev" id="id_org2" >
				 <input type="hidden" name="todo" value="bas_changeIP2" >
				 <input type="submit" name="submit" value="Новый Логин и Пароль">
				
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
         var bname = $(this).attr('org_name');
         var bprice = $(this).attr('org_id');
         var ip_deb = $(this).attr('ip_dev');

	
         $(".kartka h1").text(bname);
         $(".kartka ttt").html(bprice);
         $(".kartka  h2").html(ip_deb);
		 
		 document.getElementById("id_org2").value = $(this).attr('org_id');
		 document.getElementById("id_org1").value = $(this).attr('org_id1');
		 document.getElementById("ipp").value = $(this).attr('ip_dev');
		 document.getElementById("login1").value = $(this).attr('login');
		 document.getElementById("pass1").value = $(this).attr('pass');
		 
       });
	

   });
 
   	$(function() {		
  		$("#tablesorter").tablesorter({sortList:[[0,0]]});
  	});	
	
 
</script>
	