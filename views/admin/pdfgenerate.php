<?php ob_start(); ?>
<!DOCTYPE html>
<html>
	<body>
			<table style="width:100%;">
				<thead>
					<tr>
						<th style="text-align:left; padding:5px; border-bottom:1px solid #ccc;"><strong>Senior Citizen Project : 411.dineshdevkota.com</strong></th>
											<th style="text-align:left; padding:5px; border-bottom:1px solid #ccc;"><strong><?php echo date(); ?></strong></th>
					</tr>
				</thead>
			<tbody>
			
			</tbody>
			</table>	
			
			<div style="height: 900px; width:100%;">
				<div style="width:100%; padding:5px;">
					<h2>
						Social Media Statistics Sample PDF
					</h2>
				</div>
				<div style="width:100%; padding:5px; margin-bottom:30px;">
					<table style="width:100%;">
						<tbody>
							
							
							
							<?php

							$values=array("Facebook","Google","Twitter","Istagram","Organic","Paid");
							echo "<tr><td><h2>Source</h2></td><td><h2>Page Hits</h2></td></tr>";
							foreach ($values as $value){
								echo "<tr><td>".$value."</td><td>".rand(2000, 5000)."</td></tr>";
									}
							
							

							?>
								
									
								
						</tbody>
					</table>
				</div>
				<div>
					
				</div>
				<div style="width: 98%; padding:2% 2% 0 5px; margin-top:50px;">
					<div style="width: 50%;">
						
					</div>
					<div style="width: 50%;">
					</div>
				</div>
				<div style="border-bottom: dashed 2px #eee; width: 100%; margin:15px 0px;">&nbsp;</div>
			</div>
			
	</body>
</html>
<?php 

$html = ob_get_clean(); 
if (ob_get_contents()) ob_end_clean();
include $lib_path . "/PDF/dompdf_config.inc.php";
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("social_media_".time().".pdf", 0);





?>
