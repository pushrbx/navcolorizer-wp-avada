<div class="wrap">
	<h2>Menu Colorizer Plugin</h2>
	<form id="navcolorizer-settings" method="post" action="options.php">
		<?php @settings_fields('NavColorizer'); ?>
		<?php @do_settings_fields('NavColorizer'); ?> 
		<?php
			$inputs = "";
			$colors = $this->_options['colors'];
			$optColorCount = count($colors);
			$menuCount =  $this->getMenuCount();
			if($optColorCount < $menuCount)
			{
				for($i = 0; $i < $menuCount - $optColorCount; $i++)
				{
					$colors[] = "#000";
				}
				$this->_options['colors'] = $colors;
			}
			else if($optColorCount > $menuCount)
			{
				$i = 0;
				$temp = array();
				
				foreach($colors as $color)
				{
					if($i == $menuCount)
					{
						break;
					}
					$temp[] = $color;
					$i++;
				}
				
				$this->_options['colors'] = $temp;
				$colors = $temp;
			}
			
			foreach($colors as $i => $color)
			{
				$inputs .= '<input name="' . $this->option_name . "[colors][]" . '" data-index="'.$i.'" type="hidden" id="inpnavColorPicker-'.$i.'" value="'.$color.'" />';
			}
			// <input type="text" name="setting_b" id="setting_b" value="" />
			
			echo $inputs;
		?>
		<table class="form-table"> 
			<!--<tr valign="top"> 
				<th scope="row">
					<label for="colorcount">
						The count of colors you want:
					</label>
				</th>
				<td>
					<input type="text" <?php echo 'name="' . $this->option_name . "[colorcount]" . '"'; ?> id="colorcount" value="<?php /*echo $this->_options['colorcount'];*/ ?>" />
				</td> 
			</tr> -->
			<tr valign="top">
				<th scope="row">
					<label>Colors for each currently active menu item.</label>
				</th>
				<td>
					<!-- todo: list color pickers, and in javascript load the jquery plugin, if the jquery does not exist,
					write error message here. -->
					<div class="navColorWrap">
					<?php
						$inputs = "";
						$colors = $this->_options['colors'];
						foreach($colors as $i => $color)
						{
							$inputs .= '<div class="navColorPicker" id="navColorPicker-'.$i.'" data-index="'.$i.'" data-color="'.$color.'"><div></div></div>';
						}
						// <input type="text" name="setting_b" id="setting_b" value="" />
						
						echo $inputs;
					?>
					
					</div>
				</td>
			</tr> 
		</table> 
		<?php @submit_button(); ?>
	</form>

</div>