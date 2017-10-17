<?php 
require_once("clue.php");
session_start();
if(!isset($_SESSION["game"]))
{
    // number of players
    $p = 3;
    if(isset($_GET['ai']))
    {
        $p = (int)$_GET['ai'] + 1;
    }
    $game = new Clue($p);
    $_SESSION["game"] = $game;
    $_SESSION["player"] = $game->getCurPlayer();
    if(isset($_GET['name']))
        $_SESSION["player"]->name = $_GET['name'];
    
}
// Game interface here ^_^
$seats = array_fill(0,5,"");
$key =array();
switch(count($_SESSION["game"]->players) - 1)
{
case 2:
    $key = array(1,3);
    break;
case 3:
    $key = array(0,2,4);
    break;
case 4:
    $key = array(0,1,3,4);
    break;
case 5:
    $key = array(0,1,2,3,4);
    break;
}
$key = array_reverse($key);
foreach($_SESSION["game"]->players as $play)
{
    if($play == $_SESSION["player"]) continue;
    
    $seats[array_pop($key)] = $play->name;
}


?>
<html>
<head>
<title>Clue in PHP</title>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/ajax-dynamic-content.js" type="text/javascript"></script>
</head>
<body>
<table align="center">
 <tr>
  <td align="center"><?php echo $seats[1]; ?></td><td align="center"><?php echo $seats[2]; ?></td><td align="center"><?php echo $seats[3]; ?></td>
 </tr>
 <tr>
  <td align="center"><?php echo $seats[0]; ?></td><td align="center" style="background: black; color: white;" id="out">&nbsp;</td><td align="center"><?php echo $seats[4]; ?></td>
 </tr>
 <tr>
  <td align="center" colspan="3" style="padding: 8px;" id="player">
  </td>
 </tr>
</table>
<script type="text/javascript">
    var enableCache = false;
    ajax_loadContent('player', 'the_player.php');
</script>
</body>
</html>