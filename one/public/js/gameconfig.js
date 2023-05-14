export class LadderDef {
    static ServerTime = 0;
    static RouteBoardPosition = [325, 390];
    static CurrentTimePosition = [325, 242];
    static LeftTimeBoradPosition = [131, 30];
    static HistoryPosition = [502, 278];
    static HistoryWidgetSize = [200, 300];
    static HistoryItemPosition = [0,0];
    static HistoryItemHeight = 60;
    static ProgressBarPosition = [194, 360];
    static ProgressBarSize = [262, 60];
    static ResultBoardPosition = [326, 390];

    static startImageArray = ['left.png', 'right.png'];
    static lineImageArray = ['three.png', 'four.png'];
    static endImageArray = ['odd.png', 'even.png'];


    static RoundResult = "";
    static GamePlayingStatus = -1;
    static GameReady = 1;
    
    static RoundHistory = [];
    static CurrentHistoryScrollIndex = 0;
}

export class GameNet {
    constructor(scene){
        this.scene = scene;
    }

    getHistory(){
        $.ajax({
            url:"backend/gethistory.php",
            type:"GET",
            cache:false,
            success:(data) => {
                data = JSON.parse(data);
                LadderDef.ServerTime = new Date(data.time).getTime();
                let history = data.history;
                for(let i = 0 ; i < history.length ; i++){
                    if(history[i].operate > 0 && history[i].proc_status == 0) continue;
                    LadderDef.RoundHistory.push({
                        round:history[i].b_round,
                        b_start:history[i].b_start,
                        b_line:history[i].b_line,
                        b_end:history[i].b_end
                    });
                }
                this.scene.loadHistory();

                $.ajax({
                    url:"backend/getplayingstatus.php",
                    type:"GET",
                    cache:false,
                    success:(data) => {
                        LadderDef.GamePlayingStatus = data;
                    }
                });
                
            },
            error:function(error){
            }
        })
        
    }

    

    getRoundResult(round){
        $.ajax({
            url:"backend/getplayingstatus.php",
            type:"GET",
            cache:false,
            success:(data) => {
                LadderDef.GamePlayingStatus = data;
                if(LadderDef.GamePlayingStatus == 0) return;
                $.ajax({
                    url:"backend/getroundresult.php",
                    type:"POST",
                    data:{
                        round:round
                    },
                    cache:false,
                    success:(data) => {
                        LadderDef.RoundResult = data;
                    },
                    error:() => {
        
                    }
                })
            },
            error:() => {

            }
        })



        
    }

}


export class GameScene extends Phaser.Scene{
    constructor(option){
        super({
            key:"Game"
        })
        this.option = option;
    }
  
    preload() {
        this.load.atlas('MainAtlas', 'public/img/MainAtlas.png', 'public/img/MainAtlas.json');
        this.game.registry.events._events.blur = [];
        this.game.registry.events._events.focus = [];
        this.game.registry.events._events.hidden = [];
    }

    create(){
        this.ladderContainer = this.add.container(0, 0);
        this.ladderContainer.setSize(830, 640);
        this.ladderContainer.setDisplaySize(830, 640);
        
        this.historyContainer = this.add.container(LadderDef.HistoryPosition[0], LadderDef.HistoryPosition[1]);
        this.historyContainer.setSize(LadderDef.HistoryWidgetSize[0], LadderDef.HistoryWidgetSize[1]);
        this.historyContainer.setDisplaySize(LadderDef.HistoryWidgetSize[0], LadderDef.HistoryWidgetSize[1]);
        
        this.progressContainer = this.add.container(LadderDef.ProgressBarPosition[0], LadderDef.ProgressBarPosition[1]);
        
        this.resultContainer = this.add.container(LadderDef.ResultBoardPosition[0],LadderDef.ResultBoardPosition[1]);

        this.ladderContainer.setDepth(1);
        this.historyContainer.setDepth(1);
        this.progressContainer.setDepth(3);
        this.resultContainer.setDepth(2);
        this.resultContainer.setVisible(false);

        this.gameNet = new GameNet(this);
        this.initGraph();
        

        
        window.addEventListener("blur", (e)=>{
            this.tweens._add = [];
        })
        
    }

