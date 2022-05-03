<?php
session_id("cryptor");
session_start();
class Cryptor
{
    private $B64Chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+\=";

    public function genKey()
    {
        return str_shuffle($this->B64Chars);
    }

    public function doEncode($src, $key)
    {
        $src=@strrev(@strtr(@base64_encode(@gzdeflate($src, 9)), $this->B64Chars, $key));

        return $src;
    }

    public function doDecode($src, $key)
    {
        $src=@gzinflate(@base64_decode(@strtr(@strrev($src), $key, $this->B64Chars)));

        return $src;
    }
}
$c = new Cryptor();

if (isset($_POST) && !empty($_POST)) {
    if (isset($_GET['nav']) && $_GET['nav']=="encode") {
        $enctext = $c->doEncode($_POST['plaintext'], $_POST['key']);
        die("<a href='javascript:history.back();' class='btnLink'>&lt; Back</a><br /><strong>Encoded Text:</strong><br /><textarea style='width: 100%;'>$enctext</textarea>");
    } elseif (isset($_GET['nav']) && $_GET['nav']=="decode") {
        $dectext = $c->doDecode($_POST['encodedtext'], $_POST['key']);
        die("<a href='javascript:history.back();' class='btnLink'>&lt; Back</a><br /><strong>Decoded Text:</strong><br /><textarea style='width: 100%;'>$dectext</textarea>");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cryptor</title>
    <style>
        body {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        textarea {
            width: 100%;
        }

        input[type='text'] {
            width: 100%;
        }

        input[type='submit'] {
            margin-top: 5px;
        }

        a.btnLink {
            padding: 3px 7px 3px 7px;
            background-color: #06c;
            color: white;
            text-decoration: none;
            ;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }

        a.btnLink:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <a href='?nav=encode' class='btnLink'>Encode</a>&nbsp;&nbsp;&nbsp;<a href='?nav=decode'
        class='btnLink'>Decode</a>&nbsp;&nbsp;&nbsp;<a href='?nav=generate' class='btnLink'>Generate Key</a><br /><br />
    <?php
    if (!isset($_GET['nav']) || $_GET['nav']=="home") {
        echo("<br />Welcome to Cryptor, Please click a link above to get started!<br />");
    } elseif (isset($_GET['nav']) && $_GET['nav']=='encode') {
        if (isset($_SESSION['key'])) {
            $key = $_SESSION['key'];
        } else {
            $key="";
        }
        echo('<form method="POST" action="">
            <strong>Plain Text:</strong><br />
            <textarea name="plaintext"></textarea><br />
            <strong>Key:</strong><br />
            <input name="key" type="text" value="'.$key.'" /><br />
            <input name="submitbtn" value="Encode" type="submit" /> 
        </form>');
    } elseif (isset($_GET['nav']) && $_GET['nav']=='decode') {
        if (isset($_SESSION['key'])) {
            $key = $_SESSION['key'];
        } else {
            $key="";
        }
        echo('<form method="POST" action="">
            <strong>Encoded Text:</strong><br />
            <textarea name="encodedtext"></textarea><br />
            <strong>Key:</strong><br />
            <input name="key" type="text" value="'.$key.'" /><br />
            <input name="submitbtn" value="Decode" type="submit" /> 
        </form>');
    } elseif (isset($_GET['nav']) && $_GET['nav']=='generate') {
        $_SESSION['key']=$c->genKey();
        echo($_SESSION['key']);
    }
    ?>
    <br /><small><i>Version: 1.0.0</i></small>
</body>

</html>
