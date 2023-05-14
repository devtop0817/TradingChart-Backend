import { LadderDef } from "./ladderdef.js";

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