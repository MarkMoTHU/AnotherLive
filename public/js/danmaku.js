/**
 * Created by Volio on 2016/3/13.
 */
var firstFlag = true;
var rt;
var room;
var inputSend = document.getElementById('danmaku');

bindEvent(document.body, 'keydown', function(e) {
    if (e.keyCode === 13) {
        if (firstFlag) {
            main();
        } else {
            sendMsg();
        }
    }
});

function danmakuInit(appId,roomId) {
    var clientId = '游客';
    
    $.ajax({
        type: 'GET',
        url: '../api/user/getinfo?type=name',
        dataType: 'json',
        cache: false,
        success:function (data) {
            if(data.name){
                clientId = data.name;
            }
            main(appId,roomId,clientId);
        },
        error:function () {
            main(appId,roomId,clientId);
        }
    });
}

function bindEvent(dom, eventName, fun) {
    if (window.addEventListener) {
        dom.addEventListener(eventName, fun);
    } else {
        dom.attachEvent('on' + eventName, fun);
    }
}

function main(appId,roomId,clientId){
    printWall.innerHTML = null;
    showMessage('系统','正在连接弹幕服务器...');
    if (!firstFlag) {
        rt.close();
    }

    rt = AV.realtime({
        appId: appId,
        clientId: clientId,
        secure: true
    });

    rt.on('open', function() {
        firstFlag = false;
        showMessage('系统','弹幕服务器连接成功！');
        // 获得已有房间的实例
        rt.room(roomId, function(object) {

            // 判断服务器端是否存在这个 room，如果存在
            if (object) {
                room = object;

                // 当前用户加入这个房间
                room.join(function() {

                    // 获取成员列表
                    /*room.list(function(data) {
                     showMessage('成员列表', data);
                     rt.ping(data.slice(0, 20), function(list) {
                     showMessage('当前在线', list);
                     });
                     });*/

                });

                // 房间接受消息
                room.receive(function(data) {
                    showMsg(data,true);
                    printWall.scrollTop = printWall.scrollHeight;
                });

                room.log(function(data) {
                    var l = data.length;

                    for (var i = 0; i < l; i++) {
                        showMsg(data[i]);
                    }
                });
            } else {
                showMessage('系统','弹幕服务器连接失败...');
            }
        });
    });

    rt.on('close', function() {
        showMessage('系统','弹幕服务器被断开...');
    });

    // 监听服务情况
    // rt.on('reuse', function() {
    //     showMessage('系统','弹幕服务器正在重连，请耐心等待...');
    // });

    // 监听错误
    rt.on('error', function() {
        showMessage('系统','弹幕服务器遇到错误...');
    });
}

function showMessage(name, data,danmuma) {
    if (data) {
        console.log(name, data);
        msg = '<a>' + encodeHTML(name) + '</a> : <span>' + encodeHTML(data) + '</span>'
    }
    var div = document.createElement('div');
    div.className = 'user-danmaku';
    div.innerHTML = msg;
    if(danmuma) {
        sendDanmaku(encodeHTML(data));
    }
    printWall.appendChild(div);
    if(printWall.childNodes.length>150){
        printWall.removeChild(printWall.childNodes[0]);
    }
}

function encodeHTML(source) {
    return String(source)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function showMsg(data,danmuma) {
    var text = '';
    var from = data.fromPeerId;
    if (data.msg.type) {
        text = data.msg.text;
    } else {
        text = data.msg;
    }
    if (String(text).replace(/^\s+/, '').replace(/\s+$/, '')) {
        showMessage(encodeHTML(from), text, danmuma);
    }
}

function sendMsg() {

    // 如果没有连接过服务器
    if (firstFlag) {
        alert('请先连接服务器！');
        return;
    }
    var val = inputSend.value;

    // 不让发送空字符
    if (!String(val).replace(/^\s+/, '').replace(/\s+$/, '')) {
        alert('请输入点文字！');
    }else {
        //发送消息
        room.send({
            text: val
        }, {
            type: 'text'
        }, function (data) {
            inputSend.value = '';
            showMessage('我', val,true);
            printWall.scrollTop = printWall.scrollHeight;
        });
    }
}

$("#danmu").danmu({
    left:0,
    top:5,
    height:210,
    width:"100%",
    speed:7000,
    opacity:1,
    font_size_small:16,
    font_size_big:24,
    top_botton_danmu_time:6000
});

$('#danmu').danmu('danmuStart');

function sendDanmaku(message){
    var text = message;
    var color = "#FFFFFF";
    var position = 0;
    var time = $('#danmu').data("nowTime")+1;
    var size = 1;
    var text_obj='{ "text":"'+text+'","color":"'+color+'","size":"'+size+'","position":"'+position+'","time":'+time+'}';
    var new_obj=eval('('+text_obj+')');
    $('#danmu').danmu("addDanmu",new_obj);
}

function showOrHideDanmu() {
    if(danmu.style.display == 'none')
        $('#danmu').show();
    else
        $('#danmu').hide();
}