    initGraph(){
        this.bg = this.add.sprite(0,0,"MainAtlas", 'background.png');
        this.bg.setOrigin(0,0);
        this.bg.setDisplaySize(830,640);
        this.ladderContainer.add(this.bg);

        this.route_board = this.add.sprite(LadderDef.RouteBoardPosition[0],LadderDef.RouteBoardPosition[1],"MainAtlas", "route_board.png");
        this.route_board.setOrigin(0.5,0.5);
        this.ladderContainer.add(this.route_board);

        this.displayTime = this.add.text(LadderDef.CurrentTimePosition[0], LadderDef.CurrentTimePosition[1], "", {
            fontFamily:"굴림,Gulim,Helvetica,sans-serif",
            fontSize:"16px",
            color:'#331002',
            stroke:'#331002',
            strokeThickness: 1,
            // shadow: {
            //     offsetX: 1,
            //     offsetY: 1,
            //     color: '#51392b',
            //     blur: 3,
            //     stroke: false,
            //     fill: false
            // },
            fontStyle:"bold"
        }).setOrigin(0.5,0);
        this.ladderContainer.add(this.displayTime);

        this.leftTimeBoard = this.add.sprite(LadderDef.LeftTimeBoradPosition[0], LadderDef.LeftTimeBoradPosition[1], "MainAtlas", "left_time_board.png");
        this.leftTimeBoard.setOrigin(0.5, 0.5);
        this.progressContainer.add(this.leftTimeBoard);

        this.progress = this.add.graphics({
            x : 0,
            y : 0,
            fillStyle : {
                color : 0x249218,
                alpha : 1
            }
        });
        this.progress.fillRoundedRect(0,0,LadderDef.ProgressBarSize[0],LadderDef.ProgressBarSize[1], 30);
        this.progressContainer.add(this.progress);

        this.progressMask = this.make.graphics({
            x: LadderDef.ProgressBarPosition[0],
            y: LadderDef.ProgressBarPosition[1],
            fillStyle: {
                color:0x000000,
                alpha:1
            }
        })
        // this.progressContainer.add(progressMask);
        this.progress.mask = new Phaser.Display.Masks.BitmapMask(this, this.progressMask);
        this.progressMask.fillRoundedRect(0,0,LadderDef.ProgressBarSize[0],LadderDef.ProgressBarSize[1], 30);

        this.progressText = this.add.text(LadderDef.ProgressBarSize[0]/2,LadderDef.ProgressBarSize[1]/2,"-분 --초후 --회차 추첨 시작", {
            fontFamily:"굴림,Gulim,Helvetica,sans-serif",
            fontSize:"12px",
            color:'#ffffff',
            fontStyle:"bold"
        }).setOrigin(0.5,0.5);
        this.progressContainer.add(this.progressText);

        this.roundResultText = this.add.text(LadderDef.ProgressBarSize[0]/2,LadderDef.ProgressBarSize[1]/2, "", {
            fontFamily:"굴림,Gulim,Helvetica,sans-serif",
            fontSize:"12px",
            color:'#ffffff',
            fontStyle:"bold"
        }).setOrigin(0.5,0.5);

        this.progressContainer.add(this.roundResultText);
        this.roundResultText.setVisible(false)

        
        
        this.rect = new Phaser.Geom.Rectangle(0, 0, 830, 640);
        this.historyContainer.setInteractive(this.rect, Phaser.Geom.Rectangle.Contains)
        // this.historyContainer.on('wheel', (param) => {
        //     this.scrollHistory(param.deltaY);
        // });

        this.initHistory();
        


        let current_time = LadderDef.ServerTime;
        let delay_time = 1000 - (current_time % 1000);
        LadderDef.ServerTime = LadderDef.ServerTime - LadderDef.ServerTime % 1000;
        
        this.startInterval();
        
        
    }

   

    startInterval(){
        this.timer = setInterval(()=>{
            this.interval();
        }, 1000);
    }

