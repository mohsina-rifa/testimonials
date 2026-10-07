export type DailyPoint = { date: string; count: number };

export default function DailyChart({ data }: { data: DailyPoint[] }) {
    const max = Math.max(1, ...data.map((point) => point.count));

    return (
        <div>
            <div
                className="flex h-48 items-end gap-px"
                role="img"
                aria-label="Testimonials collected per day"
            >
                {data.map((point) => (
                    <div
                        key={point.date}
                        title={`${point.date}: ${point.count}`}
                        className="flex-1 rounded-t bg-primary/80 hover:bg-primary"
                        style={{
                            height: `${(point.count / max) * 100}%`,
                            minHeight: point.count > 0 ? 2 : 1,
                            opacity: point.count > 0 ? 1 : 0.25,
                        }}
                    />
                ))}
            </div>
            <div className="mt-2 flex justify-between text-xs text-muted-foreground">
                <span>{data[0]?.date}</span>
                <span>{data[data.length - 1]?.date}</span>
            </div>
        </div>
    );
}
