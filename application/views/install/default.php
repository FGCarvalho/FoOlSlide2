<?php
$muhSSL = false;
if(isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on")
{
    $muhSSL = true;
}
else
{
    if(FORCE_SSL)
    {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Installing FoOlSlide</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/admin/style.css" />
		<style type="text/css">
			body {
				padding-top: 60px;
			}
		</style>
		<script type="text/javascript" src="<?php echo site_url() ?>assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo site_url() ?>assets/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			function slideDown(item) { jQuery(item).slideDown(); }
			function slideUp(item) { jQuery(item).slideUp(); }
			function slideToggle(item) { jQuery(item).slideToggle(); }
			
			function confirmPlug(href, text, item)
			{
				if (text != "") {
					var modalContainer = jQuery("#modal-container");
					modalContainer.modal({show: true, closeOnEscape: true, backdrop: 'static', keyboard: true});
					modalContainer.find("#modal-text-desc").html(text);
					modalContainer.find("#modal-btn-no").click(function() {	modalContainer.modal('hide'); return false; });
					modalContainer.find("#modal-btn-yes").attr('href', href).click(function() {
						modalContainer.find("#modal-loading").show();
						jQuery.post(href, function(result) {
							modalContainer.find("#modal-loading").hide();
							if (location.href == result.href) window.location.reload(true);
							location.href = result.href;
						}, 'json');
						return false;
					}).focus();
				}
				else {
					jQuery.post(href, function(result) {
						if (location.href == result.href) window.location.reload(true);
						location.href = result.href;
					}, 'json');
				}
			}
			
			function addField(e)
			{
				if (jQuery(e).val().length > 0)
				{
					jQuery(e).clone().val('').insertAfter(e);
					jQuery(e).after('<br/>');
					jQuery(e).attr('onKeyUp', '');
					jQuery(e).attr('onChange', '');
				}
			}
			
			jQuery(document).ready(function() {
				<?php
				$CI = & get_instance();
				if ($CI->agent->is_browser('MSIE'))
				{
				?>
					jQuery('[placeholder]').focus(function() {
						var input = jQuery(this);
						if (input.val() == input.attr('placeholder'))
						{
							input.val('');
							input.removeClass('placeholder');
						}
					}).blur(function() {
						var input = jQuery(this);
						if (input.val() == '' || input.val() == input.attr('placeholder'))
						{
							input.addClass('placeholder');
							input.val(input.attr('placeholder'));
						}
					}).blur().parents('forms').submit(function() {
						jQuery(this).find('[placeholder]').each(function() {
							var input = jQuery(this);
							if (input.val() == input.attr('placeholder')) {
								input.val('');
							}
						})
					});
				<?php } ?>
				// Auto-Focus on First Input
				jQuery(":input:first").focus(); // Focus on first input generated
				
				// Bootstrap jQuery
				jQuery("a[rel^='popover']").each(function() {
					var direction = $(this).attr('rel').replace("popover-", "");
					$(this).popover({ offset: 10, placement: direction });
				});
			});
		</script>
		<script type="text/javascript">jQuery().alert();</script>
	</head>
	
	<body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <a class="navbar-brand" href="/">FoOlSlide 2</a>
                </div>
            </div>
		</nav>
		
		<div class="container-fluid">
			
			<div class="content">
				<ul class="breadcrumb">
					<?php
						echo '<li>' . $controller_title . '</li>';
						if (isset($function_title))
							echo ' <span class="divider">/</span> <li>' . $function_title . '</li>';
						if (isset($extra_title) && !empty($extra_title))
						{
							$breadcrumbs = count($extra_title); $count = 1;
							foreach ($extra_title as $item) {
								echo ' <span class="divider">/</span> ';
								if ($count == $breadcrumbs)
									echo '<li class="active">' . $item . '</li>';
								else
									echo '<li>' . $item . '</li>';
							}
						}
					?>
				</ul>
				
				<div class="alerts">
					<?php
                        if(!$muhSSL)
                        {
                            echo '<div class="alert alert-warning"><i class="fa fa-unlock-alt" aria-hidden="true"></i> '._("Potentially insecure connection, data could be intercepted.").'</div>';
                        }
						if (isset($this->notices))
							foreach ($this->notices as $key => $value)
							{
								echo '<div class="alert alert-' . $value["type"] . ' fade in" data-alert="alert"><a class="close" href="#">&times;</a><p>' . $value["message"] . '</p></div>';
							}
						if (isset($this->tank_auth))
						{
							$flashdata = $this->session->flashdata('notices');
							if (!empty($flashdata))
								foreach ($flashdata as $key => $value)
								{
									echo '<div class="alert alert-' . $value["type"] . ' fade in" data-alert="alert"><a class="close" href="#">&times;</a><p>' . $value["message"] . '</p></div>';
								}
						}
					?>
				</div>
				
				<?php echo $main_content_view; ?>
			</div>
		</div>
		
		<!-- Modal Container for Admin Panel -->
		<div id="modal-container" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 id="modal-text-head">Warning!</h3>
                    </div>
                    <div class="modal-body">
                        <p id="modal-text-desc"></p>
                        <div id="modal-loading" class="loading"><img src="<?php echo site_url() ?>assets/js/images/loader-18.gif"/></div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="modal-btn-no" class="btn btn-danger"><?php echo _('No'); ?></a>
                        <a href="#" id="modal-btn-yes" class="btn btn-success"><?php echo _('Yes'); ?></a>
                    </div>
                </div>
            </div>
        </div>
	</body>
</html>