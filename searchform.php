<form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
    <div>
    	<input type="text" name="s" id="s" placeholder="<?php _e('Hacer busqueda...', 'jeo'); ?>" value="<?php /* by mohjak 2019-11-24 Line 5 issue#120 */ echo isset($_GET['s']) ? $_GET['s'] : ''; ?>" />
    </div>
</form>
