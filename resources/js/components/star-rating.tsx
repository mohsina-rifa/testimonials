import { Star } from 'lucide-react';

export default function StarRating({ value }: { value: number }) {
    return (
        <div className="flex" aria-label={`${value} out of 5 stars`}>
            {[1, 2, 3, 4, 5].map((star) => (
                <Star
                    key={star}
                    className={
                        star <= value
                            ? 'size-4 fill-amber-400 text-amber-400'
                            : 'size-4 text-muted-foreground/40'
                    }
                />
            ))}
        </div>
    );
}
