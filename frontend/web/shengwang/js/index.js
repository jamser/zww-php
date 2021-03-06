"use strict";

(function ($) {
    $(function () {
        var socket = null;
        var images = [];
        var currentIdx = 0;
        var record_service_started = false;
        var frame_rate = 50;

        function playImage() {
            if (images.length > 0) {
                var img = document.querySelector("#image");
                var imageUrl = null;
                if (currentIdx >= images.length) {
                    imageUrl = images[images.length - 1];
                } else {
                    imageUrl = images[currentIdx++];
                }

                if (images.length - currentIdx > 100) {
                    //if falling behind too much, jump frame
                    currentIdx = images.length - 1;
                    console.log("jumping frame");
                }

                console.log("playing image " + currentIdx + "/" + images.length);
                img.src = imageUrl;
            }
            if (socket !== null) {
                setTimeout(playImage, 1000 / frame_rate);
            } else {
                var _img = document.querySelector("#image");
                _img.src = null;
            }
        }

        function subscribeEvents() {
            $('.connect_btn').off("click").on("click", function (e) {
                //connect io
                if (!socket) {
                    // socket = io();
                    var channel = $('.channelField').val();
                    var appid = $('.idField').val();
                    socket = io('http://dev.365zhuawawa.com:3000/?channel='+channel+'&appid='+appid);
                    //socket = io({
                    //    query: {
                    //        channel: channel,
                    //        appid: appid
                    //    }
                    //});
                    subscribeSocketEvents(socket);
                    $('.record_btn').hide();
                    $('.connect_btn').hide();
                } else {
                    socket.close();
                    socket = null;
                    record_service_started = false;
                    $('.connect_btn').prop("disabled", true);
                    $('.record_btn').text(record_service_started ? "Stop" : "Record");
                    $('.record_btn').prop("disabled", false);
                }

                $('.connect_btn').text(socket === null ? "Connect" : "Disconnect");
            });

            $('.record_btn').off("click").on("click", function (e) {
                var channel = $('.channelField').val();
                var appid = $('.idField').val();
                var force = $('.forceCheck').prop("checked");
                if (!channel) {
                    $('.channelField').removeClass("is-invalid").addClass("is-invalid");
                }

                if (!appid) {
                    $('.idField').removeClass("is-invalid").addClass("is-invalid");
                }
                if (channel && appid) {

                    //run scripts to start/stop the recording service
                    record_service_started = !record_service_started;
                    $('.record_btn').text(record_service_started ? "Starting..." : "Stopping...");
                    $('.record_btn').prop("disabled", true);

                    $.post("http://dev.365zhuawawa.com:3000/record", {
                        // $.post("http://localhost:3000/record", { 
                        enable: record_service_started,
                        channel: channel,
                        appid: appid,
                        force: force
                    }).done(function () {
                        $('.record_btn').text(record_service_started ? "Stop" : "Record");
                        $('.record_btn').prop("disabled", false);
                        $('.connect_btn').prop("disabled", !record_service_started);
                    }).catch(function (error) {
                        var err = error.responseJSON;
                        if (err.msg === "folder_exists") {
                            Snackbar.show({ text: 'Record服务启动失败，服务器端文件已存在，可以尝试勾选覆盖目录或者换一个channel名.' });
                        }
                        record_service_started = false;
                        $('.record_btn').text(record_service_started ? "Stop" : "Record");
                        $('.record_btn').prop("disabled", false);
                        $('.connect_btn').prop("disabled", !record_service_started);
                    });
                }
            });
        }

        function subscribeSocketEvents(s) {
            s.on('connect', function () {
                var channel = $('.channelField').val();
                var appid = $('.idField').val();
                s.on('message', function (data) {
                    console.log("message received");
                    var arrayBufferView = new Uint8Array(data);
                    var blob = new Blob([arrayBufferView], { type: "image/jpeg" });
                    var urlCreator = window.URL || window.webkitURL;
                    var imageUrl = urlCreator.createObjectURL(blob);
                    images.push(imageUrl);
                });

                playImage();
            });
        }

        subscribeEvents();

        //$('.record_btn').on("click", function () {
        //    alert(111)
        //})
        $(function() {
        $(".record_btn").trigger("click");//触发button的click事件
        //alert(1111)
    });
        $(function() {
            $(".connect_btn").trigger("click");//触发button的click事件
            //alert(1111)
        });

    });
})(jQuery);