    interval(){
        LadderDef.ServerTime += 1000;
        
        this.displayTime.setText(this.getDateTimeString());

        let currentTime = new Date(LadderDef.ServerTime);
        let pastSeconds = (currentTime.getMinutes() * 60 + currentTime.getSeconds()) % this.option.timeCycle;
        let x = pastSeconds * LadderDef.ProgressBarSize[0] / this.option.timeCycle;
        //this.progressMask.setPosition(LadderDef.ProgressBarPosition[0]-x, LadderDef.ProgressBarPosition[1]);
        this.tweens.add({
            targets:this.progressMask,
            x:LadderDef.ProgressBarPosition[0]-x,
            duration:500,
            ease: 'Power2'
        }, this)
        let next_round = (currentTime.getHours() * 60 + currentTime.getMinutes()) / (this.option.timeCycle / 60) + 1;
        let seconds = this.option.timeCycle - pastSeconds;
        this.progressText.setText((seconds > 59 ? Math.floor(seconds/60) + "분 ":"") + (seconds%60) + "초후 " + next_round + "회차 추첨 시작");
      
        if(seconds == 3){ //추첨결과를 가져와요
            this.gameNet.getRoundResult(next_round);
        }
        if(LadderDef.GamePlayingStatus == 0){
            LadderDef.GameReady = 0;
            this.disableGame();
            return;
        }
        
        if(seconds == this.option.timeCycle){ //현재 회차가 끝난거죠
            if(LadderDef.GameReady == 0) {
                LadderDef.GameReady = 1;
                this.EnableGame();
                return;
            }
            this.showRoundResult(next_round-1);
        }
        
    }

    disableGame(){
        this.progressContainer.setVisible(true);
        this.progress.setVisible(false);
        this.progressText.setVisible(false);
        this.roundResultText.setText("점검중입니다.");
        this.roundResultText.setVisible(true);
    }

    EnableGame(){
        this.progressContainer.setVisible(true);
        this.progress.setVisible(true);
        this.progressText.setVisible(true);
        // this.roundResultText.setText("점검중입니다.");
        this.roundResultText.setVisible(false);
    }


    getDateTimeString(){
        let dateTimeString = "";
        let currentTime = new Date(LadderDef.ServerTime);
        dateTimeString += currentTime.getFullYear() + ".";
        dateTimeString += ("0" + (currentTime.getMonth() + 1)).substr((currentTime.getMonth() + 1).length - 1) + ".";
        dateTimeString += ("0" + currentTime.getDate()).substr(("0" + currentTime.getDate()).length - 2) + " ";
        dateTimeString += ("0" + currentTime.getHours()).substr(("0" + currentTime.getHours()).length - 2) + ":";
        dateTimeString += ("0" + currentTime.getMinutes()).substr(("0" + currentTime.getMinutes()).length - 2) + ":";
        dateTimeString += ("0" + currentTime.getSeconds()).substr(("0" + currentTime.getSeconds()).length - 2);
        return dateTimeString;
    }

    initHistory(){
        this.gameNet.getHistory();
    }

    addHistory(round, result){
        if(LadderDef.RoundHistory[0].round == round) return;
        LadderDef.RoundHistory = [{
            round:round,
            b_start:result.substring(0, 1) * 1,
            b_line:result.substring(1, 2) * 1,
            b_end:result.substring(2) * 1
        }].concat(LadderDef.RoundHistory);
        if(LadderDef.RoundHistory.length > 500) LadderDef.RoundHistory.pop();
        this.loadHistory();
    }

