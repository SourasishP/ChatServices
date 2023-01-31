<?php
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	require_once "config.php";
	$user=$_POST["name"];
	$curr_nm=$_POST["curr_nm"];
	$f_q=$db_conn->prepare("SELECT id from users WHERE uname=?");
	$f_q->bind_param('s',$user);
	$f_q->execute();
	$res=$f_q->get_result();
	if($res->num_rows===1)
	{
		while($r=$res->fetch_assoc())
		{
			$id=$r['id'];
		}
	}
	$s_q=$db_conn->prepare("SELECT id from users WHERE uname=?");
	$s_q->bind_param('s',$curr_nm);
	$s_q->execute();
	$res=$s_q->get_result();
	if(res->num_rows==1)
	{
		while($r=$res->fetch_assoc())
		{
			$curr_id=$r['id'];
		}
	}
	$t_q=$db_conn->prepare("SELECT sender_id,receiver_id,message from chat_services WHERE (sender_id=? and receiver_id=?) or (sender_id=? and receiver_id=?) ORDER BY id");
	$t_q->bind_param('ssss',$id,$curr_id,$curr_id,$id);
	$t_q->execute();
	$res=$t_q->get_result();
	if($res->num_rows>0)
	{
		while($r=$res->fetch_assoc())
		{
			$sender_id=$r['sender_id'];$receiver_id=$r['receiver_id'];$msg=$r['message'];
			if($sender_id===$id)
			{
				//message receive
				echo "<div class='rcv_msg_dpl'>$msg</div>";
			}
			else if($receiver_id===$id)
			{
				//message sent
				echo "<div class='snd_msg_dpl'>$msg</div>";
			}
		}
	}
	else
	{
		echo "<div class='n_msg'>Start Chatting</div>";
	}
	$db_conn->close();
}
?>