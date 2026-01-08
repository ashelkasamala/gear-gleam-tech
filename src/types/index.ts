// User Types
export interface User {
  id: string;
  email: string;
  name: string;
  avatar?: string;
  role: 'user' | 'admin' | 'moderator';
  phone?: string;
  createdAt: Date;
  loyaltyPoints: number;
}

// Product Types
export interface Product {
  id: string;
  name: string;
  description: string;
  price: number;
  salePrice?: number;
  images: string[];
  category: string;
  brand: string;
  partNumber: string;
  stock: number;
  compatibility: VehicleCompatibility[];
  rating: number;
  reviewCount: number;
  specifications: Record<string, string>;
  createdAt: Date;
  updatedAt: Date;
}

export interface VehicleCompatibility {
  make: string;
  model: string;
  yearFrom: number;
  yearTo: number;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  icon: string;
  parentId?: string;
  children?: Category[];
  productCount: number;
}

// Order Types
export interface Order {
  id: string;
  userId: string;
  items: OrderItem[];
  status: OrderStatus;
  total: number;
  subtotal: number;
  tax: number;
  shipping: number;
  discount: number;
  shippingAddress: Address;
  billingAddress: Address;
  paymentMethod: string;
  paymentStatus: PaymentStatus;
  trackingNumber?: string;
  notes?: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface OrderItem {
  productId: string;
  product: Product;
  quantity: number;
  price: number;
  total: number;
}

export type OrderStatus = 
  | 'pending'
  | 'confirmed'
  | 'processing'
  | 'shipped'
  | 'delivered'
  | 'cancelled'
  | 'refunded';

export type PaymentStatus = 
  | 'pending'
  | 'paid'
  | 'failed'
  | 'refunded';

// Address Types
export interface Address {
  id: string;
  userId: string;
  name: string;
  line1: string;
  line2?: string;
  city: string;
  state: string;
  postalCode: string;
  country: string;
  phone: string;
  isDefault: boolean;
}

// Service Types
export interface ServiceBooking {
  id: string;
  userId: string;
  serviceType: string;
  vehicleId: string;
  date: Date;
  timeSlot: string;
  status: ServiceStatus;
  notes?: string;
  estimatedCost?: number;
  actualCost?: number;
  technicianNotes?: string;
  createdAt: Date;
  updatedAt: Date;
}

export type ServiceStatus = 
  | 'pending'
  | 'confirmed'
  | 'in_progress'
  | 'completed'
  | 'cancelled';

// Vehicle Types
export interface Vehicle {
  id: string;
  userId: string;
  make: string;
  model: string;
  year: number;
  vin?: string;
  licensePlate?: string;
  mileage?: number;
  color?: string;
  isDefault: boolean;
}

// Chat Types
export interface ChatConversation {
  id: string;
  participants: ChatParticipant[];
  lastMessage?: ChatMessage;
  unreadCount: number;
  status: 'active' | 'closed';
  createdAt: Date;
  updatedAt: Date;
}

export interface ChatParticipant {
  userId: string;
  user: User;
  role: 'user' | 'admin';
  joinedAt: Date;
}

export interface ChatMessage {
  id: string;
  conversationId: string;
  senderId: string;
  sender: User;
  content: string;
  type: 'text' | 'image' | 'file' | 'system';
  attachments?: ChatAttachment[];
  isRead: boolean;
  createdAt: Date;
}

export interface ChatAttachment {
  id: string;
  name: string;
  url: string;
  type: string;
  size: number;
}

// Review Types
export interface Review {
  id: string;
  userId: string;
  user: User;
  productId: string;
  rating: number;
  title: string;
  content: string;
  images?: string[];
  isVerifiedPurchase: boolean;
  helpfulCount: number;
  createdAt: Date;
}

// Notification Types
export interface Notification {
  id: string;
  userId: string;
  title: string;
  message: string;
  type: 'order' | 'service' | 'promotion' | 'system' | 'chat';
  isRead: boolean;
  link?: string;
  createdAt: Date;
}

// Analytics Types
export interface SalesData {
  date: string;
  revenue: number;
  orders: number;
  averageOrderValue: number;
}

export interface DashboardStats {
  totalRevenue: number;
  totalOrders: number;
  totalCustomers: number;
  totalProducts: number;
  revenueChange: number;
  ordersChange: number;
  customersChange: number;
  lowStockProducts: number;
}

// Coupon Types
export interface Coupon {
  id: string;
  code: string;
  type: 'percentage' | 'fixed';
  value: number;
  minOrderAmount?: number;
  maxDiscount?: number;
  usageLimit?: number;
  usageCount: number;
  validFrom: Date;
  validTo: Date;
  isActive: boolean;
}

// Wishlist Types
export interface WishlistItem {
  id: string;
  userId: string;
  productId: string;
  product: Product;
  addedAt: Date;
}

// Cart Types
export interface CartItem {
  productId: string;
  product: Product;
  quantity: number;
}
