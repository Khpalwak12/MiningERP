export default function StatCard({ title, value, subtitle, color = 'indigo' }) {
    const colors = {
        indigo: 'bg-indigo-50 border-indigo-200 text-indigo-700',
        green: 'bg-green-50 border-green-200 text-green-700',
        red: 'bg-red-50 border-red-200 text-red-700',
        amber: 'bg-amber-50 border-amber-200 text-amber-700',
        blue: 'bg-blue-50 border-blue-200 text-blue-700',
    };

    return (
        <div className={`rounded-lg border p-4 ${colors[color] || colors.indigo}`}>
            <p className="text-sm font-medium opacity-80">{title}</p>
            <p className="mt-1 text-2xl font-bold">{value}</p>
            {subtitle && <p className="mt-1 text-xs opacity-70">{subtitle}</p>}
        </div>
    );
}
