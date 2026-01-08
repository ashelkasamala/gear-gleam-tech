import * as React from "react";
import { cn } from "@/lib/utils";
import { Search } from "lucide-react";
import { cva, type VariantProps } from "class-variance-authority";

const inputVariants = cva(
  "flex w-full rounded-lg border bg-transparent text-base transition-all duration-200 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
  {
    variants: {
      variant: {
        default: "border-input focus-visible:ring-2 focus-visible:ring-ring focus-visible:border-primary",
        filled: "bg-secondary border-transparent focus-visible:bg-background focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-ring",
        ghost: "border-transparent hover:bg-secondary focus-visible:bg-secondary",
        dark: "bg-sidebar border-sidebar-border text-sidebar-foreground placeholder:text-sidebar-foreground/50 focus-visible:ring-2 focus-visible:ring-sidebar-ring",
      },
      inputSize: {
        default: "h-10 px-4 py-2",
        sm: "h-8 px-3 py-1 text-xs",
        lg: "h-12 px-5 py-3",
        xl: "h-14 px-6 py-4 text-base",
      },
    },
    defaultVariants: {
      variant: "default",
      inputSize: "default",
    },
  }
);

export interface InputProps
  extends Omit<React.ComponentProps<"input">, "size">,
    VariantProps<typeof inputVariants> {}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
  ({ className, type, variant, inputSize, ...props }, ref) => {
    return (
      <input
        type={type}
        className={cn(inputVariants({ variant, inputSize }), className)}
        ref={ref}
        {...props}
      />
    );
  }
);
Input.displayName = "Input";

// Search Input Component
interface SearchInputProps extends InputProps {
  onSearch?: (value: string) => void;
}

const SearchInput = React.forwardRef<HTMLInputElement, SearchInputProps>(
  ({ className, variant, inputSize, onSearch, ...props }, ref) => {
    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
      if (e.key === "Enter" && onSearch) {
        onSearch((e.target as HTMLInputElement).value);
      }
    };

    return (
      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <input
          type="search"
          className={cn(
            inputVariants({ variant, inputSize }),
            "pl-10",
            className
          )}
          ref={ref}
          onKeyDown={handleKeyDown}
          {...props}
        />
      </div>
    );
  }
);
SearchInput.displayName = "SearchInput";

export { Input, SearchInput, inputVariants };
