
let liveUsersMap = {};
let currentChatUserId = null;

(function ($) {
    // Inject chat HTML into body
    const chatHtml = `
        <button id="chatToggleBtn" class="btn btn-primary"><i class="fas fa-comments"></i></button>
            <div id="liveChatPanel">
                <div id="mainChatPanel">
                    <div class="chat-header d-flex align-items-center">
                        <span class="flex-grow-1 text-truncate text-center"><i class="fas fa-comments"></i> Live Chat</span>
                        <button class="closeBtn btn btn-danger py-0"><i class="fas fa-times"></i></button>
                    </div>
                    <input type="text" id="searchLiveChat" placeholder="Search..." class="form-control mb-2">
                    <ul id="liveUserList" class="list-unstyled m-2"></ul>
                </div>
                <div id="chatWindows"></div>
            </div>
    `;

    $('body').append(chatHtml);
})(jQuery);


(function ($) {
    const wsUrl = "wss://" + window.location.host + "/wss";
    const ws = new WebSocket(wsUrl);
    // const openChats = {};

    ws.onmessage = function (event) {
        const msg = JSON.parse(event.data);
        if (msg.type === 'live_users') updateLiveUsers(msg.users);
        if (msg.type === 'chat_message') receiveMessage(msg);
        if (msg.type === 'messages_read') updateReadStatus(msg);
    };

    function updateReadStatus(msg) {
        console.log('Updating read status from user:', msg.fromUserId);

        if (currentChatUserId === msg.fromUserId || (msg.isSelf && currentChatUserId === msg.toUserId)) {
            const win = $('#chatWindows .chat-window');

            // Find all outgoing bubbles that are not marked as read yet
            const bubbles = win.find('.chat-bubble.outgoing').filter(function () {
                return !$(this).data('read-at');
            });

            if (bubbles.length === 0) {
                console.warn('No unread outgoing bubbles found to mark as read.');
                return;
            }

            // Get current read time
            const readAt = msg.readAt || new Date().toISOString();

            bubbles.each(function () {
                const bubble = $(this);
                const sentAt = bubble.data('sent-at');

                // Mark read
                bubble.data('read-at', readAt);

                // Build tooltip title
                const title = `Sent At: ${sentAt ? formatDateTime("datetime", sentAt) : 'N/A'}<br>Read At: ${readAt ? formatDateTime("datetime", readAt) : 'N/A'}`;
                updateTippyOrTitle(bubble.find('.chat-meta'), title);
                // bubble.find('.chat-meta').attr('title', title);

                // Update double-check icon
                bubble.find('.status-icon').html('<i class="fas fa-check-double"></i>');
            });
        }
        else {
            // do nothing here.
        }
    }


    function markMessagesAsRead(userKey, userId) {
        console.log('Marking messages as read for user:', userKey);
        skipPreloader = true;
        apiCall('GET', `/api/internalFront/markMessagesAsRead/${userKey}`).then(response => {
            console.log('Messages marked as read:', response);
            console.log(userId);
            ws.send(JSON.stringify({
                type: "messages_read",
                toUserId: userId, // or encrypted publicId of sender
                readAt: response.readAt,
            }));

        }).catch(err => {
            console.error('Failed to mark messages as read:', err);
        });
    }

    function loadChatHistory(userId, beforeMessageId = null) {
        let url = `/api/internalFront/loadChatHistory/${userId}?limit=30`;
        if (beforeMessageId) {
            url += `&beforeMessageId=${beforeMessageId}`;
        }
        skipPreloader = true;
        return apiCall('GET', url).then(messages => {
            return messages.data || [];
        }).catch(err => {
            console.error('Failed to load chat history:', err);
            return [];
        });
    }

    jQuery("#searchLiveChat").on('keyup', function () {
        const list = $('#liveUserList');
        const val = $(this).val().toLowerCase().trim();
        list.find('li').each(function () {
            const name = $(this).find('span').text().toLowerCase().trim();
            if (name.includes(val)) {
                console.log('Showing:', name);
                $(this).addClass('d-flex').show();
            } else {
                console.log('Hiding:', name);
                $(this).removeClass('d-flex').hide();
            }
        });
    });

    function updateLiveUsers(users) {
        users.forEach(user => {
            if (!liveUsersMap[user.userId]) {
                // Create new entry directly with all fields
                liveUsersMap[user.userId] = {
                    ...user
                };
            } else {
                // Update dynamic fields
                liveUsersMap[user.userId].isOnline = user.isOnline;
                liveUsersMap[user.userId].userPic = user.userPic; // Optional if needed
                liveUsersMap[user.userId].firstName = user.firstName;
                liveUsersMap[user.userId].lastName = user.lastName;
                liveUsersMap[user.userId].lastMessageTime = user.lastMessageTime; // 💡 update or add this line
                liveUsersMap[user.userId].unreadCount = user.unreadCount || 0;
            }
        });

        // After updating map, render fresh
        renderUserList();
    }


    function renderUserList() {
        const list = $('#liveUserList');
        list.empty();

        // Prepare sorted array
        const sortedUsers = Object.values(liveUsersMap).sort((a, b) => {
            const aTime = a.lastMessageTime || '1970-01-01T00:00:00Z';
            const bTime = b.lastMessageTime || '1970-01-01T00:00:00Z';
            return new Date(bTime) - new Date(aTime);
        });

        sortedUsers.forEach(user => {
            const statusClass = user.isOnline ? 'online' : 'offline';
            const unreadBadge = user.unreadCount > 0 ? `<span class="unreadBadge">${user.unreadCount}</span>` : '';
            const li = $(`
                <li class="chat-user d-flex align-items-center mb-2 position-relative" data-userid="${user.userId}" style="cursor: pointer;">
                    <div style="position: relative;">
                        <img src="${user.userPic}" alt="Pic" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <span class="${statusClass}" title="${user.isOnline ? 'Online' : 'Offline'}"></span>
                    </div>
                    <span>${user.firstName} ${user.lastName}</span>
                    ${unreadBadge}
                </li>
            `);

            list.append(li);
        });
    }



    function receiveMessage(msg) {


        const userId = msg.isSelf ? msg.toUserId : msg.fromUserId;
        if (liveUsersMap[userId]) {
            liveUsersMap[userId].lastMessageTime = msg.sentAt || new Date().toISOString();
        }

        // Refresh UI list
        renderUserList();

        if (currentChatUserId === msg.fromUserId || (msg.isSelf && currentChatUserId === msg.toUserId)) {

            const win = $('#chatWindows .chat-window');
            let bubble;
            if (msg.isSelf) {
                bubble = createMessageBubble(msg.messageId, msg.messageText, true, msg.sentAt, msg.isRead);
                const input = win.find('.chat-input');
                input.val('');
                input.focus();
            } else {
                bubble = createMessageBubble(msg.messageId, msg.messageText, false, msg.sentAt, msg.isRead);
                liveUsersMap[userId].unreadCount ? liveUsersMap[userId].unreadCount++ : liveUsersMap[userId].unreadCount = 1;
                renderUserList();
            }

            const body = win.find('.chat-body');
            body.append(bubble);
            body[0].scrollTo({ top: body[0].scrollHeight, behavior: 'smooth' });
            win.markedAsRead = false;
        } else {
            // Optionally: add notification badge on user list if not in this chat
            user = liveUsersMap[msg.fromUserId];
            mtplAlerts.show('info', msg.messageText, `New message from ${user.firstName} ${user.lastName}`);
            liveUsersMap[userId].unreadCount ? liveUsersMap[userId].unreadCount++ : liveUsersMap[userId].unreadCount = 1;
            renderUserList();
        }
    }


    function createMessageBubble(messageId, text, outgoing = true, sentAt = null, isRead = false, readAt = null) {
        const wrapper = $('<div class="chat-bubble-wrapper">').addClass(outgoing ? 'outgoing' : 'incoming');
        const bubble = $('<div class="chat-bubble">').addClass(outgoing ? 'outgoing' : 'incoming');

        // add messageId to bubble for future reference
        bubble.data('message-id', messageId);
        bubble.data('sent-at', sentAt || new Date().toISOString());

        const msgContent = $('<div class="chat-text">').text(text);

        let timeSpan;
        if (outgoing) {
            const title = `Sent At: ${sentAt ? formatDateTime("datetime", sentAt) : 'N/A'}<br>Read at: ${readAt ? formatDateTime("datetime", readAt) : 'N/A'}`;
            timeSpan = $('<div title="' + title + '" class="chat-meta">').text(sentAt ? formatDateTime("time", sentAt) : formatDateTime("time"));
        }
        else {
            timeSpan = $('<div class="chat-meta">').text(sentAt ? formatDateTime("time", sentAt) : formatDateTime("time"));
        }



        if (outgoing) {
            const statusIcon = $('<span class="status-icon">').html(Number(isRead) ? '<i class="fas fa-check-double"></i>' : '<i class="fas fa-check"></i>');
            timeSpan.append(statusIcon);
        }

        bubble.append(msgContent).append(timeSpan);
        wrapper.append(bubble);
        return wrapper;
    }

    $(document).on('click', '.backBtn', function () {
        $("#mainChatPanel").show();
        $("#chatWindows").hide();
        currentChatUserId = null;
        renderUserList();
    });

    function getOrCreateChatWindow(userId) {
        currentChatUserId = userId;

        liveUsersMap[userId].unreadCount = 0;
        renderUserList();

        $("#mainChatPanel").hide();
        $('#chatWindows').empty();

        const user = liveUsersMap[userId];
        const userName = user ? `${user.firstName} ${user.lastName}` : `User ${userId}`;
        const userPic = user ? user.userPic : '';

        const win = $(`
            <div class="chat-window" data-userid="${userId}">
                <div class="chat-header d-flex align-items-center">
                    <button class="backBtn btn text-white py-0"><i class="fas fa-angle-left"></i></button>
                    <img src="${userPic}" alt="Pic" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                    <span class="flex-grow-1 text-truncate">${userName}</span>
                    <button class="closeBtn btn btn-danger py-0"><i class="fas fa-times"></i></button>
                </div>
                <div class="chat-body"></div>
                <div class="chat-input-container">
                    <textarea class="chat-input" rows="1" placeholder="Type your message..."></textarea>
                    <button class="send-button" type="button"><i class="fas fa-play ms-1"></i></button>
                </div>
            </div>
        `).appendTo('#chatWindows');

        $('#chatWindows').show();

        const body = win.find('.chat-body');

        // Setup scroll load
        body.on('scroll', function () {
            if (body.scrollTop() === 0 && !win.loadingHistory) {
                win.loadingHistory = true;
                const oldestMsg = body.find('.chat-bubble').first();
                const beforeId = oldestMsg.data('message-id') || null;

                const loader = $('<div class="loading">Loading...</div>');
                body.prepend(loader);

                const prevScrollHeight = body[0].scrollHeight;

                appendChatHistory(user.userKey, body, beforeId, false).then(() => {
                    loader.remove();
                    const newScrollHeight = body[0].scrollHeight;
                    body.scrollTop(newScrollHeight - prevScrollHeight);
                    win.loadingHistory = false;
                });
            }
        });

        win.find('.send-button').on('click', function () {
            sendMessage();
        });

        win.find('.chat-input').on('keydown', function (e) {
            if (e.key === 'Enter' && !e.altKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        function sendMessage() {
            const input = win.find('.chat-input');
            const txt = input.val().trim();
            if (txt) {
                ws.send(JSON.stringify({ type: 'chat_message', toUserId: userId, messageText: txt }));

                if (liveUsersMap[userId]) {
                    liveUsersMap[userId].lastMessageTime = new Date().toISOString();
                }
                renderUserList();
            }
        }


        if (!win.markedAsRead) {
            markMessagesAsRead(user.userKey, userId);
            win.markedAsRead = true;
        }


        win.markedAsRead = false;

        // Initial load
        appendChatHistory(user.userKey, body, null, true);

        return win;
    }


    function appendChatHistory(userKey, body, beforeMessageId = null, isInitial = false) {
        return loadChatHistory(userKey, beforeMessageId).then(messages => {
            if (!isInitial) {
                messages.forEach(msg => {
                    const bubble = createMessageBubble(msg.messageId, msg.messageText, msg.isOutgoing, msg.sentAt, msg.isRead, msg.readAt);
                    body.prepend(bubble);
                });
            } else {
                messages.reverse().forEach(msg => {
                    const bubble = createMessageBubble(msg.messageId, msg.messageText, msg.isOutgoing, msg.sentAt, msg.isRead, msg.readAt);
                    body.append(bubble);
                });
                body.scrollTop(body[0].scrollHeight);
            }

            addDateHeadings(body);
        });
    }


    $(document).on('click', '.chat-user', function () {
        const uid = $(this).data('userid');
        getOrCreateChatWindow(uid);
    });

    $(document).on('click', '.closeBtn', function () {
        $('#chatToggleBtn').show();
        $('#liveChatPanel').toggleClass('visible');
    });

    $(document).on('click', '#chatToggleBtn', function () {
        $('#liveChatPanel').toggleClass('visible');
        $('#chatToggleBtn').hide();
    });

    function addDateHeadings(container) {
        let lastDate = null;

        // Get all bubbles in order (assume top to bottom)
        const bubbles = container.find('.chat-bubble');

        // remove any existing date headings
        container.find('.date-heading').remove();

        if (!bubbles.length) {
            const heading = $('<div class="date-heading-wrapper"><div class="date-heading text-muted my-2"></div></div>');
            heading.find('.date-heading').text('No messages yet');
            container.append(heading);
        }

        bubbles.each(function () {
            const bubble = $(this);
            const msgDate = new Date(bubble.data('sent-at')).toDateString();

            if (msgDate !== lastDate) {
                const heading = $('<div class="date-heading-wrapper"><div class="date-heading text-muted my-2"></div></div>');
                heading.find('.date-heading').text(formatDateHeading(bubble.data('sent-at')));
                bubble.parent().before(heading);
                lastDate = msgDate;
            }

        });
    }

    function formatDateHeading(dateStr) {
        const msgDate = new Date(dateStr);
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        if (
            msgDate.getDate() === today.getDate() &&
            msgDate.getMonth() === today.getMonth() &&
            msgDate.getFullYear() === today.getFullYear()
        ) {
            return 'Today';
        }

        if (
            msgDate.getDate() === yesterday.getDate() &&
            msgDate.getMonth() === yesterday.getMonth() &&
            msgDate.getFullYear() === yesterday.getFullYear()
        ) {
            return 'Yesterday';
        }

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return msgDate.toLocaleDateString(undefined, options);
    }


})(jQuery);