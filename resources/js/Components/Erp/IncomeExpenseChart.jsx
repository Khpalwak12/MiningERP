import useTranslation from '@/hooks/useTranslation';
import { formatNumber } from '@/utils/format';

const WIDTH = 720;
const HEIGHT = 280;
const PADDING = { top: 24, right: 24, bottom: 48, left: 72 };

function buildPoints(data, key, chartWidth, chartHeight, maxValue) {
    if (data.length === 0) {
        return '';
    }

    const denominator = Math.max(data.length - 1, 1);

    return data
        .map((point, index) => {
            const x = PADDING.left + (index / denominator) * chartWidth;
            const y = PADDING.top + chartHeight - ((point[key] / maxValue) * chartHeight);

            return `${x},${y}`;
        })
        .join(' ');
}

function formatAxisValue(value) {
    if (value >= 1_000_000) {
        return `${formatNumber(value / 1_000_000, 1)}M`;
    }

    if (value >= 1_000) {
        return `${formatNumber(value / 1_000, 0)}K`;
    }

    return formatNumber(value, 0);
}

export default function IncomeExpenseChart({ data = [] }) {
    const { t } = useTranslation();
    const chartWidth = WIDTH - PADDING.left - PADDING.right;
    const chartHeight = HEIGHT - PADDING.top - PADDING.bottom;
    const maxValue = Math.max(
        1,
        ...data.flatMap((point) => [point.income, point.expenses]),
    );

    const incomePoints = buildPoints(data, 'income', chartWidth, chartHeight, maxValue);
    const expensePoints = buildPoints(data, 'expenses', chartWidth, chartHeight, maxValue);
    const yTicks = [0, 0.25, 0.5, 0.75, 1].map((ratio) => ({
        value: maxValue * ratio,
        y: PADDING.top + chartHeight - ratio * chartHeight,
    }));

    const labelStep = data.length > 8 ? Math.ceil(data.length / 6) : 1;

    return (
        <div className="rounded-lg bg-white p-4 shadow" dir="ltr">
            <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 className="font-semibold text-gray-900">{t('dashboard.income_expense_chart')}</h2>
                <div className="flex flex-wrap gap-4 text-sm">
                    <span className="inline-flex items-center gap-2 text-gray-700">
                        <span className="inline-block h-0.5 w-6 bg-emerald-500" />
                        {t('dashboard.total_income')}
                    </span>
                    <span className="inline-flex items-center gap-2 text-gray-700">
                        <span className="inline-block h-0.5 w-6 bg-rose-500" />
                        {t('dashboard.total_expenses')}
                    </span>
                </div>
            </div>

            {data.length === 0 ? (
                <p className="py-12 text-center text-sm text-gray-500">{t('messages.no_records')}</p>
            ) : (
                <div className="overflow-x-auto">
                    <svg viewBox={`0 0 ${WIDTH} ${HEIGHT}`} className="min-w-full" role="img" aria-label={t('dashboard.income_expense_chart')}>
                        {yTicks.map((tick) => (
                            <g key={tick.value}>
                                <line
                                    x1={PADDING.left}
                                    x2={WIDTH - PADDING.right}
                                    y1={tick.y}
                                    y2={tick.y}
                                    stroke="#e5e7eb"
                                    strokeDasharray="4 4"
                                />
                                <text x={PADDING.left - 10} y={tick.y + 4} textAnchor="end" className="fill-gray-500 text-[11px]">
                                    {formatAxisValue(tick.value)}
                                </text>
                            </g>
                        ))}

                        <polyline fill="none" stroke="#10b981" strokeWidth="2.5" points={incomePoints} />
                        <polyline fill="none" stroke="#f43f5e" strokeWidth="2.5" points={expensePoints} />

                        {data.map((point, index) => {
                            const denominator = Math.max(data.length - 1, 1);
                            const x = PADDING.left + (index / denominator) * chartWidth;
                            const incomeY = PADDING.top + chartHeight - ((point.income / maxValue) * chartHeight);
                            const expenseY = PADDING.top + chartHeight - ((point.expenses / maxValue) * chartHeight);

                            return (
                                <g key={point.month}>
                                    <circle cx={x} cy={incomeY} r="3.5" fill="#10b981" />
                                    <circle cx={x} cy={expenseY} r="3.5" fill="#f43f5e" />
                                    {(index % labelStep === 0 || index === data.length - 1) && (
                                        <text x={x} y={HEIGHT - 14} textAnchor="middle" className="fill-gray-600 text-[11px]">
                                            {point.month}
                                        </text>
                                    )}
                                </g>
                            );
                        })}
                    </svg>
                </div>
            )}
        </div>
    );
}
