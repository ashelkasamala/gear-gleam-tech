import React from 'react';
import { Link } from 'react-router-dom';
import { 
  ArrowRight, Wrench, Shield, Truck, Star, ChevronRight,
  Car, Cog, Battery, Filter, Disc, Zap
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { mockProducts, mockCategories } from '@/data/mockData';

const categoryIcons: Record<string, React.ComponentType<any>> = {
  'Engine Parts': Cog,
  'Brake System': Disc,
  'Electrical': Zap,
  'Filters': Filter,
  'Suspension': Car,
  'Transmission': Cog,
  'Body Parts': Car,
  'Accessories': Battery,
};

const features = [
  {
    icon: Wrench,
    title: 'Expert Service',
    description: 'Professional mechanics with years of experience',
  },
  {
    icon: Shield,
    title: 'Quality Guaranteed',
    description: 'All parts come with warranty protection',
  },
  {
    icon: Truck,
    title: 'Fast Delivery',
    description: 'Free shipping on orders over $50',
  },
];

export default function Index() {
  const featuredProducts = mockProducts.slice(0, 4);
  const popularCategories = mockCategories.slice(0, 6);

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-sidebar via-sidebar to-sidebar/90 text-sidebar-foreground">
        <div className="absolute inset-0 bg-[url('/placeholder.svg')] opacity-5" />
        <div className="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-primary/10 to-transparent" />
        
        <div className="container mx-auto px-4 py-20 md:py-28 relative z-10">
          <div className="max-w-2xl">
            <Badge variant="secondary" className="mb-4 animate-fade-in">
              🔧 Premium Auto Parts & Services
            </Badge>
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 animate-fade-in animation-delay-100">
              Quality Parts for
              <span className="text-primary block mt-2">Every Vehicle</span>
            </h1>
            <p className="text-lg md:text-xl text-sidebar-foreground/70 mb-8 animate-fade-in animation-delay-200">
              Your one-stop destination for premium auto parts and professional workshop services. 
              Trusted by thousands of car owners nationwide.
            </p>
            <div className="flex flex-wrap gap-4 animate-fade-in animation-delay-300">
              <Button size="lg" variant="gradient" asChild>
                <Link to="/shop">
                  Shop Now <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </Button>
              <Button size="lg" variant="outline" className="border-sidebar-foreground/30 text-sidebar-foreground hover:bg-sidebar-accent" asChild>
                <Link to="/services">
                  Book Service
                </Link>
              </Button>
            </div>

            {/* Stats */}
            <div className="flex flex-wrap gap-8 mt-12 pt-8 border-t border-sidebar-border/50 animate-fade-in animation-delay-400">
              {[
                { value: '50K+', label: 'Products' },
                { value: '15K+', label: 'Happy Customers' },
                { value: '99%', label: 'Satisfaction' },
                { value: '24/7', label: 'Support' },
              ].map((stat) => (
                <div key={stat.label}>
                  <div className="text-2xl md:text-3xl font-bold text-primary">{stat.value}</div>
                  <div className="text-sm text-sidebar-foreground/60">{stat.label}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Features Bar */}
      <section className="py-8 bg-card border-b">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {features.map((feature) => (
              <div key={feature.title} className="flex items-center gap-4 p-4 rounded-xl hover:bg-secondary/50 transition-colors">
                <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10">
                  <feature.icon className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-semibold">{feature.title}</h3>
                  <p className="text-sm text-muted-foreground">{feature.description}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Categories */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between mb-8">
            <div>
              <h2 className="text-2xl md:text-3xl font-bold">Shop by Category</h2>
              <p className="text-muted-foreground mt-1">Find parts for every system in your vehicle</p>
            </div>
            <Button variant="ghost" asChild>
              <Link to="/categories">
                View All <ChevronRight className="ml-1 h-4 w-4" />
              </Link>
            </Button>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            {popularCategories.map((category, index) => {
              const IconComponent = categoryIcons[category.name] || Cog;
              return (
                <Link
                  key={category.id}
                  to={`/shop?category=${category.slug}`}
                  className="group animate-fade-in"
                  style={{ animationDelay: `${index * 50}ms` }}
                >
                  <Card variant="interactive" className="h-full">
                    <CardContent className="p-6 text-center">
                      <div className="flex h-14 w-14 mx-auto items-center justify-center rounded-xl bg-primary/10 group-hover:bg-primary/20 transition-colors mb-4">
                        <IconComponent className="h-7 w-7 text-primary" />
                      </div>
                      <h3 className="font-semibold text-sm mb-1">{category.name}</h3>
                      <p className="text-xs text-muted-foreground">{category.productCount} products</p>
                    </CardContent>
                  </Card>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-16 bg-secondary/30">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between mb-8">
            <div>
              <h2 className="text-2xl md:text-3xl font-bold">Featured Products</h2>
              <p className="text-muted-foreground mt-1">Top-rated parts from trusted brands</p>
            </div>
            <Button variant="ghost" asChild>
              <Link to="/shop">
                View All <ChevronRight className="ml-1 h-4 w-4" />
              </Link>
            </Button>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {featuredProducts.map((product, index) => (
              <Link
                key={product.id}
                to={`/product/${product.id}`}
                className="group animate-fade-in"
                style={{ animationDelay: `${index * 100}ms` }}
              >
                <Card variant="interactive" className="h-full overflow-hidden">
                  <div className="relative aspect-square bg-secondary">
                    <img
                      src={product.images[0] || '/placeholder.svg'}
                      alt={product.name}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    {product.salePrice && (
                      <Badge variant="destructive" className="absolute top-3 left-3">
                        Sale
                      </Badge>
                    )}
                    {product.stock === 0 && (
                      <Badge variant="secondary" className="absolute top-3 right-3">
                        Out of Stock
                      </Badge>
                    )}
                    {product.stock > 0 && product.stock <= 10 && (
                      <Badge variant="warning" className="absolute top-3 right-3">
                        Low Stock
                      </Badge>
                    )}
                  </div>
                  <CardContent className="p-4">
                    <p className="text-xs text-muted-foreground mb-1">{product.brand}</p>
                    <h3 className="font-semibold text-sm line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                      {product.name}
                    </h3>
                    <div className="flex items-center gap-1 mb-3">
                      <div className="flex items-center">
                        {[...Array(5)].map((_, i) => (
                          <Star
                            key={i}
                            className={`h-3.5 w-3.5 ${
                              i < Math.floor(product.rating)
                                ? 'text-warning fill-warning'
                                : 'text-muted-foreground/30'
                            }`}
                          />
                        ))}
                      </div>
                      <span className="text-xs text-muted-foreground">
                        ({product.reviewCount})
                      </span>
                    </div>
                    <div className="flex items-center gap-2">
                      {product.salePrice ? (
                        <>
                          <span className="font-bold text-lg text-primary">
                            ${product.salePrice.toFixed(2)}
                          </span>
                          <span className="text-sm text-muted-foreground line-through">
                            ${product.price.toFixed(2)}
                          </span>
                        </>
                      ) : (
                        <span className="font-bold text-lg">
                          ${product.price.toFixed(2)}
                        </span>
                      )}
                    </div>
                  </CardContent>
                </Card>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-gradient-to-r from-primary to-orange-500 text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Need Professional Service?
          </h2>
          <p className="text-lg text-primary-foreground/80 mb-8 max-w-2xl mx-auto">
            Book an appointment with our certified mechanics. Quality service, fair prices, 
            and your satisfaction guaranteed.
          </p>
          <div className="flex flex-wrap justify-center gap-4">
            <Button size="lg" variant="secondary" asChild>
              <Link to="/services">
                Book Service <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
            </Button>
            <Button size="lg" variant="outline" className="border-primary-foreground/30 text-primary-foreground hover:bg-primary-foreground/10" asChild>
              <Link to="/contact">
                Contact Us
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* Brands Section */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <h2 className="text-center text-xl font-semibold text-muted-foreground mb-8">
            Trusted Brands We Carry
          </h2>
          <div className="flex flex-wrap justify-center items-center gap-8 md:gap-16">
            {['Bosch', 'NGK', 'Brembo', 'Monroe', 'K&N', 'Mobil 1'].map((brand) => (
              <div 
                key={brand} 
                className="text-2xl font-bold text-muted-foreground/40 hover:text-muted-foreground transition-colors"
              >
                {brand}
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
