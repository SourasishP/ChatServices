<?php
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	require_once "config.php";
	$to_user=$_POST["to"];
	$from_user=$_POST["from"];
	$msg=$_POST["text"];
	$f_q=$db_conn->prepare("SELECT id from users WHERE uname=?");
	$f_q->bind_param('s',$to_user);
	$f_q->execute();
	$res=$f_q->get_result();
	if($res->num_rows==1)
	{
		while($r=$res->fetch_assoc())
		{
			$to_id=$r['id'];
		}
	}
	$s_q=$db_conn->prepare("SELECT id from users WHERE uname=?");
	$s_q->bind_param('s',$from_user);
	$s_q->execute();
	$res=$s_q->get_result();
	if($res->num_rows==1)
	{
		while($r=$res->fetch_assoc())
		{
			$from_id=$r['id'];
		}
	}
	$t_q=$db_conn->prepare("INSERT INTO chat_services (sender_id,receiver_id,message) VALUES (?,?,?)");
	$t_q->bind_param('sss',$from_id,$to_id,$msg);
	if($t_q->execute()==False)
	{
		echo "ERROR";
	}
	$db_conn->close();
}
?>