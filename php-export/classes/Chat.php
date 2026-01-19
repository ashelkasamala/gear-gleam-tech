<?php
/**
 * AUTO SPARE WORKSHOP - Chat Class
 * Handles live chat functionality
 */

class Chat {
    
    /**
     * Get or create conversation for user
     */
    public static function getOrCreateConversation($userId) {
        // Check for existing active conversation
        $conversation = fetchOne(
            "SELECT * FROM chat_conversations WHERE user_id = ? AND status = 'active'",
            [$userId]
        );
        
        if ($conversation) {
            return $conversation;
        }
        
        // Create new conversation
        $conversationId = insert('chat_conversations', [
            'user_id' => $userId,
            'status' => 'active'
        ]);
        
        return fetchOne("SELECT * FROM chat_conversations WHERE id = ?", [$conversationId]);
    }
    
    /**
     * Get all active conversations (admin)
     */
    public static function getActiveConversations() {
        return fetchAll(
            "SELECT c.*, u.name as user_name, u.email as user_email, u.avatar as user_avatar,
                    (SELECT COUNT(*) FROM chat_messages WHERE conversation_id = c.id AND is_read = 0 AND sender_id = c.user_id) as unread_count,
                    (SELECT content FROM chat_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM chat_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_at
             FROM chat_conversations c
             JOIN users u ON c.user_id = u.id
             WHERE c.status = 'active'
             ORDER BY last_message_at DESC NULLS LAST"
        );
    }
    
    /**
     * Get conversation messages
     */
    public static function getMessages($conversationId, $limit = 50) {
        return fetchAll(
            "SELECT m.*, u.name as sender_name, u.avatar as sender_avatar
             FROM chat_messages m
             JOIN users u ON m.sender_id = u.id
             WHERE m.conversation_id = ?
             ORDER BY m.created_at ASC
             LIMIT ?",
            [$conversationId, $limit]
        );
    }
    
    /**
     * Send message
     */
    public static function sendMessage($conversationId, $senderId, $content, $type = 'text', $attachments = null) {
        // Insert message
        $messageId = insert('chat_messages', [
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'message_type' => $type,
            'content' => $content,
            'attachments' => $attachments ? json_encode($attachments) : null,
            'is_read' => false
        ]);
        
        // Update conversation
        update('chat_conversations', [
            'last_message_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $conversationId]);
        
        // Get sender info
        $sender = User::find($senderId);
        
        return [
            'id' => $messageId,
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'sender_name' => $sender->getName(),
            'sender_avatar' => $sender->getAvatar(),
            'content' => $content,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Mark messages as read
     */
    public static function markAsRead($conversationId, $userId) {
        query(
            "UPDATE chat_messages SET is_read = 1 
             WHERE conversation_id = ? AND sender_id != ? AND is_read = 0",
            [$conversationId, $userId]
        );
    }
    
    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId) {
        // For regular users - count unread admin messages
        $conversation = fetchOne(
            "SELECT id FROM chat_conversations WHERE user_id = ? AND status = 'active'",
            [$userId]
        );
        
        if (!$conversation) return 0;
        
        return fetchOne(
            "SELECT COUNT(*) as count FROM chat_messages 
             WHERE conversation_id = ? AND sender_id != ? AND is_read = 0",
            [$conversation['id'], $userId]
        )['count'];
    }
    
    /**
     * Get total unread count for admin
     */
    public static function getAdminUnreadCount() {
        return fetchOne(
            "SELECT COUNT(*) as count FROM chat_messages m
             JOIN chat_conversations c ON m.conversation_id = c.id
             WHERE c.status = 'active' AND m.is_read = 0 
             AND m.sender_id = c.user_id"
        )['count'];
    }
    
    /**
     * Close conversation
     */
    public static function closeConversation($conversationId) {
        update('chat_conversations', ['status' => 'closed'], 'id = :id', ['id' => $conversationId]);
    }
    
    /**
     * Assign admin to conversation
     */
    public static function assignAdmin($conversationId, $adminId) {
        update('chat_conversations', ['admin_id' => $adminId], 'id = :id', ['id' => $conversationId]);
    }
    
    /**
     * Send system message
     */
    public static function sendSystemMessage($conversationId, $content) {
        return insert('chat_messages', [
            'conversation_id' => $conversationId,
            'sender_id' => 1, // System/Admin ID
            'message_type' => 'system',
            'content' => $content,
            'is_read' => false
        ]);
    }
}
?>
