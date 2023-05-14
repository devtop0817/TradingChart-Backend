<!DOCTYPE HTML>
<html lang="en">
<head>
   <title>Phaser Game</title>
   <script src="//cdn.jsdelivr.net/npm/phaser@3.16.2/dist/phaser.js"></script>
</head>
<body>
<div id="game"></div>

<script type="text/javascript">
   (function () {
       var game = new Phaser.Game({
           type: Phaser.AUTO,
           parent: 'game',
           width: 1024,
           height: 768
       });
   })();
</script>

</body>
</html>