<script src="./layout/js/bootstrap.bundle.min.js"></script>
<script src="./layout/js/all.min.js"></script>

</body>
<script>
  var notify_num = $('.notify-num').html();
  var notify_msg = 0;
  let usersType = [];
  let = msg_from_rooms = [];

  <?php if (isset($_SESSION['token'])) {?>
    var conn = new WebSocket("ws://localhost:8080?token=<?=$_SESSION['token']?>");
  <?php }?>

  $(function() {
    setTimeout(function() {
      $('.error').remove();
    }, 2000);
  });

  $('.members').click(function(e) {
    var roomid = $(this).attr("data-roomid");

    $.ajax({
      type: 'post',
      url: 'members.php',
      data: {
        roomid: roomid,
        members: "members"

      },
      success: function(data) {
        let privilege = '';
        data = JSON.parse(data);
        console.log(data);

        if (data.result.length > 0) {
          $('#members-modal').find('.modal-body').html('');
          console.log($('#members-modal').find('.members-header'));

          for (var i = 0; i < data.result.length; i++) {
            if (data.rank == "2") {
              privilege = `<div class="col-4 p-0">
            <div class="d-flex"style="justify-content: space-evenly;" >
           
            ${data.result[i][6] == 0 ?
              "<input type='submit' style='cursor: pointer;' name='Bann' value='Bann' class='btn btn-outline-warning' onclick='bann(event,this)'>":
              "<input type='submit' style='cursor: pointer;  background-color:#ffc107;color:#000' name='Bann' value='Banned' class='btn btn-outline-warning' onclick='bann(event,this)'>"}
              <input type='submit' style='cursor: pointer;' name='Remove' value='Remove' class="btn btn-outline-danger"onclick="remove(event,this)">
            </div>
            </div>`
            }
            $('#members-modal').find('.modal-body').append(`

          <div class="setting my-4">
            <input type='hidden' id="setting-userid" name='userid' value='${data.result[i][0]}'>
            <input type='hidden' id="setting-roomid" name='roomid' value='${data.result[i][1]}'>
            <input type='hidden' id="setting-rank" name='rank' value='${data.result[i][2]}'>
            <div class="row align-items-center">
            <div class='col-8 p-0 d-flex align-items-center'>
            <img class="img-setting img-fluid" src= ${data.result[i][4] !== null ? 'imgs/'+ data.result[i][4]  : 'imgs/avatar.png'} />
            <span class='username'> ${data.result[i][3]} </span>
            <span style='font-size:3.8rem;line-height:0; color:${data.result[i][5] == 1 ? 'green' : 'red' }'>•</span>
            </div>

              ${typeof privilege != 'undefined' ? privilege :''}
            </div>
            </div>
            `)
          }
        }

      },
      error: function(data) {

      }
    });
  })



  function notifyLabel() {
    $('.notify-num').html('');
    $('.notify-num').attr("style", "display: none !important");

    $.ajax({
      type: 'post',
      url: 'notifications.php',
      data: {
        notifyForm: "notifyForm",
        myid: <?=isset( $_SESSION['id']) ? $_SESSION['id'] :'null'?>
      },
      success: function(data) {
        data = JSON.parse(data);
        console.log(data);
        if (data.result.length > 0) {
          const col = `<div class='col-lg-5 text-end'>
                 <input type='submit' id='accept_join' name='accept-join' value='accept' class='accept-join btn btn-success' onclick='accept_join()'>
                 <input type='submit' id='decline_join' name='decline-join' value='decline'class='decline-join btn btn-danger' onclick='decline_join()'>
               </div>`;

          $('.notifications').html('')
          for (var i = 0; i < data.result.length; i++) {
            $('.notifications').prepend(`<li>
             <input type='hidden' id='admin-to-join' name='admin' value='${data.result[i][1]}'>
             <input type='hidden' id='user-to-join' name='user' value='${data.result[i][0]}'>
             <input type='hidden' id='room-to-join' name='room' value='${ data.result[i][3] }'>
             <input type='hidden' id='room-to-join-name' name='name' value='${data.result[i][4]}'>
             <div class='row'>
               <div class= ${data.result[i][2].includes("wants to join") ?'col-lg-7':'col-10'}>
                 <p>${data.result[i][2]}</p>
               </div>



                ${data.result[i][2].includes("wants to join") ? col :
                data.result[i][2].includes("accepted") ? '<div style="color:green;font-size:2rem" class="col-2"><i class="fa-solid fa-circle-check"></i></div>'  :
                data.result[i][2].includes("declined") ? '<div style="color:red;font-size:2rem" class="col-2"><i class="fa-solid fa-circle-xmark"></i></div>'  :
                data.result[i][2].includes("you have been banned from ") ? '<div style="color:#ff7a00;font-size:2rem" class="col-2"><i class="fa-solid fa-triangle-exclamation"></i></div>'  :
                data.result[i][2].includes("bann removed from ") ? '<div style="color:purple;font-size:2rem" class="col-2"><i class="fa-regular fa-comment-dots"></i></div>'  :
                data.result[i][2].includes("you removed from ") ? '<div style="color:#ff0000;font-size:2rem" class="col-2"><i class="fa-solid fa-user-xmark"></i></div>'  :''}
                </div>

             <div class='row'>
               <div class='col-12'>${data.result[i][5]}</div>
             </div>
          </li>`)
          }


        } else {
          $('.notifications').html('');
          $('.notifications').prepend(`<li>There is no notifications</i> `);
        }
      },
      error: function(data) {

      }
    });

  }

  function notifymsg() {
    msg_from_rooms = [];
    $('.notify-msg').html('');
    $('.notify-msg').attr("style", "display: none !important");
    notify_msg = 0;


    $.ajax({
      type: 'post',
      url: 'notifications.php',
      data: {
        notifyMessage: "notifyMessage",
      },
      success: function(data) {
        data = JSON.parse(data);
        console.log(data);
        if (data.result.length > 0) {


          $('.messages').html('')
          for (var i = 0; i < data.result.length; i++) {
            $('.messages').prepend(`<li>
            <a href="myrooms.php?id=${data.userid}&room_id=${data.result[i][0]}">
             <div style="color:green;">New Message from ${data.result[i][1]}</div>
             <div style="color:#000"> ${data.result[i][2]}</div>
            </a>
          </li>`)
          }


        } else {
          $('.messages').html('');
          $('.messages').prepend(`<li style="color:#000">There is no messages</i> `);
        }
      },
      error: function(data) {

      }
    });

  }


  $(".file label").click(function() {
    $("#inputTag").trigger('click');

  });
  $(".fileChat .attach").click(function() {
    $("#attach").trigger('click');

  });
  $(".submit .submitlabel").click(function() {
    $("#submit").trigger('click');

  });

  $(".open-profile").click(function() {
    $('.profile').toggleClass("open");
  });


  $(".profile-title").click(function() {
    $('.profile').removeClass("open");
  });

  $(".open-close").click(function() {

    $('.sidebar').toggleClass("open-sidebar");
  });





  function accept_join() {
    $.ajax({
      type: 'post',
      url: 'accept-decline-join.php',
      data: {
        admin_to_join: $('#admin-to-join').val(),
        user_to_join: $('#user-to-join').val(),
        room_to_join: $('#room-to-join').val(),
        room_to_join_name: $('#room-to-join-name').val(),
        want_to_join: $('#accept_join').val()
      },
      success: function(data) {

        console.log(data);
        conn.send(data);
        notifyLabel();

      },
      error: function(data) {

      }
    });

  }

  function decline_join() {
    $.ajax({
      type: 'post',
      url: 'accept-decline-join.php',
      data: {
        admin_to_join: $('#admin-to-join').val(),
        user_to_join: $('#user-to-join').val(),
        room_to_join: $('#room-to-join').val(),
        room_to_join_name: $('#room-to-join-name').val(),
        want_to_join: "decline"
      },
      success: function(data) {
        data_not = JSON.parse(data)
        console.log(data);
        console.log(data_not);
        conn.send(data);
        notifyLabel();

      },
      error: function(data) {

      }
    });

  }

  $(function() {


    <?php if (isset($_SESSION['token'])) {?>


      conn.onopen = function(e) {
        console.log("Connection established!");
      };


      $('.join_room').on('submit', function(e) {
        e.preventDefault();

        var user_id = $(this).find('#user_id_join').val();
        var room_id = $(this).find('#room_id_join').val();
        var room_name = $(this).find('#room_name_join').val();
        var admin_id = $(this).find('#admin_id_join').val();

        $.ajax({
          type: 'post',
          url: 'join_cancel.php',
          data: {
            userid: user_id,
            roomid: room_id,
            roomname: room_name,
            adminid: admin_id,
            join: "join"
          },
          success: function(data) {

            //  console.log(data);
            conn.send(data);
            location.reload();
          },
          error: function(data) {

          }
        });
      })




      $('.cancel_room').on('submit', function(e) {

        e.preventDefault();
        var user_id = $(this).find('#user_id_cancel').val();
        var room_id = $(this).find('#room_id_cancel').val();
        var admin_id = $(this).find('#admin_id_cancel').val();

        $.ajax({
          type: 'post',
          url: 'join_cancel.php',
          data: {
            userid: user_id,
            roomid: room_id,
            adminid: admin_id,
            cancel: "cancel"

          },
          success: function(data) {
            conn.send(data);
            location.reload();
          },
          error: function(data) {

          }
        });
      })


      conn.onmessage = function(e) {


        if (JSON.parse(e.data) == "cancel") {

          notify_num -= 1;

          if (notify_num <= 0) {
            notify_num = 0;
            $('.notify-num').css('display', 'none');
            $('.notify-num').html(notify_num);
            return false;
          } else {
            $('.notify-num').html(notify_num);
            $('.notify-num').css('display', 'block');
          }


        } else if (JSON.parse(e.data) == "join") {
          console.log(e.data);

          if ($('.notify-num').html() == '' || $('.notify-num').html() == 0) {
            notify_num = 0;
          }
          notify_num += 1;

          $('.notify-num').html(notify_num);
          $('.notify-num').css('display', 'block');




        } else if (JSON.parse(e.data)["accept_to_user"] == "accept_to_user") {

          data = JSON.parse(e.data);

          console.log(data);

          if ($('.notify-num').html() == '' || $('.notify-num').html() == 0) {
            notify_num = 0;
          }
          notify_num++;

          $('.notify-num').html(parseInt(notify_num));
          $('.notify-num').css('display', 'block');

          $.ajax({
            type: 'post',
            url: 'join_cancel.php',
            data: {
              userid: data.data.userid,
              roomid: data.data.roomid,
              adminid: data.data.adminid,
              adminname: data.data.adminname,
              roomname: data.data.roomname,
              accept_to_user: "accept_to_user"
            },
            success: function(data) {
              console.log(data);
            }
          });

        } else if (JSON.parse(e.data)["decline_to_user"] == "decline_to_user") {

          data = JSON.parse(e.data);

          console.log(data);
          if ($('.notify-num').html() == '' || $('.notify-num').html() == 0) {
            notify_num = 0;
          }
          notify_num++;

          $('.notify-num').html(parseInt(notify_num));
          $('.notify-num').css('display', 'block');

          $.ajax({
            type: 'post',
            url: 'join_cancel.php',
            data: {
              userid: data.data.userid,
              roomid: data.data.roomid,
              adminid: data.data.adminid,
              adminname: data.data.adminname,
              roomname: data.data.roomname,
              decline_to_user: "decline_to_user"
            },
            success: function(data) {
              console.log(data);
            }
          });
        } else if (JSON.parse(e.data)["removed"] == "removed") {

          data = JSON.parse(e.data);

          console.log(data);
          if ($('.notify-num').html() == '' || $('.notify-num').html() == 0) {
            notify_num = 0;
          }
          notify_num++;

          $('.notify-num').html(parseInt(notify_num));
          $('.notify-num').css('display', 'block');

          $(".text-box").children().remove();
          } else if (JSON.parse(e.data)["bann"] == "bann") {

        data = JSON.parse(e.data);

        console.log(data);
        if ($('.notify-num').html() == '' || $('.notify-num').html() == 0) {
          notify_num = 0;
        }
        notify_num++;

        $('.notify-num').html(parseInt(notify_num));
        $('.notify-num').css('display', 'block');
        if(data.roomid == <?=isset($_GET["room_id"]) ? $_GET["room_id"] :'null' ?>){
        $(".text-box").children().remove();
        }
      }else if (JSON.parse(e.data)["banned"] == "banned") {

        data = JSON.parse(e.data);

        console.log(data);
        if ($('.notify-num').html() == '' || $('.notify-num').html() == 0) {
          notify_num = 0;
        }
        notify_num++;

        $('.notify-num').html(parseInt(notify_num));
        $('.notify-num').css('display', 'block');
        if(data.roomid == <?=isset($_GET["room_id"]) ? $_GET["room_id"] :'null' ?>){
        $(".text-box").append(`       <div class="fileChat">
          <label for="attach"><i class="attach fa-solid fa-paperclip"></i></label>
          <input form="submit_file_form" id="attach" type="file" name="attach" onchange="readURL(this);" />
        </div>
        <div class="d-flex" style="flex:1; align-items: center;">
          <form id="chat_form" action="" method="post">
            <input id="userid" type="hidden" name="userid" value="<?=isset($_GET['id']) ? $_GET['id'] : 'null'?> " />
            <input id="roomid" type="hidden" name="roomid" value="<?=isset($_GET['room_id']) ? $_GET['room_id'] : 'null' ?> " />


            <textarea id="message" placeholder="Type Message" onkeydown="pressed(event)"></textarea>

          </form>
          <div class="send">
            <label for="submit"><i class="submitlabel fa-regular fa-paper-plane"></i></label>
            <input form="chat_form" id="submit" type="submit" name="submit" />
          </div>

        </div>`);
        }
        } else if (JSON.parse(e.data)["focusSeen"] == "focusSeen") {


          data = JSON.parse(e.data);

          console.log(data);
          if(data.roomid == <?=isset($_GET["room_id"]) ? $_GET["room_id"] : 'null'?>){

            $('.seen').css('color','blue');
          }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        else if (JSON.parse(e.data)["save_message"]) {
          data = JSON.parse(e.data);
          console.log(data.save_message.roomid);
          console.log(data);
          for (var i = 0; i < data.room_count.length; i++) {
            $(".room-count").each(function(index) {
              if ($(this).hasClass(data.room_name) && data.room_count[i][1] == <?=isset($_GET["id"]) ? $_GET["id"] : 'null'?>) {
                if (data.room_count[i][0] == 0) {
                  $(this).css('display', 'none')
                } else {
                  $(this).css('display', 'block')
                  $(this).html(data.room_count[i][0]);
                }
              }
            });
          }

          if (!msg_from_rooms.includes(data.room_name)) {
            msg_from_rooms.push(data.room_name);
            notify_msg++;
            $('.notify-msg').html(parseInt(notify_msg));
            $('.notify-msg').css('display', 'block');
            if (data.save_message.userid != <?=$_SESSION['id']?>) {

              $('.title').prepend("(" + parseInt(notify_msg) + ")" + " ");
            }
          }



          var sender = "<?=$_SESSION["username"]?>" == data.save_message.username ? "you" : data.save_message.username;
          // console.log(sender);
          var from = <?=$_SESSION["id"]?>;
          if (data.save_message.roomid == <?=isset($_GET["room_id"]) ? $_GET["room_id"] : 'null'?>) {
            if (from == data.save_message.userid) {
              if (data.save_message.file_type) {
                if (data.save_message.file_type.split("/")[0] == "image") {
                  $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #be06f3;margin-left:auto; border-radius:15px;">
                    <span style="color: #eee;">${sender} </span><br>
                    <img src="imgs/${data.save_message.message}" style='cursor: pointer' onclick='open_photo(this)' />
                    <br>
                    <span style="color: #bbb;"> <?=date("Y-m-d h:i A")?> </span>
                    <span class="seen" style="color:${typeof data.save_message.seen !='undefined' ?  'blue' : '#bbb'}"> <i class="fa-solid fa-check-double"></i> </span>
                    </div>
                    </div>`)
                } else {
                  $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #be06f3;margin-left:auto; border-radius:15px;">
                    <span style="color: #eee;">${sender} </span><br>
                    <a href="imgs/${data.save_message.message}">${data.save_message.message} </a><br>
                      <span style="color: #bbb;"> <?=date("Y-m-d h:i A")?> </span>
                      <span class="seen" style="color:${typeof data.save_message.seen !='undefined' ?  'blue' : '#bbb'}"> <i class="fa-solid fa-check-double"></i> </span>
                      </div>
                    </div>`)
                }
              } else {
                $(".chat-area").append(`
                  <div class="row">
                  <div class="message" style="background: #be06f3;margin-left:auto; border-radius:15px;">
                  <span style="color: #eee;">${sender} </span><br>
                  ${data.save_message.message}<br>
                  <span style="color: #bbb;"> <?=date("Y-m-d h:i A")?> </span>
                  <span class="seen" style="color:${typeof data.save_message.seen !='undefined' ?  'blue' : '#bbb'}"> <i class="fa-solid fa-check-double"></i> </span>
                  </div>
                  </div>`)
              }
            } else {
              if (data.save_message.file_type) {
                if (data.save_message.file_type.split("/")[0] == "image") {
                  $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #888;margin-right:auto; border-radius:15px;">
                    <span style="color: #eee;">${sender} </span><br>
                    <img src="imgs/${data.save_message.message}" style='cursor: pointer' onclick='open_photo(this)' />
                    <br>
                      <span style="color: #bbb;"> <?=date("Y-m-d h:i A")?> </span>
                      </div>
                    </div>`)
                } else {
                  $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #888;margin-right:auto; border-radius:15px;">
                    <span style="color: #eee;">${sender} </span><br>
                    <a href="imgs/${data.save_message.message}">${data.save_message.message} </a><br>
                      <span style="color: #bbb;"> <?=date("Y-m-d h:i A")?> </span>
                      </div>
                    </div>`)
                }
              } else {

                $(".chat-area").append(`
          <div class="row">
            <div class="message" style="background: #888;margin-right:auto; border-radius:15px;">
            <span style="color: #eee;">${sender} </span><br>
            ${data.save_message.message}<br>
              <span style="color: #bbb;"> <?=date("Y-m-d h:i A")?> </span>
            </div>
          </div>`)
              }
            }
          }
          $(".chat-area").animate({
            scrollTop: $(".chat-area > .row").height() * $(".chat-area > .row").length
          }, 100);
        } else if (JSON.parse(e.data)["is_typing"]) {


          data = JSON.parse(e.data);
          console.log(data);
          if (data.roomval_type == <?=isset($_GET['room_id']) ? $_GET['room_id'] : "null"?>) {
            if (data.userval == '') {
              $(".usertyping").html('');
              $(".headTyping").css('display', 'none');
              usersType = $.grep(usersType, function(value) {
                return value != data.is_typing;
              });
            } else {
              if (!usersType.includes(data.is_typing)) {
                usersType.push(data.is_typing)
              }
              if (usersType.length == 1) {
                $(".usertyping").html(data.is_typing + " is");
                $(".headTyping").css('display', 'flex');
              } else {
                $(".usertyping").html(usersType.join(', ') + ' are');
                // $(".usertyping").html('');
                // usersType.forEach(i => {
                //   $(".usertyping").append(i+", ");
                // });
                // $(".usertyping").append("are");
              }

            }

          }



          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }

      };







  $('#chat_form').on('submit', function(e) {

    e.preventDefault();
    var message = $("#message").data("emojioneArea").getText().trim();

    if (message !== '') {
      var userid = $("#userid").val();
      var roomid = $("#roomid").val();
      $("#message").data("emojioneArea").setText("");
      $.ajax({
        type: 'post',
        url: 'save_messages.php',
        data: {
          message: message,
          userid: userid,
          roomid: roomid,
          save_message: "save_message"
        },
        success: function(data) {
          // edata = JSON.parse(data);
          // console.log(data);
          conn.send(data);
        },
        error: function(data) {

        }
      });
    }
  })

  $('#submit_file_form').on('submit', function(e) {

    e.preventDefault();
    $('#image_preview').modal('hide');
    $.ajax({
      type: 'POST',
      url: 'save_messages.php',
      data: new FormData(this),
      contentType: false,
      cash: false,
      processData: false,
      success: function(data) {
        // data = JSON.parse(data);
        // console.log(data);
        conn.send(data);

      },
      error: function(data) {

      }
    });

  })




  $(document).ready(function() {

    $('.members').each(function( index ) {
      // console.log( $('.members').data("roomid"));
      if( $(this).data("roomid") == <?=isset($_GET['room_id']) ? $_GET['room_id'] : 'null'?>){

        $(this).parent().parent().css('background','grey');
      }
});

    $('.title').html('<?=$pageTitle?>');

    if (<?=isset($_GET["id"]) ? 1 : 0?>) {
      $.ajax({
        type: 'post',
        url: 'save_messages.php',
        data: {
          userid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
          roomid: <?=isset($_GET["room_id"]) ? $_GET["room_id"] : "null"?>,
          get_message: "get_message"
        },
        success: function(edata) {
          data = JSON.parse(edata);
          // console.log(edata);
          $('.loader-container').css('display','none');

          var from = <?=$_SESSION["id"]?>;
          $(".chat-area").html('');
          for ($i = 0; $i < data[0].length; $i++) {
            if (from == data[0][$i][0]) {
              if (data[0][$i][6] == 2) {
                $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #be06f3;margin-left:auto; border-radius:15px;">
                    <span style="color: #eee;">you</span> <br>
                    <img src="imgs/${data[0][$i][2]}" style='cursor: pointer' onclick='open_photo(this)' />
                    <br>
                    <span style="color: #bbb;">${data[0][$i][3]}</span>
                    <span style="color:${data[0][$i][7] == 1 ? 'blue' : '#bbb' } ;"> <i class="fa-solid fa-check-double"></i> </span>
                    </div>
                    </div>`)
              } else if (data[0][$i][4] == 1) {
                $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #be06f3;margin-left:auto; border-radius:15px;">
                    <span style="color: #eee;">you</span> <br>
                    <a href="imgs/${data[0][$i][2]}">${data[0][$i][2]} </a><br>
                    <span style="color: #bbb;">${data[0][$i][3]}</span>
                    <span style="color:${data[0][$i][7] == 1 ? 'blue' : '#bbb' } ;"> <i class="fa-solid fa-check-double"></i> </span>
                    </div>
                    </div>`)
              } else {
                $(".chat-area").append(`
                  <div class="row">
                    <div class="message" style="background: #be06f3;margin-left:auto; border-radius:15px;">
                    <span style="color: #eee;">you</span> <br>
                    ${data[0][$i][2]} <br>
                      <span style="color: #bbb;">${data[0][$i][3]}</span>
                      <span style="color:${data[0][$i][7] == 1 ? 'blue' : '#bbb' } ;"> <i class="fa-solid fa-check-double"></i> </span>
                    </div>
                  </div>`)
              }
            } else {
              if (data[0][$i][6] == 2) {
                $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #888;margin-right:auto; border-radius:15px;">
                    <span style="color: #eee;">${data[0][$i][5]}</span> <br>
                    <img src="imgs/${data[0][$i][2]}" style='cursor: pointer' onclick='open_photo(this)' />
                    <br>
                    <span style="color: #bbb;">${data[0][$i][3]}</span>
                    </div>
                    </div>`)
              } else if (data[0][$i][4] == 1) {
                $(".chat-area").append(`
                    <div class="row">
                    <div class="message" style="background: #888;margin-right:auto; border-radius:15px;">
                    <span style="color: #eee;">${data[0][$i][5]}</span> <br>
                    <a href="imgs/${data[0][$i][2]}">${data[0][$i][2]} </a><br>
                    <span style="color: #bbb;">${data[0][$i][3]}</span>
                    </div>
                    </div>`)
              } else {
                $(".chat-area").append(`
                  <div class="row">
                    <div class="message" style="background: #888;margin-right:auto; border-radius:15px;">
                    <span style="color: #eee;">${data[0][$i][5]}</span> <br>
                    ${data[0][$i][2]} <br>
                      <span style="color: #bbb;">${data[0][$i][3]}</span>
                    </div>
                  </div>`)
              }

            }
          }

          $(".chat-area").animate({
            scrollTop: $(".chat-area > .row").height() * $(".chat-area > .row").length
          }, 100);
           conn.send(edata);
        },
        error: function(data) {

        }
      });
    }


  })

  <?php }?>
  })


  $(document).ready(function() {
    // $(".chat-area").css({'background':'url(imgs/loader.gif)','background-position':'center'});
    if (<?=isset($_GET["id"]) ? 1 : 0?>) {
      $.ajax({
        type: 'post',
        url: 'notifications.php',
        data: {
          // userid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
          // roomid: <?=isset($_GET["room_id"]) ? $_GET["room_id"] : "null"?>,
          room_count: "room_count",
        },
        success: function(data) {
          $(".chat-area").css('background','#ddd');
          data = JSON.parse(data);
          console.log(data);
          for (var i = 0; i < data.result.length; i++) {
            $(".room-count").each(function(index) {
              if ($(this).hasClass(data.result[i][1])) {

                if (data.result[i][0] != 0) {
                  $(this).css('display', 'block')
                  $(this).html(data.result[i][0]);
                }

              }
            });
          }
        },
        error: function(data) {

        }
      });
    }

  })



  function arabicDetect(val) {

    var la_ar = ["ي", "لا", "ى", "ي", "و", "ه", "ش", "س", "ق", "ف", "غ", "ع", "ض", "ص", "ن", "م", "ل", "ك", "ظ", "ط", "ز", "ر", "ذ", "د", "خ", "ح", "ج", "ث", "ت", "ب", "ا"];
    var bla = $(val).text();
    var blaـl = bla.charAt(0);

    if ($.inArray(blaـl, la_ar) > -1) {
      $(".emojionearea-editor").attr('dir', 'rtl');
      $(".emojionearea-editor").css('padding-right', '25px');
    } else {
      $(".emojionearea-editor").attr('dir', 'ltr');
    }
  }

  function enterkey(val, event) {
    if (event.key === "Enter") {
      event.preventDefault();
      $("#chat_form").submit();
      $("#message").data("emojioneArea").setText("");
    }

  }

  function scrolldown(val, event) {
    // console.log($(".chat-area > .row"));
    $(".chat-area").animate({
      scrollTop: $(".chat-area > .row").height() * $(".chat-area > .row").length
    }, 100);
  }
  // let timeoutCount = 0;
  function isTyping(val) {
    // console.log( $(val).text());
    let userroom_type = {
      "userid_type": <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
      "roomid_type": <?=isset($_GET["room_id"]) ? $_GET["room_id"] : "null"?>,
      "username_type": '<?=$_SESSION['username']?>',
      "userval_type": $(val).text()
    };
    userroom_type = JSON.stringify(userroom_type);
    conn.send(userroom_type);


  }

  function focusReaded() {
    $('.title').html('<?=$pageTitle?>');
    $.ajax({
      type: 'POST',
      url: 'save_messages.php',
      data: {
        userid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
        roomid: <?=isset($_GET["room_id"]) ? $_GET["room_id"] : "null"?>,
        focusRead: "focusRead"
      },
      success: function(data) {
        data = JSON.parse(data)
        // console.log(data);
        if (data.roomid == <?=isset($_GET["room_id"]) ? $_GET["room_id"] : 'null'?>) {


          $(".room-count").each(function(index) {
            if ($(this).hasClass(data.room_name)) {

              $(this).html('');
            }
          });

        }
      },
      error: function(data) {

      }
    });

  }

  function focusSeen() {
    $.ajax({
      type: 'POST',
      url: 'save_messages.php',
      data: {
        userid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
        roomid: <?=isset($_GET["room_id"]) ? $_GET["room_id"] : "null"?>,
        focusSeen: "focusSeen"
      },
      success: function(data) {
        // data = JSON.parse(data);
        console.log(data);
        conn.send(data);
      },
      error: function(data) {

      }
    });

  }

  $('.leave-btn').on('click',function(){
    $.ajax({
      type: 'POST',
      url: 'save_messages.php',
      data: {
        userid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
        roomid: <?=isset($_GET["room_id"]) ? $_GET["room_id"] : "null"?>,
        leave: "leave"
      },
      success: function(data) {
        // data = JSON.parse(data);
        window.location.href = "index.php"    ;
        // conn.send(data);
      },
      error: function(data) {

      }
    });
  })
  function bann(e,_this){
    e.preventDefault();
    console.log($(_this).val() );
    if($(_this).val() == "Bann"){
      $(_this).val("Banned").css({"background-color":"#ffc107",'color':'#000'});
      $.ajax({
          type: 'post',
          url: 'join_cancel.php',
          data: {
            adminid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
            userid: $(_this).parents(".setting").eq(0).find('#setting-userid').val(),
            roomid: $(_this).parents(".setting").eq(0).find('#setting-roomid').val(),
            bann: "bann"
          },
          success: function(data) {

            console.log(data);
            conn.send(data);

          },
          error: function(data) {

          }
        });
      return;
    }
    if($(_this).val() == "Banned"){

      $(_this).val("Bann").css({"background-color":"transparent",'color':'#ffc107'});

      $.ajax({
          type: 'post',
          url: 'join_cancel.php',
          data: {
            adminid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
            userid: $(_this).parents(".setting").eq(0).find('#setting-userid').val(),
            roomid: $(_this).parents(".setting").eq(0).find('#setting-roomid').val(),
            banned: "banned"
          },
          success: function(data) {

            console.log(data);
            conn.send(data);

          },
          error: function(data) {

          }
        });
        return;
    }

  }
  function remove(e,_this){
    e.preventDefault();

      $.ajax({
          type: 'post',
          url: 'join_cancel.php',
          data: {
            adminid: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
            userid: $(_this).parents(".setting").eq(0).find('#setting-userid').val(),
            roomid: $(_this).parents(".setting").eq(0).find('#setting-roomid').val(),
            remove: "remove"
          },
          success: function(data) {
          $(_this).parent().parent().parent().remove();
            // console.log(data);
            conn.send(data);

          },
          error: function(data) {

          }
        });
  }


  $(document).ready(function() {
    $("#message").emojioneArea({
      search: false,
      pickerPosition: "left",
      searchPlaceholder: "Search",
      buttonTitle: "Use your TAB key to insert emoji faster",
      searchPosition: "top",
      pickerPosition: "top",
      events: {
        keyup: function(editor, event) {
          arabicDetect(editor);
          enterkey(editor, event);
          scrolldown(editor, event);
          isTyping(editor);
        },
        focus: function(editor, event) {
          focusReaded();
          focusSeen();
        }
      }
    });
  });

$('.Room-Search').on('keyup',function(){
  $.ajax({
          type: 'post',
          url: 'join_cancel.php',
          data: {
            val : $(this).val(),
            user_id: <?=isset($_GET["id"]) ? $_GET["id"] : "null"?>,
            search_rooms: "search_rooms"
          },
          success: function(data) {
            data = JSON.parse(data);
            console.log(data);
            
            $('.sql_search').html('');
            for (let i = 0; i < data.length; i++) {
              $('.sql_search').append(`<div class="row py-4 px-1 room-chat align-items-start">
        <a class="col-9 " style="padding-right: 0;position:relative" href='?id=<?= isset( $_GET['id']) ? $_GET['id'] : 'null' ?>&room_id=${data[i][0]}'>

          <img src=" ${data[i][2] == '' ? './imgs/pami_room.png' : './imgs/'.data[i][2]}" style="vertical-align: sub;" class="m-0 pr-0 room-img ">

          <span class="room-name" style="position:relative ;" class="mx-md-1">${data[i][1]}
          </span>

        </a>

      <hr class="w-75 my-0" style="margin-left:auto">`);
            }
     

          },
          error: function(data) {

          }
        });

})


  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {

        if (input.files[0].type == "image/jpeg" || input.files[0].type == "image/jpg" || input.files[0].type == "image/png") {
          $(".preview").html("");
          $(".preview").append(`<img id="blah" src="#" alt="your image" />`)
          $('#blah')
            .attr('src', e.target.result)
            .width("100%")
            .height("100%");
        } else {
          $(".preview").html("");
          $(".preview").append(input.files[0].name)
        }
      };

      reader.readAsDataURL(input.files[0]);
      $('#image_preview').modal('show');
    }
  }

  function open_photo(img) {


    $(".preview_photo").html("");
    $(".preview_photo").append(`<img id="blah2" src="#" alt="your image" />`)
    $('#blah2')
      .attr('src', $(img).attr("src"))
      .width("100%")
      .height("100%");

    $('#open_photo').modal('show');
  }




  $('.sidebar, .profile').css({
    'height': `calc(100vh - ${$('.navbar').innerHeight()}px)`
  });
  $('.chat-area').css({
    'height': `calc(85vh - ${$('.navbar').innerHeight()}px)`
  });
  // $('.sidebar').on('resize',function(){
  //     $(this).css({
  //     'height': `calc(100vh - ${$('.navbar').innerHeight()}px)`
  //   });
  // });
  // $('.chat-area').on('resize',function(){
  //     $(this).css({
  //       'height': `calc(85vh - ${$('.navbar').innerHeight()}px)`
  //   });
  // });
  // $('.profile').on('resize',function(){
  //     $(this).css({
  //     'height': `calc(100vh - ${$('.navbar').innerHeight()}px)`
  //   });
  // });


  <?php if (!empty($errors)) {?>
    $('#exampleModal').modal('show');
  <?php }?>
  <?php ob_end_flush();?>
</script>

</html>