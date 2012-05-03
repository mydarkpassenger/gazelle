<?
enforce_login();
show_header('Staff');

include(SERVER_ROOT.'/sections/staff/functions.php');
include(SERVER_ROOT.'/sections/staffpm/functions.php');
$SupportStaff = get_support();

list($FrontLineSupport, $ForumStaff, $Staff) = $SupportStaff;

?>
<div class="thin">
	<h2><?=SITE_NAME?> Staff</h2>
	<div class="box pad" style="padding:10px;">
		<h3>Contact Staff</h3>
		<div id="below_box">
			<p>If you are looking for help with a general question, we appreciate it if you would only message through the staff inbox, where we can all help you.</p>
                  <p>You can do that by
                              <a href="#"  class="contact_link" onClick="$('#compose').toggle();">sending a message to the Staff Inbox</a>
                              <em>Please do not PM individual staff members for support!</em> </p>
            </div>
		<? print_compose_staff_pm(true); ?>
		<br />
      </div>
	<div class="box pad" style="padding:10px;">
		<h3>First-line Support</h3>
		<p><strong>These users are not official staff members</strong> - they're users who have volunteered their time to help people in need. Please treat them with respect and read <a href="wiki.php?action=article&amp;id=260">this</a> before contacting them. </p>
		<table class="staff" width="100%">
			<tr class="colhead">
				<td width="150px">Username</td>
				<td width="150px">Last seen</td>
				<td><strong>Support for</strong></td>
			</tr>
<?
	$Row = 'a';
	foreach($FrontLineSupport as $Support) {
		list($ID, $Class, $Username, $Paranoia, $LastAccess, $SupportFor) = $Support;
		$Row = ($Row == 'a') ? 'b' : 'a';
?>
			<tr class="row<?=$Row?>">
				<td class="nobr">
					<?=format_username($ID, $Username)?>
				</td>
				<td class="nobr">
					<? if (check_paranoia('lastseen', $Paranoia, $Class)) { echo time_diff($LastAccess); } else { echo 'Hidden by user'; }?>
				</td>
				<td class="nobr">
					<?=$SupportFor?>
				</td>
			</tr>
<?	} ?>
		</table>
	</div> 
      <?	/* ?>
	<div class="box pad" style="padding:0px 10px 10px 10px;">
		<br /><h3><!--Forum -->Mod Pervs</h3>
	<!--<p>Forum Mods are users who have been promoted to help moderate the forums. They can only help with forum oriented questions</p>-->
		<p>Mods are staff members and have wide ranging powers.</p>
		<table class="staff" width="100%">
			<tr class="colhead">
				<td width="150px">Username</td>
				<td width="150px">Last seen</td>
				<td><strong>Remark</strong></td>
			</tr>
<?
	$Row = 'a';
	foreach($ForumStaff as $Support) {
		list($ID, $Class, $ClassName, $Username, $Paranoia, $LastAccess, $SupportFor) = $Support;
		$Row = ($Row == 'a') ? 'b' : 'a';
?>
			<tr class="row<?=$Row?>">
				<td class="nobr">
					<?=format_username($ID, $Username)?>
				</td>
				<td class="nobr">
					<? if (check_paranoia('lastseen', $Paranoia, $Class)) { echo time_diff($LastAccess); } else { echo 'Hidden by user'; }?>
				</td>
				<td class="nobr">
					<?=$SupportFor?>
				</td>
			</tr>
<?	} ?>
		</table>
	</div>
      
      <?	*/ ?>
     
	<div class="box pad" style="padding:10px;">
            <h3>Staff Members</h3>
            <p>Staff members do something something blah blah should we even bother having a line here?</p>
		
<?
	$CurClass = 0;
	$CloseTable = false;
	foreach($ForumStaff as $Support) {
		list($ID, $Class, $ClassName, $Username, $Paranoia, $LastAccess, $SupportFor) = $Support;
		if($Class!=$CurClass) { // Start new class of staff members
			$Row = 'a';
			if($CloseTable) {
				$CloseTable = false;
				echo "\t</table>";
			}
			$CurClass = $Class;
			$CloseTable = true;
			echo '<h3>'.$ClassName.'s</h3>';
?>
		<table class="staff" width="100%">
			<tr class="colhead">
				<td width="150px">Username</td>
				<td width="150px">Last seen</td>
				<td><strong>Remark</strong></td>
			</tr>
<?
		} // End new class header
		
		// Display staff members for this class
		$Row = ($Row == 'a') ? 'b' : 'a';
?>
			<tr class="row<?=$Row?>">
				<td class="nobr">
					<?=format_username($ID, $Username)?>
				</td>
				<td class="nobr">
					<? if (check_paranoia('lastseen', $Paranoia, $Class)) { echo time_diff($LastAccess); } else { echo 'Hidden by staff member'; }?>
				</td>
				<td class="nobr">
					<?=$Remark?>
				</td>
			</tr>
<?	} ?>
		</table>
		
	</div>
       
	<div class="box pad" style="padding:10px;">
            <h3>Admins</h3>
            <p>Senior Staff - They make the decisions, annoy them at your own peril.</p>
<?
	$CurClass = 0;
	$CloseTable = false;
	foreach ($Staff as $StaffMember) {
		list($ID, $Class, $ClassName, $Username, $Paranoia, $LastAccess, $Remark) = $StaffMember;
		if($Class!=$CurClass) { // Start new class of staff members
			$Row = 'a';
			if($CloseTable) {
				$CloseTable = false;
				echo "\t</table>";
			}
			$CurClass = $Class;
			$CloseTable = true;
			echo '<h3>'.$ClassName.'s</h3>';
?>
		<table class="staff" width="100%">
			<tr class="colhead">
				<td width="150px">Username</td>
				<td width="150px">Last seen</td>
				<td><strong>Remark</strong></td>
			</tr>
<?
		} // End new class header
		
		// Display staff members for this class
		$Row = ($Row == 'a') ? 'b' : 'a';
?>
			<tr class="row<?=$Row?>">
				<td class="nobr">
					<?=format_username($ID, $Username)?>
				</td>
				<td class="nobr">
					<? if (check_paranoia('lastseen', $Paranoia, $Class)) { echo time_diff($LastAccess); } else { echo 'Hidden by staff member'; }?>
				</td>
				<td class="nobr">
					<?=$Remark?>
				</td>
			</tr>
<?	} ?>
		</table>
		
	</div>
</div>
<?
show_footer();
?>