    loadHistory(){
        let str = "<ul style='width:100%;padding:0px;margin:0px;'>"
        for(let i = 0 ; i < LadderDef.RoundHistory.length ; i++){
            str+="<li style='background:url(public/img/history_item.png) no-repeat;height:55px;padding:3px;list-style: none;text-align: center;border-radius:3px'>";
            str+="<div style='padding-top: 8px;'><div style='width:30px;float:left;padding-top: 8px;padding-left: 5px;text-align:center'><label style='color: #d6bc99;font-family: Tahom, sans-serif;font-size:12px;'>" + LadderDef.RoundHistory[i].round + "</label></div>"
            str+="<img style='vertical-align: middle;' src='public/img/"+ LadderDef.startImageArray[LadderDef.RoundHistory[i].b_start] +"'>"
            str+="<img style='vertical-align: middle;margin-left:3px' src='public/img/" + LadderDef.lineImageArray[LadderDef.RoundHistory[i].b_line] + "'>"
            str+="<img style='vertical-align: middle;margin-left:3px' src='public/img/" + LadderDef.endImageArray[LadderDef.RoundHistory[i].b_end] + "'>"
            str+="</div></li>"
        }
        str += "</ul>"
        document.getElementById('history').innerHTML = str;

    }

    
    showRoundResult(round){
        this.progressContainer.setVisible(false);
        this.resultContainer.removeAll();
        this.resultContainer.setVisible(true);
        
        if(LadderDef.RoundResult == "" || LadderDef.RoundResult.length < 3){
            return this.displayRoundResult(round, LadderDef.RoundResult);
        }

        if(!(document.hidden)){
            let left_right_result = this.add.sprite(LadderDef.RoundResult.substring(0,1)==0?-80:81,-75, "MainAtlas", LadderDef.RoundResult.substring(0,1)==0?"result_left.png":"result_right.png");
            
            this.resultContainer.add(left_right_result);

            
            let back_img_name = ["three_lines.png", "four_lines.png"];
            let line_flag = LadderDef.RoundResult.substring(1, 2)
            let LinesBack = this.add.sprite(0,0,"MainAtlas", back_img_name[line_flag]);
            LinesBack.setOrigin(0.5,0.5);
            this.resultContainer.add(LinesBack);
            let start_flag = 1;
            if(LadderDef.RoundResult.substring(0, 1) == 1) start_flag = -1

            let vertical_line_1 = this.add.sprite(-80*start_flag,-57, "MainAtlas", "line_pattern.png");
            vertical_line_1.setOrigin(0.5,0);
            this.resultContainer.add(vertical_line_1);
            this.tweens.add({
                targets:vertical_line_1,
                scaleY:{
                    from:0,
                    to:line_flag==0?4.2:3.2
                },
                duration:200,
                ease: 'Power2',
                onComplete: () => {
                    let horizontal_line1 = this.add.sprite(-80*start_flag, line_flag==0?-20:-30, "MainAtlas", "line_pattern.png");
                    horizontal_line1.setOrigin(start_flag==-1?1:0,0.5);
                    this.resultContainer.add(horizontal_line1);
                    this.tweens.add({
                        targets:horizontal_line1,
                        scaleX:{
                            from:1,
                            to:16.6
                        },
                        duration:400,
                        ease: 'Power2',
                        onComplete: () => {
                            let vertical_line_2 = this.add.sprite(81*start_flag,line_flag==0?-25:-35, "MainAtlas", "line_pattern.png");
                            vertical_line_2.setOrigin(0.5,0);
                            this.resultContainer.add(vertical_line_2);
                            this.tweens.add({
                                targets:vertical_line_2,
                                scaleY:{
                                    from:1,
                                    to:3
                                },
                                duration:200,
                                ease: 'Power2',
                                onComplete: () => {
                                    let horizontal_line2 = this.add.sprite(81*start_flag,line_flag==0?0:-10, "MainAtlas", "line_pattern.png");
                                    horizontal_line2.setOrigin(start_flag==-1?0:1,0.5);
                                    this.resultContainer.add(horizontal_line2);
                                    this.tweens.add({
                                        targets:horizontal_line2,
                                        scaleX:{
                                            from:1,
                                            to:16.6
                                        },
                                        duration:400,
                                        ease: 'Power2',
                                        onComplete: () => {
                                            let vertical_line_3 = this.add.sprite(-80*start_flag,line_flag==0?-5:-15, "MainAtlas", "line_pattern.png");
                                            vertical_line_3.setOrigin(0.5,0);
                                            this.resultContainer.add(vertical_line_3);
                                            this.tweens.add({
                                                targets:vertical_line_3,
                                                scaleY:{
                                                    from:1,
                                                    to:3
                                                },
                                                duration:200,
                                                ease: 'Power2',
                                                onComplete: () => {
                                                    let horizontal_line3 = this.add.sprite(-80*start_flag,line_flag==0?20:10, "MainAtlas", "line_pattern.png");
                                                    horizontal_line3.setOrigin(start_flag==-1?1:0,0.5);
                                                    this.resultContainer.add(horizontal_line3);
                                                    this.tweens.add({
                                                        targets:horizontal_line3,
                                                        scaleX:{
                                                            from:1,
                                                            to:16.6
                                                        },
                                                        duration:400,
                                                        ease: 'Power2',
                                                        onComplete: () => {
                                                            let vertical_line_4 = this.add.sprite(81*start_flag, line_flag==0?15:5,"MainAtlas", "line_pattern.png");
                                                            vertical_line_4.setOrigin(0.5, 0);
                                                            this.resultContainer.add(vertical_line_4);
                                                            if(line_flag == 0){
                                                                this.tweens.add({
                                                                    targets:vertical_line_4,
                                                                    scaleY:{
                                                                        from:1,
                                                                        to:4.2
                                                                    },
                                                                    duration:200,
                                                                    ease:'Poser2',
                                                                    onComplete: () => {
                                                                        
                                                                    }
                                                                }, this)
                                                            }
                                                            else{
                                                                this.tweens.add({
                                                                    targets:vertical_line_4,
                                                                    scaleY:{
                                                                        from:1,
                                                                        to:3
                                                                    },
                                                                    duration:200,
                                                                    ease:'Poser2',
                                                                    onComplete: () => {
                                                                        let horizontal_line4 = this.add.sprite(81*start_flag,30, "MainAtlas", "line_pattern.png");
                                                                        horizontal_line4.setOrigin(start_flag==-1?0:1,0.5);
                                                                        this.resultContainer.add(horizontal_line4);
                                                                        this.tweens.add({
                                                                            targets:horizontal_line4,
                                                                            scaleX:{
                                                                                from:1,
                                                                                to:16.6
                                                                            },
                                                                            duration:400,
                                                                            ease: 'Power2',
                                                                            onComplete: () => {
                                                                                let vertical_line_5 = this.add.sprite(-80*start_flag, 25, "MainAtlas", "line_pattern.png");
                                                                                vertical_line_5.setOrigin(0.5,0);
                                                                                this.resultContainer.add(vertical_line_5);
                                                                                this.tweens.add({
                                                                                    targets:vertical_line_5,
                                                                                    scaleY:{
                                                                                        from:1,
                                                                                        to:3.2
                                                                                    },
                                                                                    duration:200,
                                                                                    ease: 'Power2',
                                                                                    onComplete: () => {
                                                                                        
                                                                                    }
                                                                                }, this)
                                                                            }
                                                                        }, this)
                                                                    }
                                                                }, this)
                                                            }
                                                        }
                                                    }, this)
                                                }
                                            }, this)
                                        }
                                    }, this)
                                }
                            }, this)
                        }
                    }, this)
                }
            }, this)
        }
        setTimeout(()=>{
            this.addHistory(round, LadderDef.RoundResult);
            this.displayRoundResult(round, LadderDef.RoundResult);
        }, 3000)
    }

