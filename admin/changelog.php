<style type="text/css">
	.bs-callout {   padding: 20px;margin: 20px 0; border: 1px solid #fff;border-left-width: 2px;border-radius: 1px;}
	.bs-callout h4 {margin-top: 0;margin-bottom: 5px;}
	.bs-callout p:last-child {margin-bottom: 0;}
	.bs-callout code {border-radius: 3px;}
	.bs-callout+.bs-callout {margin-top: -5px;}
	.bs-callout-default {border-left-color: #777;}
	.bs-callout-default h4 {color: #777;}
	.bs-callout-primary {border-left-color: #428bca;}
	.bs-callout-primary h4 {color: #428bca;}
	.bs-callout-success {border-left-color: #5cb85c;}
	.bs-callout-success h4 {color: #5cb85c;}
	.bs-callout-danger {border-left-color: #d9534f;}
	.bs-callout-danger h4 {color: #d9534f;}
</style>
<center><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#changelog">Open Changelog</button></center>
<div id="changelog" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">webSPELL NOR | Changelog</h4>
      </div>
      <div class="modal-body">
		<?php
		# written by Getschonnik
 		/*
		try { 

			$myXMLData = file_get_contents("http://webspell-nor.de/_NOR/changelog.xml");
			$xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");

			 for($m = 0; $m<=(count($xml->major)-1); $m++) {
				echo '<div class="bs-callout bs-callout-primary">';
					echo "<strong>webSPELL | NOR ".$xml->major[$m]->version  .".".$xml->major[$m]->minor->version;
					if($xml->major[$m]->patch->character!=""){
						echo ".".$xml->major[$m]->patch->character;
					}
				echo '</strong></div>';
				for($d = 0; $d<=(count($xml->major[$m]->patch->description)-1); $d++) {
					echo '<div class="list-group">';
					echo '<a href="#" class="list-group-item active">'.$xml->major[$m]->patch->description[$d]->name  .'&nbsp;<span class="badge">'.count($xml->major[$m]->patch->description[$d]->note).'</span></a>';
					for($i = 0; $i<=(count($xml->major[$m]->patch->description[$d]->note)-1); $i++) {
						echo '<a href="#" class="list-group-item">'.$xml->major[$m]->patch->description[$d]->note[$i]  ."</a>";	
					}
					echo '</div>';
				}
			}
		} CATCH(Exception $e) {
			echo '> Changlog can\'t found.';
		}
		}*/
?> Deactivated
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



</body></html>