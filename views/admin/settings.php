<?php
$valuesdb = $this->session->userdata('creds');
$this->load->helper('form');
?>
<div id="content-body">
	<section class="title">
		<h4>Media Stats API settings</h4>
	</section>
	<section class="item">
		<div class="content">
			<form class="crud" method="get" accept-charset="utf-8"
				action="admin/media_stats/index">
				<ul>
					<li>
						<div class="form_inputs">
							<label for="description">Google Analytics API Set Up<span>*</span></label>
						</div> <br> <small>Information of Client's Google Analytics</small>
						<div class="input">
							<input name="Analytics_ClientName" class="one_half"
								placeholder="Client Name "
								value="<?php  echo  ($valuesdb['Analytics_ClientName']!=" ")?$valuesdb['Analytics_ClientName']: "Enter Client Name";?>"
								" type="text">
						</div>
						<div class="input">
							<input
								value="<?php  echo  ($valuesdb['Analytics_ClientId']!=" ")?$valuesdb['Analytics_ClientId']: "Enter Client ID";?>"
								name="Analytics_ClientId" placeholder="ClientId" type="text">
						</div>
					</li>
					<li><div class="buttons float-right padding-top">
							<button type="submit" name="btnAction" value="save"
								class="btn blue">
								<span>Authenticate</span>
							</button>
							<a href="<?php echo base_url().'admin';?>"
								class="btn gray cancel">Cancel</a> <br> <br>Note: <span>*</span>
							This form should be properly filled to make sure that labels work
							fine!
						</div>
						<hr></li>
					<li><br> <small>Developer's Google Console Information</small>
						<div class="input">
							<input name="ClientId" class="one_half" placeholder="Client ID "
								value="<?php  echo  ($valuesdb['ClientId']!=Null)?$valuesdb['ClientId']: "Your Value";?>"
								type="text">
						</div>
						<div class="input">
							<input name="Clientsecret"
								value="<?php  echo  ($valuesdb['Clientsecret']!=Null)?$valuesdb['Clientsecret']: "Your Value";?>"
								placeholder="Client Secret" type="password">
						</div></li>
				</ul>
			</form>
		</div>
	</section>
</div>