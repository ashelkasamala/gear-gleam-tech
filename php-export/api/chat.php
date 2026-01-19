<?php
/**
 * AUTO SPARE WORKSHOP - Chat API
 * Handles chat operations via AJAX
 */

require_once '../config/config.php';

header('Content-Type: application/json');

// Require authentication for chat
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$userId = $_SESSION['user_id'];
$isAdminUser = isAdmin();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'start':
        // Start or get existing conversation
        $conversation = Chat::getOrCreateConversation($userId);
        
        echo json_encode([
            'success' => true,
            'conversation' => $conversation,
            'messages' => Chat::getMessages($conversation['id'])
        ]);
        break;
        
    case 'send':
        $conversationId = $input['conversation_id'] ?? null;
        $content = trim($input['message'] ?? '');
        
        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
            exit;
        }
        
        // If no conversation, create one
        if (!$conversationId) {
            $conversation = Chat::getOrCreateConversation($userId);
            $conversationId = $conversation['id'];
        }
        
        $message = Chat::sendMessage($conversationId, $userId, $content);
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'conversation_id' => $conversationId
        ]);
        break;
        
    case 'messages':
        $conversationId = $_GET['conversation_id'] ?? $input['conversation_id'] ?? null;
        
        if (!$conversationId) {
            echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
            exit;
        }
        
        // Mark messages as read
        Chat::markAsRead($conversationId, $userId);
        
        $messages = Chat::getMessages($conversationId);
        
        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
        break;
        
    case 'conversations':
        // Admin only - get all active conversations
        if (!$isAdminUser) {
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit;
        }
        
        $conversations = Chat::getActiveConversations();
        
        echo json_encode([
            'success' => true,
            'conversations' => $conversations
        ]);
        break;
        
    case 'close':
        // Close a conversation
        $conversationId = $input['conversation_id'] ?? null;
        
        if (!$conversationId) {
            echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
            exit;
        }
        
        Chat::closeConversation($conversationId);
        
        echo json_encode(['success' => true, 'message' => 'Conversation closed']);
        break;
        
    case 'unread_count':
        $count = $isAdminUser ? Chat::getAdminUnreadCount() : Chat::getUnreadCount($userId);
        
        echo json_encode([
            'success' => true,
            'unread_count' => $count
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
