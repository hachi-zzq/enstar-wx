var _prototypeProperties = function (child, staticProps, instanceProps) { if (staticProps) Object.defineProperties(child, staticProps); if (instanceProps) Object.defineProperties(child.prototype, instanceProps); };

var _classCallCheck = function (instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } };

(function (win, doc, navigator, weChat, Vue) {
    "use strict";

    // define Gramophone class

    var Gramophone = (function () {

        /*
         * options: {
         *   complete: complete,  // complete callback
         *   src: '',
         *   duration: 60, // in secs
         * }
         */

        function Gramophone(options) {
            var _this = this;

            _classCallCheck(this, Gramophone);

            // create Audio instance
            this.audio = new Audio();
            this.audio.preload = "none";
            this.audio.src = options.src;
            this.duration = options.duration;
            this.repeating = false;
            // implement repeating as audio.loop has a poor support
            this.audio.addEventListener("ended", function () {
                if (_this.repeating) {
                    _this.currentTime = 0;
                    _this.play();
                } else {
                    _this.complete();
                }
            }, false);
        }

        _prototypeProperties(Gramophone, null, {
            play: {
                value: function play() {
                    this.audio.play();
                    return this;
                },
                writable: true,
                configurable: true
            },
            pause: {
                value: function pause() {
                    this.audio.pause();
                    return this;
                },
                writable: true,
                configurable: true
            },
            stop: {
                value: function stop() {
                    this.pause();
                    this.audio.currentTime = 0;
                    return this;
                },
                writable: true,
                configurable: true
            }
        });

        return Gramophone;
    })();

    // define Recorder class

    var Recorder = (function () {

        /*
         * options: {
         *   complete: complete,  // complete callback
         *   playbackComplete: playbackComplete,  // playbackComplete callback
         *   [weChat: weChat,]
         * }
         */

        function Recorder(options) {
            _classCallCheck(this, Recorder);

            this.player = options.player;
            this.lessonId = options.lessonId;
            this.weChat = options.weChat || weChat;
            this.complete = options.complete;
            this.init();
        }

        _prototypeProperties(Recorder, null, {
            init: {
                value: function init() {
                    var _this = this;

                    this.weChat.onVoiceRecordEnd({
                        complete: function (res) {
                            _this.lastTrack = res.localId;
                            _this.complete(res);
                        }
                    });
                    wx.onVoicePlayEnd({
                        success: function (res) {
                            return _this.playbackComplete();
                        } });
                },
                writable: true,
                configurable: true
            },
            record: {
                value: function record() {
                    this.player.vm.recordLength = 0;
                    this.weChat.startRecord();
                    this.startTime = new Date().getTime();
                    this.recordLength = 0;
                    this.updateTime();
                },
                writable: true,
                configurable: true
            },
            updateTime: {
                value: function updateTime() {
                    var recorder = this,
                        refreshInterval = 200;
                    recorder.refreshTimeout = setTimeout(function refresh() {
                        recorder.recordLength = (new Date().getTime() - recorder.startTime) / 1000;
                        recorder.player.vm.recordLength = Math.round(recorder.recordLength);
                        recorder.refreshTimeout = setTimeout(refresh, refreshInterval);
                    }, refreshInterval);
                },
                writable: true,
                configurable: true
            },
            stop: {
                value: function stop(callback) {
                    var _this = this;

                    clearTimeout(this.refreshTimeout);
                    this.weChat.stopRecord({
                        success: function (res) {
                            _this.lastTrack = res.localId(callback || _this.complete)(res);
                        }
                    });
                },
                writable: true,
                configurable: true
            },
            playback: {
                value: function playback() {
                    if (this.lastTrack) {
                        this.weChat.playVoice({
                            localId: this.lastTrack
                        });
                    }
                },
                writable: true,
                configurable: true
            },
            pausePlayback: {
                value: function pausePlayback(callback) {
                    if (this.lastTrack) {
                        this.weChat.pauseVoice({
                            localId: this.lastTrack
                        });
                        if (callback) {
                            callback();
                        }
                    }
                },
                writable: true,
                configurable: true
            },
            send: {
                value: function send(callback) {
                    var _this = this;

                    if (this.lastTrack) {
                        this.weChat.uploadVoice({
                            localId: this.lastTrack,
                            success: function (res) {
                                var recordData = new FormData(),
                                    req = new XMLHttpRequest();
                                recordData.append("lesson_guid", _this.lessonId);
                                recordData.append("media_id", _this.lastTrack);
                                req.onreadystatechange = function () {
                                    // https://xhr.spec.whatwg.org/#dom-xmlhttprequest-readystate
                                    if (req.readyState == 4) {
                                        // `DONE`
                                        _this.callback(JSON.parse(req.responseText));
                                        if (req.status == 200) {
                                            win.alert("录音上传成功！");
                                        } else {
                                            win.alert("上传失败，请稍后再试");
                                        }
                                    }
                                };
                                req.open("POST", "/reading/save");
                                req.send(recordData);
                            }
                        });
                    }
                },
                writable: true,
                configurable: true
            }
        });

        return Recorder;
    })();

    // define Player class

    var Player = (function () {
        // TODO add necessary cleanups between state changing

        function Player(options) {
            var _this = this;

            _classCallCheck(this, Player);

            this.gramophone = new Gramophone(extend({}, doc.querySelector("[data-play]").dataset, {
                complete: function () {
                    return _this.status = "stadingBy";
                } }));
            this.recorder = new Recorder({
                player: this,
                lessonId: doc.querySelector("#player").dataset.lessonid,
                complete: function (res) {
                    return _this.status = "recorded";
                }, // use arrow function to bind player as scope
                playbackComplete: function () {
                    _this.trackPlaying = false;
                },
                weChat: weChat });
            this.initControls();
        }

        _prototypeProperties(Player, null, {
            initControls: {
                value: function initControls() {
                    var player = this,
                        recordSecs = 0;

                    this.vm = new Vue({
                        el: "#player",
                        data: {
                            status: "standingBy",
                            repeating: false,
                            trackPlaying: false,
                            recordTime: "00:00:00",
                            sendingRecord: false },
                        computed: {
                            recordLength: {
                                get: function get() {
                                    return recordSecs;
                                },
                                set: function set(value) {
                                    if (typeof value === "number") {
                                        player.vm.recordTime = [player.lowerDigits(parseInt(value / 3600), 2), player.lowerDigits(parseInt(value % 3600 / 60), 2), player.lowerDigits(Math.round(value % 60), 2)].join(":");
                                    } else {
                                        player.vm.recordTime = value;
                                    }
                                    return recordSecs = value;
                                } } },
                        methods: {
                            playClick: function playClick() {
                                switch (this.status) {
                                    case "standingBy":
                                    case "paused":
                                        player.gramophone.play();
                                        this.status = "playing";
                                        break;
                                    case "playing":
                                        player.gramophone.pause();
                                        this.status = "paused";
                                }
                            },

                            stopClick: function stopClick() {
                                if (this.status !== "initiating") {
                                    player.gramophone.stop();
                                    this.status = "standingBy";
                                }
                            },

                            repeatClick: function repeatClick() {
                                player.gramophone.repeating = this.repeating = !this.repeating;
                            },

                            recordClick: function recordClick() {
                                var _this = this;
                                switch (this.status) {
                                    case "standingBy":
                                        player.recorder.record();
                                        this.status = "recording";
                                        break;
                                    case "recording":
                                        player.recorder.stop();
                                        this.status = "recorded";
                                        break;
                                    case "recorded":
                                        if (!this.sendingRecord) {
                                            this.sendingRecord = true;
                                            player.recorder.send(function (res) {
                                                _this.sendingRecord = false;
                                                _this.status = "standingBy";
                                            });
                                        }
                                }
                            },

                            cancelClick: function cancelClick() {
                                var _this = this;

                                if (!this.sendingRecord) {
                                    switch (this.status) {
                                        case "recording":
                                            this.recorder.stop(function (res) {
                                                _this.recorder.lastTrack = null;
                                                _this.status = "standingBy";
                                            });
                                            break;
                                        case "recorded":
                                            if (win.confirm("确定要放弃之前的录音？")) {
                                                this.recorder.lastTrack = null;
                                                this.status = "standingBy";
                                            }
                                    }
                                    this.status = "standingBy";
                                }
                            },

                            redoClick: function redoClick() {
                                if (!this.sendingRecord) {
                                    this.recorder.lastTrack = null;
                                    this.recorder.record();
                                    this.status = "recording";
                                }
                            },

                            trackPlayClick: function trackPlayClick() {
                                if (!this.sendingRecord) {
                                    if (this.trackPlaying) {
                                        this.recorder.pausePlayback();
                                    } else {
                                        this.recorder.playback();
                                    }
                                    this.trackPlaying = !this.trackPlaying;
                                }
                            } }
                    })
                        // // TODO load user settings
                        // this.vue.repeating = true
                    ;
                },
                writable: true,
                configurable: true
            },
            lowerDigits: {
                value: function lowerDigits(integer, n) {
                    var str = '';
                    for(var count = 0; count < n; count++){
                        str += '0'
                    }
                    return (integer < 0 ? "-" : "") + (str + (Math.abs(integer) || 0)).slice(-n);
                },
                writable: true,
                configurable: true
            }
        });

        return Player;
    })();

    // helpers
    function extend(target) {
        for (var _len = arguments.length, sources = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
            sources[_key - 1] = arguments[_key];
        }

        if (target) {
            for (var index in sources) {
                for (var key in sources[index]) {
                    if (sources[index].hasOwnProperty(key)) {
                        target[key] = sources[index][key];
                    }
                }
            }
        }
        return target;
    }

    // set up weChat APIs and instantiate player
    if (win.weChatConfig) {
        (function () {
            var player = new Player();
            win.weChatConfig({
                // debug: true,
                jsApiList: ["hideMenuItems", "showMenuItems", "startRecord", "stopRecord", "onVoiceRecordEnd", "playVoice", "uploadVoice"]
            });
            weChat.ready(function () {
                // set menu list
                weChat.hideMenuItems({
                    menuList: ["menuItem:share:qq", "menuItem:share:weiboApp", "menuItem:share:facebook", "menuItem:share:QZone", "menuItem:copyUrl", "menuItem:originPage", "menuItem:readMode", "menuItem:openWithQQBrowser", "menuItem:openWithSafari", "menuItem:share:email", "menuItem:share:brand"]
                });
                // init player
                player.status = "standingBy";
            });
        })();
    }
})(window, document, navigator, wx, Vue);
// use arrow function to bind player as scope