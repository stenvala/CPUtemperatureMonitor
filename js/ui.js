/*!
 * Class for ui+front of CPU Temperature Monitor
 * (c) Antti Stenvall
 * antti@stenvall.fi
 */

(function(self, $, undefined) {
  // private variable
  var that = {
    pollOn: false,
    width: 0,
    firstTime: 0,
    flotData: null,
    elem: {main: null, monitor: null},
    ws: null
  }
  // common function (shouldn't really be here)
  that.centralize = function(el) {
    var pad = 2 * parseInt($(el).css('padding')) + 2 * parseInt($(el).css('margin'));
    var h = $(el).height() + 2 * pad;
    var wh = $(window).height();
    $(el).css('top', (wh / 2 - h / 2) + 'px');
    var w = $(el).width() + 2 * pad;
    var ww = $(window).width();
    $(el).css('left', (ww / 2 - w / 2) + 'px');
    $(el).hide();
    $(el).css('visibility', 'visible');
    $(el).fadeIn(500);
  }
  // close socket
  that.closeSocket = function() {
    if (that.ws !== null) {
      that.ws.close();
      that.ws = null;
    }
  }
  // empty stack
  that.empty = function() {
    that.pollOn = false;
    that.flotData = null;
    $.each(that.elem, function(key, value) {
      if (value !== null) {
        $(value).fadeOut(1000, function() {
          $(this).remove();
        })
        that.elem[key] = null;
      }
    });
  }
  // initialize temperature monitor
  that.initMonitor = function() {
    that.flotData = null;
    var el = document.createElement('div');
    that.elem.monitor = el;
    $(el).addClass('box').width($(window).width() - 200).height($(window).height() - 200);
    $(el).css('visibility', 'hidden').appendTo('body');
  }
  // poll RESTful API to get new temperatures
  that.poll = function() {
    if (!that.pollOn) {
      return;
    }
    console.log('New poll');
    $.ajax({
      url: 'rest/temperature',
      contentType: 'application/json; charset=utf-8',
      type: 'get'
    }).done(that.monitorData);
    setTimeout(function() {
      that.poll();
    }, 1000);
  }
  // receives data and plots it
  that.monitorData = function(data) {
    if (that.flotData === null) {
      that.firstTime = new Date().getTime();
      var time = 0;
      that.flotData = [];
      for (var i = 0; i < data.length; i++) {
        that.flotData.push([time, data[i].temp]);
      }
    }
    else {
      var time = Math.round((new Date().getTime() - that.firstTime) / 100) / 10;
      for (i = 0; i < data.length; i++) {
        that.flotData[i].push([time, data[i].temp]);
        if (that.flotData[i].length > 100) {
          that.flotData[i].splice(0, 1);
        }
      }
    }
    var plotData = [];
    for (var i = 0; i < that.flotData.length; i++) {
      plotData.push({
        label: 'Core ' + (i + 1),
        data: that.flotData[i]
      });
    }
    $.plot($(that.elem.monitor), plotData,
            {series: {
                shadowSize: 0
              },
              legend: {position: 'nw'},
              xaxis: {axisLabel: 'Time [s]'},
              yaxis: {axisLabel: 'Temperature [deg Celsius]'},
              grid: {backgroundColor: 'white'}
            });
    $('.yaxisLabel').css('color', 'white').css('fontWeight', 'bold').css('paddingBottom', '15px');
    $('.xaxisLabel').css('color', 'white').css('fontWeight', 'bold');
    if (that.flotData[0].length === 3) {
      that.centralize(that.elem.monitor);
      $(that.elem.monitor).css('visibility', 'visible');
      $(that.elem.monitor).fadeIn(1000);
      console.log('Brought monitor visible');
    }
  }
  /*
   * Public interface to ui these are the views
   */
  var obj = function() {
    this.main();
    console.log('Initialized ui');
  }
  // Main view
  obj.prototype.main = function() {
    var _this = this;
    that.closeSocket();
    that.empty();
    var el = document.createElement('div');
    that.elem.main = el;
    $(el).addClass('box');
    $(el).html('<h1>CPU Temperature Monitor</h1> with ').appendTo('body');
    // poll link
    var poll = document.createElement('a');
    $(poll).attr('href', 'javascript:void(0)').html('AJAX polling via REST').appendTo(el);
    $(poll).bind('click', function() {
      _this.poll();
    });
    $(el).append(' or ');
    // websocket link
    var ws = document.createElement('a');
    $(ws).attr('href', 'javascript:void(0)').html('WebSocket').appendTo(el);
    $(ws).bind('click', function() {
      _this.socket();
    });
    $(el).append('.');
    that.centralize(el);
  }
  // start ajax poll
  obj.prototype.poll = function() {
    that.closeSocket();
    that.empty();
    that.initMonitor();
    that.pollOn = true;
    that.poll();
  }
  // open web socket
  obj.prototype.socket = function() {
    var _this = this;
    that.empty();
    that.initMonitor();
    that.ws = new WebSocket("ws://www.mathcodingclub.com:8081/sensors/socket/index.php");
    that.ws.onopen = function() {
      console.log('Opened connection');
      // send message
      // ws.send('Hey! I want to listen to data too');
    };
    that.ws.onmessage = function(evt) {
      var msg = evt.data;
      console.log('Received message');
      that.monitorData(JSON.parse(msg));
    };
    that.ws.onclose = function(evt) {
      console.log(evt);
      if (evt.code !== 1000) {
        _this.main();
        alert('WebSocket server is not on. Use AJAX polling instead.');
      }
      console.log('Connection closed');
    };
  }
  self.create = obj;
}(window.ui = window.ui || {}, jQuery));