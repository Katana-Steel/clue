<?php
require_once("clue.php");
session_start();
    $_SESSION['player']->setPicks($_GET['per'],$_GET['wea'],$_GET['pla']);
    $turn = $_GET['turn'];
    $res = $_SESSION['game']->takeTurn($turn);
        
    if(count($res) > 0) {
        $_SESSION['player']->store($res);
        foreach( $res as $line )
            echo $line . "<br />\n";
        if(!$_SESSION['player']->status || $turn == "g") {
            echo "<br />\nYou got killed in the back by the perp for picking the wrong circumstances";
            session_destroy();
        }
    } else {
        switch($turn) {
        case 't':
            echo "<br />The others said That could be the one.<br />\n";
            break;
        case 'g':
            foreach( $_SESSION['player']->picks as $deed )
                echo "${deed}<br />\n";
            echo "This was indeed the right circumstances\n";
            session_destroy();
        }
    }
?>
<script type="text/script">
ajax_loadContent("player", "the_player.php");
</script>
