@extends('app')

@section('content')


    <div class="talent_profile">

        <?php
        $visitingUserId = \Illuminate\Support\Facades\Session::get(\talenthub\Repositories\SiteSessions::USER_ID);
        $profileEditable = false;

        $video_thubnail_url = array();
        $video_embed_code = array();

        $videoCount = 0;
        foreach($videos as $key=>$video)
            {
                if($userVideo = $youtube->getVideoInfo($youtube->parseVIdFromURL($video->video_url)))
                {
                    $video_thubnail_url[$videoCount]="";
                    $video_embed_code[$videoCount] = "";

                    if(isset($userVideo->snippet->thumbnails->default->url))
                    {
                        $video_thubnail_url[$videoCount] = $userVideo->snippet->thumbnails->default->url;
                    }
                    else if(isset($userVideo->snippet->thumbnails->medium->url))
                    {
                        $video_thubnail_url[$videoCount] = $userVideo->snippet->thumbnails->medium->url;
                    }
                    else if(isset($userVideo->snippet->thumbnails->high->url))
                    {
                        $video_thubnail_url[$videoCount] = $userVideo->snippet->thumbnails->high->url;
                    }
                    if(isset($userVideo->player->embedHtml))
                    {
                        $video_embed_code[$videoCount] = $userVideo->player->embedHtml;
                    }
                }
                else
                {
                    $video_thubnail_url[$videoCount]=null;
                    $video_embed_code[$videoCount] = null;
                }
                $videoCount++;
            }

        ?>
        @include('profile.template.userIntroBanner',compact('userProfile','profileEditable'))

        <?php
        $active_menu=3; //For Videos
        ?>
        @include('templates.menu.user_profile_menu',compact('userProfile','active_menu'))


        <div class="profile_content_container">
            <div class="container curriculum_vitae_container">
                <h1 class="headings">Videos</h1>
                @if($userProfile->user_id == $visitingUserId)
                    <h4>Upload a new Video</h4>
                    {!! Form::open(['method'=>'post','url'=>'profile/videos']) !!}
                        <div class="row form_container">
                            <div class="col-xs-6 col-lg-4">
                                <div class="form-group">
                                    {!! Form::label('video_url','Link to youtube video:') !!}
                                    {!! Form::text('video_url',null,['class'=>'form-control','data-validate'=>'require',
                                    'data-toggle'=>'tooltip','data-placement'=>'bottom','title'=>'Enter a valid url']) !!}
                                </div>
                            </div>
                            <div class="col-xs-6 col-lg-4">
                                <div class="form-group">
                                    {!! Form::label('title','Video Title:') !!}
                                    {!! Form::text('title',null,['class'=>'form-control','data-validate'=>'require',
                                    'data-toggle'=>'tooltip','data-placement'=>'bottom','title'=>'Enter a title for the video.']) !!}
                                </div>
                            </div>
                            <div class="col-xs-6 col-lg-4">
                                <div class="form-group">
                                    {!! Form::label('descriptions','Description:') !!}
                                    {!! Form::textarea('descriptions',null,['class'=>'form-control','data-validate'=>'require',
                                    'data-toggle'=>'tooltip','data-placement'=>'bottom','title'=>'Enter a description for the video.']) !!}
                                </div>
                            </div>
                        </div>
                        <div>{!! Form::submit("Save Video",['class'=>'btn t-button']) !!}</div>
                    {!! Form::close() !!}
                @endif
                <div class="row video_container">

                    @for($count=0;$count<$videoCount; $count++)
                        @if($video_thubnail_url[$count]!=null)
                            <div class="col-xs-4 col-lg-3">
                                <div class="videos">
                                    <img src="{!! $video_thubnail_url[$count] !!}">
                                    <div class="play_button" data-toggle="modal" data-target="#video{{$count}}"><span class="glyphicon glyphicon-play-circle"></span></div>
                                </div>
                            </div>
                        @endif
                    @endfor
                </div>

            </div>

        </div>

    </div>


    <div class="video_modal_container">
        {{$count = 0}}
        @foreach($videos as $key=>$video)
            <?php
            if($video_embed_code[$count]!=null)
            {


            ?>
                <div class="modal fade" id="video{{$count}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">{{$video->title}}</h4>
                            </div>
                            <div class="modal-body" >
                                {!! $video_embed_code[$count] !!}
                                <p>{{$video->descriptions}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            $count++;
            ?>
        @endforeach
    </div>

@stop