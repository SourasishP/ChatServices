<html><head><title>Details Entry Portal | Chat Services</title>
<?php
if(!isset($_COOKIE["Uname"]))
{
    die("Not Logged In");return false;
    exit(0);
}
require_once "config.php";
class User
{
	public $uname,$em,$path;
	function get_user_dtl()
	{
		$f_q=$db_conn->prepare("SELECT A.uname,B.file_name FROM users A,user_images B WHERE A.email=? and A.email=B.email");
		$f_q->bind_param('s',$thid->em);
		$f_q->execute();
		$res=$f_q->get_result();
		if ($res->num_rows==1)
		{
			while($r=$res->fetch_assoc())
			{
				$this->path="photos/".$r['file_name'];
				$this->uname=$r['uname'];
			}
		}
	}
}
$curr_user=new User();
$curr_user->em=$_COOKIE["Uname"];$curr_user->get_user_dtl();
$show_user=new User();
?><link rel='stylesheet' href='chat.css'>
</head>
<body>
<div class="head"><div class="head_content">Details Entry | Chat Services</div></div>
<div class="content_box">
<div class="content_left"><div class="content_left_head"><img src="<?php echo $curr_user->path; ?>" height='80' width='80' alt="Profile Picture" id="img_active" class="img_active">
<span class='show_nm' style="font-weight:bold;margin-top:25px;"><?php echo $curr_user->uname; ?><br><span style="color:#CB4335;font-weight:normal;margin-top:5px;float:left;" id="curr_s">&bull; Offline</span></span>
</div>
<?php
$s_q=$db_conn->prepare("SELECT email FROM users WHERE email!=? and status='complete' ORDER BY uname");
$s_q->bind_param('s',$curr_user->em);
$s_q->execute();
$res=$s_q->get_result();
if($res->num_rows>1)
{
	while($r=$res->fetch_assoc())
	{
		$show_user->em=$r['email'];
		$show_user->get_user_dtl();
		echo "<div class='content_left_show' title='Click to Start Chatting' onclick='javascript:chat(\"$show_user->path\",\"$show_user->uname\");'>
		<img src='$show_user->path' height='50' width='50' alt='Show User Picture' class='show_img'>
		<span class='show_nm'>$show_user->uname</span></div>";
	}
}
?>
</div>
<div class="content_right"><div class="content_right_head" id="content_right_head"></div>
<div class="show_msg_text_prev" id="show_msg_text_prev"></div>
<form class="show_msg_text" id="show_msg_text" style="border:0;font-weight:normal;padding:0;margin:0;" name="show_msg_text" method="post">
<textarea rows='2' cols='100' name='snd_msg' id='snd_msg' required autofocus placeholder="Type message here..."></textarea>
<input type="hidden" id="to_user_h" name="to_user_h" value="">
<span class="send_msg"><input type="submit" style="width:auto;outline:none;" value="SEND" name="sub_snd_msg"></span></form></div>
</div>
<div id='notifi' class='success_popup_background'><div class='success_popup_content anmt'>
<center><div style='color:#ff0066;font-size:20px' id='success'><b>ERROR</b></div><hr color='blue' size='2'><span style="font-size:1.5em;color:#b36b00" id="show_err">Message could not be sent.<br>Connection to Server lost.</span></center><br><br>
<center><button onclick='cls_bt()' class='cls_bt'><b>OK</b></button></center></div></div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script>
jQuery('#show_msg_text').on('submit',function(e){
	var msg=document.getElementById('snd_msg').value;
	var f="<?php echo $curr_user->uname; ?>";
	var t=document.getElementById('to_user_h').value;
	document.getElementById('snd_msg').value='';
	snd_msg(t,f,msg);
	e.preventDefault();
});
function get_msg(nm)
{
	var curr_nm="<?php echo $curr_user->uname; ?>";
	$.ajax({
		type: 'post',
		url: 'Get_Msg.php',
		data: {name:nm,curr_nm:curr_nm},
		success: function(response)
		{
			change_s();
			document.getElementById('show_msg_text_prev').innerHTML=response;
		},
		error:function(err,exception){
			document.getElementById('show_msg_text_prev').innerHTML="";
			document.getElementById('notifi').style.display='block';document.getElementById('success').style.display='block';
			if(err.status==0)
			{
				document.getElementById('show_err').innerHTML="Cannot connect";
				document.getElementById('curr_s').style.color="#CB4335";
				document.getElementById('img_active').style.border="2px solid #CB4335";
				document.getElementById('curr_s').innerHTML="&bull; Offline";
			}
			else if(err.status==500)
				document.getElementById('show_err').innerHTML="Internal Server Error.";
			else if(err.status==404)
				document.getElementById('show_err').innerHTML="Could not process your request";
			else
				document.getElementById('show_err').innerHTML="ERROR:"+err.responseText;
		}
	});
}
function snd_msg(nm,curr,msg)
{
	$.ajax({
		type: 'post',
		url: 'Send_Msg.php',
		data: {to:nm,from:curr,text:msg},
		success: function(response)
		{
			if(response!="ERROR")
			{
				get_msg(nm);change_s();
			}
			else
			{
				document.getElementById('notifi').style.display='block';document.getElementById('success').style.display='block';
			}
		},
		error:function(err,exception){
			document.getElementById('notifi').style.display='block';document.getElementById('success').style.display='block';
			if(err.status==0)
			{
				document.getElementById('show_err').innerHTML="Cannot connect";
				document.getElementById('curr_s').style.color="#CB4335";
				document.getElementById('img_active').style.border="2px solid #CB4335";
				document.getElementById('curr_s').innerHTML="&bull; Offline";
			}
			else if(err.status==500)
				document.getElementById('show_err').innerHTML="Internal Server Error.";
			else if(err.status==404)
				document.getElementById('show_err').innerHTML="Page not Found";
			else
				document.getElementById('show_err').innerHTML="ERROR:"+err.responseText;
		}
	});
}
function chat(pth,nm)
{
	document.getElementById("content_right_head").style.display="block";
	document.getElementById("show_msg_text").style.display="block";document.getElementById("show_msg_text_prev").style.display="block";
	document.getElementById("content_right_head").innerHTML="<img src='"+pth+"' height='80' width='80' class='show_img'><span class='show_nm'>"+nm+"</span>";
	document.getElementById("to_user_h").value=nm;
	get_msg(nm);
}
function cls_bt(){
document.getElementById('notifi').style.display='none';
}
function change_s()
{
	document.getElementById('curr_s').style.color="green";
	document.getElementById('img_active').style.border="2px solid green";
	document.getElementById('curr_s').innerHTML="&bull; Online";
}
setTimeout(change_s,2000);
</script></html>
<?php $db_conn->close(); ?>
