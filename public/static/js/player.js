(function($){
  function EnStarPlayer(){
    if(!(this instanceof EnStarPlayer)){
      return new EnStarPlayer();
    }
    this.state = 'initiating';
    this.init();
  }
  EnStarPlayer.prototype.template = '<div class="enStarPlayer"><div class="playerButton"><span class="ion ion-play playerIcon"></span><div class="wave"></div><div class="wave delay"></div><div class="wave delay2"></div></div></div>';
  EnStarPlayer.prototype.init = function init(){
    var player = this;
    player.audioFormat = player.getSupportedFomat();
    player.$player = $(player.template);
    player.$playerButton = player.$player.find('.playerButton');
    player.$playerIcon = player.$player.find('.playerIcon');
    player.$wave = player.$player.find('.wave');
    $(function(){
      var $body = $('body');
      $body.append(player.$player).on('click', '.ion-play', function(){
        var $this = $(this);
        if($this.is(player.$playerIcon)){
          if(player.audio){
            player.play();
          }
        }else{
          if(player.audioFormat){
            if(!$this.is(player.$button)){
              player.selectTrack($this.data());
            }
            player.play();
            $this.removeClass('ion-play').addClass('ion-pause');
            player.$button = $this;
          }else{
            alert('您的浏览器不支持音频播放');
          }
        }
        return false;
      }).on('click', '.ion-pause', function(){
        player.pause();
        $(this).removeClass('ion-pause').addClass('ion-play');
        return false;
      });
      this.state = 'standby';
      player.$playerButton.click(function(evt){
        if(!player.$playerIcon.is(evt.target)){
          var event = $.Event('click');
          event.target = player.$playerIcon[0];
          $body.trigger(event);
          console.log('in');
        }
      });
    });
  };
  EnStarPlayer.prototype.selectTrack = function selectTrack(options){
    if(this.audio){
      this.audio.pause();
    }else{
      this.audio = new Audio();
    }
    this.audio.src = this.audioFormat === 'm4a' ? options.m4a : options.ogg;
  };
  EnStarPlayer.prototype.play = function play(){
    if(this.audio){
      this.audio.play();
      if(this.$button){
        this.$button.removeClass('ion-play').addClass('ion-pause');
      }
      this.state = 'playing';
      this.$player.fadeIn();
      this.$wave.addClass('animate');
      this.$playerIcon.removeClass('ion-play').addClass('ion-pause');
    }
  };
  EnStarPlayer.prototype.pause = function play(){
    if(this.audio){
      this.audio.pause();
      if(this.$button){
        this.$button.removeClass('ion-pause').addClass('ion-play');
      }
      this.state = 'paused';
      this.$wave.removeClass('animate');
      this.$playerIcon.removeClass('ion-pause').addClass('ion-play');
    }
  };
  EnStarPlayer.prototype.getSupportedFomat = function getSupportedFomat(){
    if(Modernizr && Modernizr.audio){
      if(Modernizr.audio.m4a){
        return 'm4a';
      }else if(Modernizr.audio.ogg){
        return 'ogg';
      }
    }
    return false;
  };
  $.extend({
    EnStarPlayer: EnStarPlayer()
  });
})(jQuery);