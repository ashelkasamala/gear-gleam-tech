import React, { createContext, useContext, useState, useCallback, useEffect, ReactNode } from 'react';
import { ChatConversation, ChatMessage, User } from '@/types';
import { useAuth } from './AuthContext';

interface ChatContextType {
  conversations: ChatConversation[];
  activeConversation: ChatConversation | null;
  messages: ChatMessage[];
  unreadCount: number;
  isOpen: boolean;
  isLoading: boolean;
  setActiveConversation: (conversation: ChatConversation | null) => void;
  sendMessage: (content: string, type?: 'text' | 'image' | 'file') => void;
  openChat: () => void;
  closeChat: () => void;
  toggleChat: () => void;
  markAsRead: (conversationId: string) => void;
  createConversation: () => void;
}

const ChatContext = createContext<ChatContextType | undefined>(undefined);

// Mock data for demo
const mockAdmin: User = {
  id: 'admin-1',
  email: 'support@autospare.com',
  name: 'Support Team',
  role: 'admin',
  createdAt: new Date(),
  loyaltyPoints: 0,
  avatar: 'https://api.dicebear.com/7.x/bottts/svg?seed=support',
};

export function ChatProvider({ children }: { children: ReactNode }) {
  const { user, isAdmin } = useAuth();
  const [conversations, setConversations] = useState<ChatConversation[]>([]);
  const [activeConversation, setActiveConversation] = useState<ChatConversation | null>(null);
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [isOpen, setIsOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  // Load mock conversations
  useEffect(() => {
    if (user) {
      // Mock conversations
      const mockConversations: ChatConversation[] = isAdmin ? [
        {
          id: 'conv-1',
          participants: [
            { userId: 'user-1', user: { ...mockAdmin, id: 'user-1', name: 'John Customer', role: 'user' }, role: 'user', joinedAt: new Date() },
            { userId: 'admin-1', user: mockAdmin, role: 'admin', joinedAt: new Date() },
          ],
          unreadCount: 2,
          status: 'active',
          createdAt: new Date(Date.now() - 3600000),
          updatedAt: new Date(),
          lastMessage: {
            id: 'msg-3',
            conversationId: 'conv-1',
            senderId: 'user-1',
            sender: { ...mockAdmin, id: 'user-1', name: 'John Customer', role: 'user' },
            content: 'Do you have brake pads for Toyota Camry 2020?',
            type: 'text',
            isRead: false,
            createdAt: new Date(),
          },
        },
        {
          id: 'conv-2',
          participants: [
            { userId: 'user-2', user: { ...mockAdmin, id: 'user-2', name: 'Sarah Miller', role: 'user' }, role: 'user', joinedAt: new Date() },
            { userId: 'admin-1', user: mockAdmin, role: 'admin', joinedAt: new Date() },
          ],
          unreadCount: 0,
          status: 'active',
          createdAt: new Date(Date.now() - 86400000),
          updatedAt: new Date(Date.now() - 7200000),
          lastMessage: {
            id: 'msg-5',
            conversationId: 'conv-2',
            senderId: 'admin-1',
            sender: mockAdmin,
            content: 'Your order has been shipped! Tracking number: TRK123456',
            type: 'text',
            isRead: true,
            createdAt: new Date(Date.now() - 7200000),
          },
        },
      ] : [
        {
          id: 'conv-user-1',
          participants: [
            { userId: user.id, user, role: 'user', joinedAt: new Date() },
            { userId: 'admin-1', user: mockAdmin, role: 'admin', joinedAt: new Date() },
          ],
          unreadCount: 1,
          status: 'active',
          createdAt: new Date(Date.now() - 3600000),
          updatedAt: new Date(),
          lastMessage: {
            id: 'msg-reply',
            conversationId: 'conv-user-1',
            senderId: 'admin-1',
            sender: mockAdmin,
            content: 'Hello! How can I help you today?',
            type: 'text',
            isRead: false,
            createdAt: new Date(),
          },
        },
      ];
      setConversations(mockConversations);
    }
  }, [user, isAdmin]);

  // Load messages when active conversation changes
  useEffect(() => {
    if (activeConversation) {
      setIsLoading(true);
      // Mock messages
      const mockMessages: ChatMessage[] = [
        {
          id: 'msg-1',
          conversationId: activeConversation.id,
          senderId: 'admin-1',
          sender: mockAdmin,
          content: 'Hello! Welcome to Auto Spare Workshop. How can I help you today?',
          type: 'text',
          isRead: true,
          createdAt: new Date(Date.now() - 3600000),
        },
        {
          id: 'msg-2',
          conversationId: activeConversation.id,
          senderId: user?.id || 'user-1',
          sender: user || { ...mockAdmin, id: 'user-1', name: 'Customer', role: 'user' },
          content: 'Hi! I need help finding the right parts for my car.',
          type: 'text',
          isRead: true,
          createdAt: new Date(Date.now() - 3500000),
        },
        {
          id: 'msg-3',
          conversationId: activeConversation.id,
          senderId: 'admin-1',
          sender: mockAdmin,
          content: 'Of course! What\'s your vehicle make and model? I\'ll help you find compatible parts.',
          type: 'text',
          isRead: true,
          createdAt: new Date(Date.now() - 3400000),
        },
      ];
      
      setTimeout(() => {
        setMessages(mockMessages);
        setIsLoading(false);
      }, 500);
    } else {
      setMessages([]);
    }
  }, [activeConversation, user]);

  const unreadCount = conversations.reduce((sum, conv) => sum + conv.unreadCount, 0);

  const sendMessage = useCallback((content: string, type: 'text' | 'image' | 'file' = 'text') => {
    if (!activeConversation || !user) return;

    const newMessage: ChatMessage = {
      id: `msg-${Date.now()}`,
      conversationId: activeConversation.id,
      senderId: user.id,
      sender: user,
      content,
      type,
      isRead: false,
      createdAt: new Date(),
    };

    setMessages(prev => [...prev, newMessage]);

    // Simulate admin response after 2 seconds
    if (!isAdmin) {
      setTimeout(() => {
        const autoReply: ChatMessage = {
          id: `msg-${Date.now()}-reply`,
          conversationId: activeConversation.id,
          senderId: 'admin-1',
          sender: mockAdmin,
          content: 'Thanks for your message! Our team will get back to you shortly. In the meantime, feel free to browse our catalog.',
          type: 'text',
          isRead: false,
          createdAt: new Date(),
        };
        setMessages(prev => [...prev, autoReply]);
      }, 2000);
    }
  }, [activeConversation, user, isAdmin]);

  const openChat = useCallback(() => setIsOpen(true), []);
  const closeChat = useCallback(() => setIsOpen(false), []);
  const toggleChat = useCallback(() => setIsOpen(prev => !prev), []);

  const markAsRead = useCallback((conversationId: string) => {
    setConversations(prev =>
      prev.map(conv =>
        conv.id === conversationId ? { ...conv, unreadCount: 0 } : conv
      )
    );
  }, []);

  const createConversation = useCallback(() => {
    if (!user) return;

    const newConv: ChatConversation = {
      id: `conv-${Date.now()}`,
      participants: [
        { userId: user.id, user, role: 'user', joinedAt: new Date() },
        { userId: 'admin-1', user: mockAdmin, role: 'admin', joinedAt: new Date() },
      ],
      unreadCount: 0,
      status: 'active',
      createdAt: new Date(),
      updatedAt: new Date(),
    };

    setConversations(prev => [newConv, ...prev]);
    setActiveConversation(newConv);
  }, [user]);

  return (
    <ChatContext.Provider value={{
      conversations,
      activeConversation,
      messages,
      unreadCount,
      isOpen,
      isLoading,
      setActiveConversation,
      sendMessage,
      openChat,
      closeChat,
      toggleChat,
      markAsRead,
      createConversation,
    }}>
      {children}
    </ChatContext.Provider>
  );
}

export function useChat() {
  const context = useContext(ChatContext);
  if (context === undefined) {
    throw new Error('useChat must be used within a ChatProvider');
  }
  return context;
}
