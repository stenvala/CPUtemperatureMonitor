<!doctype html>
<html>
  <!--

  Main app
  (c) Antti Stenvall
  antti@stenvall.fi

  -->

  <head>
    <meta charset="utf-8">
    <title>CPU Temperature Monitor</title>
    <script src="js/jquery/2.1.0.min.js"></script>
    <script src="js/jquery/extensions/backstretch.js"></script>
    <script src="js/jquery/extensions/flot/0.8.3.js"></script>
    <script src="js/jquery/extensions/flot/plugins/axislabels.js"></script>
    <script src="js/ui.js"></script>
    <link href="css/style.php" rel="stylesheet" type="text/css">
  </head>
  <body>
    <script>
      $.backstretch('fig/bg.jpg');
    </script>
    <div id="cred" onClick="location.href = '../'">
      &copy; mathcodingclub.com 2013 -
      <script>
      document.write(new Date().getFullYear());
      </script>
    </div>
    <div id="monitor">
      This is <b>CPU Temperature Monitor</b>
    </div>
    <script>
      $(document).ready(function() {
        var u = new ui.create();
        $('#monitor').bind('click', function() {
          u.main();
        })
      });
    </script>
  </body>
</html>