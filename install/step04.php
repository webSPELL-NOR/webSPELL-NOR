<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

if ($_POST['hp_url']) {
?>
<div class="row marketing">

    <div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $_language->module['select_install']; ?></h3>
			</div>
			<div class="panel-body">
            <?php echo $_language->module['what_to_do']; ?>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update">
                        <?php echo $_language->module['update_31']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_beta">
                        <?php echo $_language->module['update_beta4']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_beta5">
                        <?php echo $_language->module['update_beta5']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_beta6">
                        <?php echo $_language->module['update_beta6']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_final">
                        <?php echo $_language->module['update_40']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_40100">
                        <?php echo $_language->module['update_40100']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_40102">
                        <?php echo $_language->module['update_40102']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_121">
                        <?php echo $_language->module['update_121']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_123">
                        <?php echo $_language->module['update_123']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_124">
                        <?php echo $_language->module['update_124']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="update_125">
                        <?php echo $_language->module['update_125']; ?>
					</label>
				</div>
				<div class="radio">
					<label>
                        <input type="radio" name="installtype" value="full" checked="checked" id="full_install">
                        <input type="hidden" name="hp_url" value="<?php echo $_POST['hp_url']; ?>">
                        <?php echo $_language->module['new_install']; ?>
					</label>
				</div>        
                <div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>
			</div>
		</div>
    </div>
</div> <!-- row end -->

<?php
}
