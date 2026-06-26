<?php
include '../config.php'; 
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Layout Chat */
        .chat-layout { display: flex; height: 600px; background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        
        /* Sidebar */
        .user-list-sidebar { width: 300px; background: #f8f9fa; border-right: 1px solid #eee; display: flex; flex-direction: column; }
        .sidebar-header { padding: 15px; background: #fff; border-bottom: 1px solid #eee; font-weight: bold; color: #333; }
        .user-list { flex: 1; overflow-y: auto; }
        
        /* User Item */
        .user-list-item { 
            display: flex; align-items: center; padding: 15px; cursor: pointer; border-bottom: 1px solid #f1f1f1; 
            position: relative; /* Penting untuk badge absolute */
        }
        .user-list-item:hover { background: #fff; }
        .user-list-item.active { background: #e3f2fd; border-left: 4px solid var(--primary); }
        .user-avatar { width: 40px; height: 40px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; color: #666; }
        .user-info { flex: 1; }
        .user-info .u-name { font-weight: 600; font-size: 14px; color: #333; }
        .user-info .u-status { font-size: 11px; color: #888; }

        /* NOTIFIKASI BUBBLE HIJAU (PERBAIKAN CSS) */
        .unread-badge {
            position: absolute; /* Mengambang */
            right: 15px; /* Jarak dari kanan */
            top: 50%;
            transform: translateY(-50%);
            background-color: #27ae60; /* HIJAU MENYALA */
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: pulse 1s infinite;
        }
        @keyframes pulse { 0% { transform: translateY(-50%) scale(1); } 50% { transform: translateY(-50%) scale(1.1); } 100% { transform: translateY(-50%) scale(1); } }

        /* Chat Area */
        .chat-area { flex: 1; display: flex; flex-direction: column; background: #fff; }
        .chat-area-header { padding: 15px; border-bottom: 1px solid #eee; font-weight: bold; background: #fff; color: var(--primary); }
        .chat-messages { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 10px; }
        .chat-input-wrapper { padding: 15px; background: #fff; border-top: 1px solid #eee; display: flex; gap: 10px; }
        .chat-input-wrapper input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 20px; outline: none; }
        .chat-input-wrapper button { background: var(--primary); color: #fff; border: none; padding: 0 20px; border-radius: 20px; cursor: pointer; font-weight: 600; }
        
        /* Bubble Chat */
        .message { display: flex; width: 100%; margin-bottom: 5px; }
        .chat-me { justify-content: flex-end; }
        .chat-other { justify-content: flex-start; }
        .msg-bubble { max-width: 70%; padding: 12px 16px; border-radius: 15px; font-size: 13px; line-height: 1.4; }
        .chat-me .msg-bubble { background: var(--primary); color: #fff; border-bottom-right-radius: 2px; }
        .chat-other .msg-bubble { background: #e9ecef; color: #333; border-bottom-left-radius: 2px; }
        .empty-state { display: flex; justify-content: center; align-items: center; height: 100%; color: #999; flex-direction: column; }
        .empty-state i { font-size: 50px; margin-bottom: 10px; opacity: 0.3; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-header"><div>Admin Chat</div></div>
        
        <div class="container">
            <h2 class="page-title">Live Chat Center</h2>
            
            <div class="chat-layout">
                <div class="user-list-sidebar">
                    <div class="sidebar-header">Recent Chats</div>
                    <div class="user-list" id="userListContainer">
                        <div style="padding:20px; text-align:center; color:#999;">Loading...</div>
                    </div>
                </div>

                <div class="chat-area">
                    <div class="chat-area-header" id="chatHeaderName">
                        Select a user to start chatting
                    </div>

                    <div class="chat-messages" id="adminChatDisplay">
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <div>Select a conversation from the left</div>
                        </div>
                    </div>
                    
                    <div class="chat-input-wrapper">
                        <input type="text" id="adminMsg" placeholder="Type a reply..." disabled>
                        <button id="sendBtn" onclick="adminReply()" disabled>Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var currentUserId = null;
        var chatInterval = null;

        // 1. Load Daftar User (Termasuk Notif Badge)
        function loadUserList() {
            $.post("../chat_endpoint.php", { action: 'fetch_chat_users' }, function(data) {
                var scrollPos = $(".user-list").scrollTop(); // Simpan posisi scroll
                $("#userListContainer").html(data);
                
                if(currentUserId) {
                    $(`#user_${currentUserId}`).addClass('active');
                    // Jika user sedang dibuka, sembunyikan notifnya secara visual
                    $(`#user_${currentUserId} .unread-badge`).hide();
                }
                $(".user-list").scrollTop(scrollPos); // Kembalikan posisi scroll
            });
        }

        // 2. Saat Admin Klik Salah Satu User
        function selectUser(uid, name) {
            currentUserId = uid;
            
            $(".user-list-item").removeClass("active");
            $(`#user_${uid}`).addClass("active");
            $("#chatHeaderName").html(`<i class="fas fa-user-circle"></i> ${name}`);
            $("#adminMsg").prop("disabled", false).focus();
            $("#sendBtn").prop("disabled", false);
            
            // HILANGKAN BADGE
            $(`#user_${uid} .unread-badge`).fadeOut();

            // UPDATE DB JADI READ
            $.post("../chat_endpoint.php", { action: 'mark_read', user_id: uid });

            // Load Chat & Set Interval
            loadChatHistory();
            if(chatInterval) clearInterval(chatInterval);
            chatInterval = setInterval(loadChatHistory, 3000);
        }

        function loadChatHistory() {
            if(!currentUserId) return;
            $.post("../chat_endpoint.php", { 
                action: 'fetch_messages', 
                user_id: currentUserId,
                is_admin_view: true 
            }, function(data) {
                var chatDiv = document.getElementById("adminChatDisplay");
                var isAtBottom = (chatDiv.scrollHeight - chatDiv.scrollTop <= chatDiv.clientHeight + 50);
                $("#adminChatDisplay").html(data);
                if(isAtBottom) chatDiv.scrollTop = chatDiv.scrollHeight;
            });
        }

        function adminReply() {
            var msg = $("#adminMsg").val();
            if(msg.trim() == "" || !currentUserId) return;
            $.post("../chat_endpoint.php", {
                action: 'reply_message',
                user_id: currentUserId,
                message: msg
            }, function() {
                $("#adminMsg").val("");
                loadChatHistory();
            });
        }

        $("#adminMsg").keypress(function(e) { if(e.which == 13) adminReply(); });

        // Loop Refresh List User (Cek pesan baru tiap 3 detik)
        loadUserList();
        setInterval(loadUserList, 3000); 
    </script>
</body>
</html>