    displayRoundResult(round, result){
        if(result.length == 3){
            let odd_even_result = this.add.sprite(LadderDef.RoundResult.substring(2)==0?-80:81, 75, "MainAtlas", LadderDef.RoundResult.substring(2)==0?"result_odd.png":"result_even.png");
            this.resultContainer.add(odd_even_result);
        }
        this.progressContainer.setVisible(true);
        this.progress.setVisible(false);
        this.progressText.setVisible(false);
        if(result.length == 3){
            this.roundResultText.setText(round+"회차 결과는 [" + this.convertRoundResult(result) + "] 입니다")
        }
        else {
            this.roundResultText.setText("추첨결과 집계중입니다.");
        }
        this.roundResultText.setVisible(true)

        setTimeout(()=>{
            this.resultContainer.setVisible(false);
            this.progress.setVisible(true);
            this.progressText.setVisible(true);
            this.roundResultText.setVisible(false)
        }, 5000)

    }
    
    convertRoundResult(result){
        let stringResult = "";
        stringResult += result.substring(0,1)==0?"좌":"우";
        stringResult += result.substring(1,2) * 1 + 3;
        stringResult += result.substring(2)==0?"홀":"짝";
        return stringResult;
    }

    scrollHistory(delta){
        if(delta > 0) LadderDef.CurrentHistoryScrollIndex += 3;
        else LadderDef.CurrentHistoryScrollIndex -= 3;
        if(LadderDef.CurrentHistoryScrollIndex < 0) LadderDef.CurrentHistoryScrollIndex = 0;
        if(LadderDef.CurrentHistoryScrollIndex > LadderDef.RoundHistory.length - 3) LadderDef.CurrentHistoryScrollIndex = LadderDef.RoundHistory.length - 3;
        this.loadHistory();
    }
}


// let loadScene = new LoadScene();
let option = {
    timeCycle : 60
}
let gameScene = new GameScene(option);
export default {
    type: Phaser.AUTO,  //Phaser will decide how to render our game (WebGL or Canvas)
    parent : 'game',
    width: 830, // game width
    height: 640, // game height
    scene:gameScene
};