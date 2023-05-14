
class Game extends Phaser.Game {
  Game(){
    Phaser.Game({
      type: Phaser.AUTO,  //Phaser will decide how to render our game (WebGL or Canvas)
      parent : 'game',
      width: 830, // game width
      height: 640, // game height
    });
  }
}

window.onload = function(){
  console.log("create game");
  game = new Game();
  game.Game();
}
