import React from 'react';
import { Link } from 'react-router-dom';
import { 
  Facebook, Twitter, Instagram, Youtube, 
  Mail, Phone, MapPin, CreditCard, Truck, Shield, Clock 
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const footerLinks = {
  shop: [
    { label: 'All Products', href: '/shop' },
    { label: 'Categories', href: '/categories' },
    { label: 'Deals & Offers', href: '/deals' },
    { label: 'New Arrivals', href: '/shop?sort=newest' },
    { label: 'Best Sellers', href: '/shop?sort=popular' },
  ],
  services: [
    { label: 'Book a Service', href: '/services' },
    { label: 'Service Pricing', href: '/services#pricing' },
    { label: 'Service Centers', href: '/locations' },
    { label: 'Warranty Info', href: '/warranty' },
  ],
  support: [
    { label: 'Contact Us', href: '/contact' },
    { label: 'FAQs', href: '/faq' },
    { label: 'Shipping Info', href: '/shipping' },
    { label: 'Returns & Refunds', href: '/returns' },
    { label: 'Track Order', href: '/track-order' },
  ],
  company: [
    { label: 'About Us', href: '/about' },
    { label: 'Careers', href: '/careers' },
    { label: 'Blog', href: '/blog' },
    { label: 'Privacy Policy', href: '/privacy' },
    { label: 'Terms of Service', href: '/terms' },
  ],
};

const features = [
  { icon: Truck, label: 'Free Shipping', description: 'On orders over $50' },
  { icon: Shield, label: 'Secure Payment', description: '100% protected' },
  { icon: Clock, label: '24/7 Support', description: 'Always here to help' },
  { icon: CreditCard, label: 'Easy Returns', description: '30-day policy' },
];

export function Footer() {
  return (
    <footer className="bg-sidebar text-sidebar-foreground">
      {/* Features Bar */}
      <div className="border-b border-sidebar-border">
        <div className="container mx-auto px-4 py-6">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {features.map((feature) => (
              <div key={feature.label} className="flex items-center gap-3">
                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sidebar-accent">
                  <feature.icon className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="font-medium text-sm">{feature.label}</p>
                  <p className="text-xs text-sidebar-foreground/60">{feature.description}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Main Footer */}
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-8">
          {/* Brand */}
          <div className="lg:col-span-2">
            <Link to="/" className="flex items-center gap-2 mb-4">
              <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground font-bold text-lg">
                AS
              </div>
              <div>
                <span className="font-bold text-lg">Auto Spare</span>
                <span className="text-xs text-sidebar-foreground/60 block -mt-1">Workshop</span>
              </div>
            </Link>
            <p className="text-sm text-sidebar-foreground/70 mb-4 max-w-xs">
              Your one-stop destination for quality auto parts and professional workshop services. 
              Trusted by thousands of car owners.
            </p>

            {/* Newsletter */}
            <div className="mb-6">
              <p className="font-medium text-sm mb-2">Subscribe to our newsletter</p>
              <div className="flex gap-2">
                <Input
                  type="email"
                  placeholder="Enter your email"
                  variant="dark"
                  className="flex-1"
                />
                <Button>Subscribe</Button>
              </div>
            </div>

            {/* Social Links */}
            <div className="flex gap-2">
              {[Facebook, Twitter, Instagram, Youtube].map((Icon, i) => (
                <Button key={i} variant="ghost" size="icon" className="hover:bg-sidebar-accent">
                  <Icon className="h-5 w-5" />
                </Button>
              ))}
            </div>
          </div>

          {/* Links */}
          <div>
            <h4 className="font-semibold mb-4">Shop</h4>
            <ul className="space-y-2">
              {footerLinks.shop.map((link) => (
                <li key={link.href}>
                  <Link 
                    to={link.href} 
                    className="text-sm text-sidebar-foreground/70 hover:text-primary transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold mb-4">Services</h4>
            <ul className="space-y-2">
              {footerLinks.services.map((link) => (
                <li key={link.href}>
                  <Link 
                    to={link.href} 
                    className="text-sm text-sidebar-foreground/70 hover:text-primary transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold mb-4">Support</h4>
            <ul className="space-y-2">
              {footerLinks.support.map((link) => (
                <li key={link.href}>
                  <Link 
                    to={link.href} 
                    className="text-sm text-sidebar-foreground/70 hover:text-primary transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold mb-4">Contact</h4>
            <ul className="space-y-3">
              <li className="flex items-start gap-2 text-sm text-sidebar-foreground/70">
                <MapPin className="h-4 w-4 mt-0.5 shrink-0 text-primary" />
                <span>123 Auto Lane, Motor City, MC 12345</span>
              </li>
              <li className="flex items-center gap-2 text-sm text-sidebar-foreground/70">
                <Phone className="h-4 w-4 shrink-0 text-primary" />
                <span>+1 (555) 123-4567</span>
              </li>
              <li className="flex items-center gap-2 text-sm text-sidebar-foreground/70">
                <Mail className="h-4 w-4 shrink-0 text-primary" />
                <span>support@autospare.com</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom Bar */}
      <div className="border-t border-sidebar-border">
        <div className="container mx-auto px-4 py-4">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p className="text-sm text-sidebar-foreground/60">
              © 2024 Auto Spare Workshop. All rights reserved.
            </p>
            <div className="flex items-center gap-4">
              <img src="/placeholder.svg" alt="Visa" className="h-6 opacity-60" />
              <img src="/placeholder.svg" alt="Mastercard" className="h-6 opacity-60" />
              <img src="/placeholder.svg" alt="PayPal" className="h-6 opacity-60" />
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
