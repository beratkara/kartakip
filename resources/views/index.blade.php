<html>
<head>
    <meta charset="utf-8">
    <title>Kar Takip - ANKARA</title>
    <meta name="description" content="Kar Takip - ANKARA">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.9/plyr.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <style>
        body{margin-top:20px;
            background:#eee;
        }
        .box {
            background: #fff;
            padding: 30px;
            margin: 0 0 24px 0;
        }
        .rte .boxHeadline {
            font-size: 18px;
            font-size: 1.8rem;
            font-weight: 400;
            margin: 0 0 25px 0;
        }
        .rte .boxHeadline+.boxHeadlineSub {
            margin: -18px 0 30px 0;
        }
        .rte .boxHeadlineSub {
            font-size: 14px;
            font-size: 1.4rem;
            font-weight: 400;
            font-style: italic;
            color: #919599;
            margin: 0 0 25px 0;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h2>Kar Takip</h2>
        </div>
    </div>
    <div class="row" id="videos">
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdn.plyr.io/3.6.9/plyr.js"></script>
<script src="https://cdn.jsdelivr.net/hls.js/latest/hls.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
<script>
    const players = Plyr.setup("video");
    window.players = players;

    function setVideos() {

        let videos = '';

        @foreach($vehicles as $vehicle)
            videos += '<div class="col-sm-2">'+
            '<div class="box rte">'+
            '<h2 class="boxHeadline">{{ $vehicle->plate }}</h2>'+
            '<video width="200" height="200" preload="none" id="player{{$loop->index}}" autoplay muted controls crossorigin></video>'+
            '</div>'+
            '</div>';

            @if($loop->last)
                $('#videos').html(videos);
            @endif
        @endforeach

        @foreach($vehicles->pluck('url') as $vehicle)
        var video = document.querySelector("#player{{$loop->index}}");
        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource("{{ $vehicle }}");
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                video.play();
            });
        }
        window.Plyr.setup(video);
        @endforeach

    }

    $(document).ready(function() {
        setVideos();
    });
</script>

</body>
</html>
