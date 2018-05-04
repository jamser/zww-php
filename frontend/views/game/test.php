<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
<link href="http://vjs.zencdn.net/5.19/video-js.min.css" rel="stylesheet">
<script src="http://vjs.zencdn.net/5.19/video.min.js"></script>
</head>
<body>
<link href="http://vjs.zencdn.net/5.8.8/video-js.css" rel="stylesheet">
<video id="example_video_1" class="video-js vjs-default-skin" controls preload="auto" width="640" height="264" loop="loop" webkit-playsinline>
	<source src="rtmp://16787.liveplay.myqcloud.com/live/16787_device_006" type='rtmp/flv'>
</video>
<script src="http://vjs.zencdn.net/5.8.8/video.js"></script>
<script>
videojs.options.flash.swf = 'video.swf';
videojs('example_video_1').ready(function() {
  this.play();
});
</script>
</body>
</html>