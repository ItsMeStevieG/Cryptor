<?php
session_id("cryptor");
session_start();
$cryptorversion="1.2.0";

//Main Crytor Class
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

//Check is Generated Key Exists in Session
if (isset($_SESSION['key'])) {
    $key = $_SESSION['key'];
} else {
    $key="";
}

//Active Link Menu Handler
if (!isset($_GET['nav']) || $_GET['nav']=="") {
    $homeactive="active";
} else {
    $homeactive="";
}

if (isset($_GET['nav']) && $_GET['nav']=="encode") {
    $encodeactive="active";
} else {
    $encodeactive="";
}

if (isset($_GET['nav']) && $_GET['nav']=="decode") {
    $decodeactive="active";
} else {
    $decodeactive="";
}

if (isset($_GET['nav']) && $_GET['nav']=="generate") {
    $generateactive="active";
} else {
    $generateactive="";
}

$content="<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"UTF-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Cryptor</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\"
        integrity=\"sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3\" crossorigin=\"anonymous\">
</head>

<body>
    <nav class=\"navbar navbar-expand-lg navbar-dark bg-primary\">
        <div class=\"container-fluid\">
            <a class=\"navbar-brand\" href=\"#\">Cryptor</a>
            <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\"
                data-bs-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\"
                aria-label=\"Toggle navigation\">
                <span class=\"navbar-toggler-icon\"></span>
            </button>
            <div class=\"collapse navbar-collapse\" id=\"navbarSupportedContent\">
                <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">
                    <li class=\"nav-item\">
                        <a class=\"nav-link $homeactive\" aria-current=\"page\" href=\"index.php\">Home</a>
                    </li>
                    <li class=\"nav-item\">
                        <a class=\"nav-link $encodeactive\" href=\"?nav=encode\">Encode</a>
                    </li>
                    <li class=\"nav-item\">
                        <a class=\"nav-link $decodeactive\" href=\"?nav=decode\">Decode</a>
                    </li>
                    <li class=\"nav-item\">
                        <a class=\"nav-link $generateactive\" href=\"?nav=generate\">Generate</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Begin page content -->
    <main class=\"flex-shrink-0\">
        <div class=\"container-fluid pt-3\">";

            if (!isset($_GET['nav']) || $_GET['nav']=="") {
                $content.="<h1>Welcome to Cryptor</h1>
                <p class=\"lead\">Please click one of the links above to get started!</p>";
            } elseif (isset($_GET['nav']) && $_GET['nav']=="encode") {
                if (!isset($_POST['plaintext']) && !isset($_POST['secretkey'])) {
                    $content.="<form method=\"post\">
                    <div class=\"mb-3\">
                    <label for=\"PlainTextInput\" class=\"form-label\">Plain Text:</label>
                    <textarea name=\"plaintext\" class=\"form-control\" id=\"PlainTextInput\" rows=\"3\"></textarea>
                    </div>
                    <div class=\"mb-3\">
                        <label for=\"SecretKeyInput\" class=\"form-label\">Secret Key</label>
                        <input name=\"secretkey\" type=\"text\" value=\"$key\" class=\"form-control\" id=\"SecretKeyInput\" placeholder=\"Secret Key\">
                    </div>
                    <div class=\"mb-3\">
                        <button type=\"submit\" class=\"btn btn-primary\">Encode</button>
                    </div>
                </form>";
                } else {
                    $_SESSION['key']=$_POST['secretkey'];
                    $encodedtext=$c->doEncode($_POST['plaintext'], $_POST['secretkey']);
                    $content.="<div class=\"mb-3\">
                        <a href='javascript:history.back();' class='btn btn-primary'>&lt; Back</a>
                    </div>
                    <strong>Encoded Text: </strong>$encodedtext";
                }
            } elseif (isset($_GET['nav']) && $_GET['nav']=="decode") {
                if (!isset($_POST['encodedtext']) && !isset($_POST['secretkey'])) {
                    $content.="<form method=\"post\">
                    <div class=\"mb-3\">
                    <label for=\"EncodedTextInput\" class=\"form-label\">Encoded Text:</label>
                    <textarea name=\"encodedtext\" class=\"form-control\" id=\"EncodedTextInput\" rows=\"3\"></textarea>
                    </div>
                    <div class=\"mb-3\">
                        <label for=\"SecretKeyInput\" class=\"form-label\">Secret Key</label>
                        <input name=\"secretkey\" type=\"text\" value=\"$key\" class=\"form-control\" id=\"SecretKeyInput\" placeholder=\"Secret Key\">
                    </div>
                    <div class=\"mb-3\">
                        <button type=\"submit\" class=\"btn btn-primary\">Decode</button>
                    </div>
                </form>";
                } else {
                    $_SESSION['key']=$_POST['secretkey'];
                    $decodedtext=$c->doDecode($_POST['encodedtext'], $_POST['secretkey']);
                    $content.="<div class=\"mb-3\">
                        <a href='javascript:history.back();' class='btn btn-primary'>&lt; Back</a>
                    </div>
                    <strong>Decoded Text: </strong>$decodedtext";
                }
            } elseif (isset($_GET['nav']) && $_GET['nav']=="generate") {
                $_SESSION['key']=$c->genKey();
                $content.="<div class=\"mb-3\">
                    <a href='javascript:history.back();' class='btn btn-primary'>&lt; Back</a>
                </div>
                <strong>Secret Key:</strong> ".$_SESSION['key'];
            }
            
        
        $content.="</div>
    </main>

    <footer class=\"footer mt-3 py-3 bg-light\">
        <div class=\"container-fluid\">
            <span class=\"text-muted\">Version $cryptorversion</span>
        </div>
    </footer>

    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\"
        integrity=\"sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p\" crossorigin=\"anonymous\">
    </script>
</body>

</html>";

echo($content);
