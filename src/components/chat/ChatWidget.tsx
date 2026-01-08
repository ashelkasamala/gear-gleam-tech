import React, { useState, useRef, useEffect } from 'react';
import { MessageCircle, X, Send, Minimize2, Maximize2, Paperclip, Image as ImageIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useChat } from '@/contexts/ChatContext';
import { useAuth } from '@/contexts/AuthContext';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';

export function ChatWidget() {
  const { 
    isOpen, 
    toggleChat, 
    closeChat,
    messages, 
    activeConversation,
    conversations,
    sendMessage, 
    setActiveConversation,
    createConversation,
    unreadCount,
    isLoading
  } = useChat();
  const { user, isAdmin } = useAuth();
  const [inputValue, setInputValue] = useState('');
  const [isMinimized, setIsMinimized] = useState(false);
  const scrollRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  // Scroll to bottom when new messages arrive
  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }
  }, [messages]);

  // Focus input when chat opens
  useEffect(() => {
    if (isOpen && !isMinimized && inputRef.current) {
      inputRef.current.focus();
    }
  }, [isOpen, isMinimized, activeConversation]);

  const handleSend = () => {
    if (!inputValue.trim()) return;
    sendMessage(inputValue);
    setInputValue('');
  };

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSend();
    }
  };

  const handleStartChat = () => {
    if (conversations.length > 0) {
      setActiveConversation(conversations[0]);
    } else {
      createConversation();
    }
  };

  if (!isOpen) {
    return (
      <Button
        onClick={toggleChat}
        className="fixed bottom-6 right-6 h-14 w-14 rounded-full shadow-glow-lg z-50 animate-pulse-glow"
        size="icon"
      >
        <MessageCircle className="h-6 w-6" />
        {unreadCount > 0 && (
          <span className="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-destructive text-xs font-medium text-destructive-foreground flex items-center justify-center">
            {unreadCount}
          </span>
        )}
      </Button>
    );
  }

  return (
    <div 
      className={cn(
        "fixed bottom-6 right-6 z-50 flex flex-col bg-card border rounded-2xl shadow-xl transition-all duration-300",
        isMinimized ? "w-80 h-14" : "w-96 h-[32rem]"
      )}
    >
      {/* Header */}
      <div className="flex items-center justify-between p-4 border-b bg-primary text-primary-foreground rounded-t-2xl">
        <div className="flex items-center gap-3">
          <div className="relative">
            <div className="h-10 w-10 rounded-full bg-primary-foreground/20 flex items-center justify-center">
              <MessageCircle className="h-5 w-5" />
            </div>
            <span className="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-success border-2 border-primary" />
          </div>
          <div>
            <h3 className="font-semibold text-sm">
              {isAdmin ? 'Support Chat' : 'Need Help?'}
            </h3>
            <p className="text-xs text-primary-foreground/70">
              {isAdmin ? `${conversations.length} active chats` : 'We typically reply in minutes'}
            </p>
          </div>
        </div>
        <div className="flex items-center gap-1">
          <Button 
            variant="ghost" 
            size="icon-sm" 
            className="text-primary-foreground hover:bg-primary-foreground/20"
            onClick={() => setIsMinimized(!isMinimized)}
          >
            {isMinimized ? <Maximize2 className="h-4 w-4" /> : <Minimize2 className="h-4 w-4" />}
          </Button>
          <Button 
            variant="ghost" 
            size="icon-sm" 
            className="text-primary-foreground hover:bg-primary-foreground/20"
            onClick={closeChat}
          >
            <X className="h-4 w-4" />
          </Button>
        </div>
      </div>

      {!isMinimized && (
        <>
          {/* Conversation List or Messages */}
          {!activeConversation ? (
            <div className="flex-1 flex flex-col items-center justify-center p-6 text-center">
              <div className="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                <MessageCircle className="h-8 w-8 text-primary" />
              </div>
              <h4 className="font-semibold mb-2">Start a Conversation</h4>
              <p className="text-sm text-muted-foreground mb-4">
                {isAdmin 
                  ? 'Select a conversation from the list or wait for new messages.'
                  : 'Our support team is ready to help you with any questions.'}
              </p>
              
              {isAdmin && conversations.length > 0 ? (
                <div className="w-full space-y-2 max-h-48 overflow-y-auto">
                  {conversations.map((conv) => (
                    <button
                      key={conv.id}
                      onClick={() => setActiveConversation(conv)}
                      className="w-full p-3 rounded-lg bg-secondary hover:bg-secondary/80 text-left transition-colors"
                    >
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                          <img 
                            src={conv.participants.find(p => p.role === 'user')?.user.avatar} 
                            alt="" 
                            className="h-8 w-8 rounded-full"
                          />
                          <div>
                            <p className="font-medium text-sm">
                              {conv.participants.find(p => p.role === 'user')?.user.name}
                            </p>
                            <p className="text-xs text-muted-foreground truncate max-w-[180px]">
                              {conv.lastMessage?.content}
                            </p>
                          </div>
                        </div>
                        {conv.unreadCount > 0 && (
                          <span className="h-5 w-5 rounded-full bg-primary text-xs text-primary-foreground flex items-center justify-center">
                            {conv.unreadCount}
                          </span>
                        )}
                      </div>
                    </button>
                  ))}
                </div>
              ) : (
                <Button onClick={handleStartChat}>
                  Start Chat
                </Button>
              )}
            </div>
          ) : (
            <>
              {/* Messages */}
              <ScrollArea className="flex-1 p-4" ref={scrollRef}>
                {isLoading ? (
                  <div className="flex items-center justify-center h-full">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
                  </div>
                ) : (
                  <div className="space-y-4">
                    {messages.map((message, index) => {
                      const isOwnMessage = message.senderId === user?.id;
                      const showAvatar = index === 0 || 
                        messages[index - 1].senderId !== message.senderId;

                      return (
                        <div
                          key={message.id}
                          className={cn(
                            "flex gap-2",
                            isOwnMessage ? "justify-end" : "justify-start"
                          )}
                        >
                          {!isOwnMessage && showAvatar && (
                            <img
                              src={message.sender.avatar || '/placeholder.svg'}
                              alt={message.sender.name}
                              className="h-8 w-8 rounded-full shrink-0"
                            />
                          )}
                          {!isOwnMessage && !showAvatar && (
                            <div className="w-8 shrink-0" />
                          )}
                          <div
                            className={cn(
                              "max-w-[75%] px-4 py-2 text-sm",
                              isOwnMessage 
                                ? "chat-bubble-user" 
                                : "chat-bubble-admin"
                            )}
                          >
                            <p>{message.content}</p>
                            <p className={cn(
                              "text-[10px] mt-1",
                              isOwnMessage 
                                ? "text-primary-foreground/60" 
                                : "text-muted-foreground"
                            )}>
                              {format(message.createdAt, 'h:mm a')}
                            </p>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                )}
              </ScrollArea>

              {/* Input */}
              <div className="p-4 border-t">
                <div className="flex items-center gap-2">
                  <Button variant="ghost" size="icon-sm" className="shrink-0">
                    <Paperclip className="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon-sm" className="shrink-0">
                    <ImageIcon className="h-4 w-4" />
                  </Button>
                  <Input
                    ref={inputRef}
                    value={inputValue}
                    onChange={(e) => setInputValue(e.target.value)}
                    onKeyDown={handleKeyDown}
                    placeholder="Type a message..."
                    className="flex-1"
                  />
                  <Button 
                    size="icon" 
                    onClick={handleSend}
                    disabled={!inputValue.trim()}
                  >
                    <Send className="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </>
          )}
        </>
      )}
    </div>
  );
}
