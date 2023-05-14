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