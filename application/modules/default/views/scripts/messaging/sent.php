<?php
//print_r ($this->InboxDetails["inbox"]);
$inbox=$this->sentDetails["inbox"];
print_r($inbox);
foreach($inbox as $key => $value) { 
echo $value["message_subject"];
}

?>
<div class="main_body">
<div class="left width-100">
	<div class="breadcrumbs">
		<span class="home-icon"></span>
		<a href="javascript:void(0)" class="home">Home</a>
		<span class="arrow"></span>
		<a href="javascript:void(0)" class="events">Internal Messaging</a>
		<div class="clear"></div>
	</div>
	<div id="events_page">
		<div class="header_events">
			<h2>Inbox</h2>
			<a id="add-event" href="<?php echo $this->baseUrl('messaging/compose/');?>" class="button-dark icon add"><span><span>+ Compose</span></span></a>
			<div class="event_search">
				<input type="text" placeholder="Event Name | Event Venue | Owner"/>
				<input type="submit" value="go"/>
			</div>
			<div class="clear"></div>
		</div>
		<div class="grid-view">
			<div class="grid-view-heading left">
				<a href="javascript:void(0)" class="button-light icon delete"><span><span>Delete</span></span></a>
				<a href="javascript:void(0)" class="button-light icon share"><span><span>Share</span></span></a>
				<a href="javascript:void(0)" class="button-light icon export"><span><span>Export</span></span></a>
				<a href="javascript:void(0)" class="button-light icon import"><span><span>Import</span></span></a>
				<a href="javascript:void(0)" class="button-light icon archive"><span><span>Archive</span></span></a>	
				<a href="javascript:void(0)" class="button-light icon tags"><span><span>Tags</span></span></a>	
			</div>
			<div class="right">
				<select>
					<option>Select</option>
				</select>
			</div>
			<div class="clear"></div>
			<table cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width='30'><input type="checkbox"/></th>
						<th >From</th>
						<th  width='100'>Subject</th>
						<th  width='100'>Message</th>
						<th  width='100'>Created Date</th>
						
					</tr>
				</thead>
				<tbody>
				
					<?php foreach($inbox as $key => $value) { 
					$subject= $value["message_subject"];
					$message= $value["message_body_content"];
					$mdate= $value["createddatetime"];
					$from= $value["firstname"];
					$subject= $value["message_subject"];
					$mid=$value["message_id"];
					print<<<END
					<tr class="odd">
					<td><input type="checkbox" id="$mid"/></td>
				    <td>$from</td><td>$subject</td><td>$message</td><td>$mdate</td>	
				
					</tr>
END;
}
					?>
					
					
				</tbody>
			</table>
			<div class="grid-view-footer">
					<a  href="javascript:void(0)" class="left button-light no-text icon refresh"><span><span>Refresh</span></span></a>
					<span class="right">View 1-10 of 20</span>
					<div class="pagention text-center">
						<a  href="javascript:void(0)" class="button-light icon First"><span><span>First</span></span></a>
						<a  href="javascript:void(0)" class="button-light icon prev"><span><span>Prev</span></span></a>
						<span class="info">Page 1 of 2</span>
						<a  href="javascript:void(0)" class="button-light icon next"><span><span>Next</span></span></a>
						<a  href="javascript:void(0)" class="button-light icon last"><span><span>last</span></span></a>
						<select>
							<option>1</option>
							<option>2</option>
						</select>
					</div>
					<div class="clear"></div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
</div>